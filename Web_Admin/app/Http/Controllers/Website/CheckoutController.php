<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\CouponResource;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\WebSetting;
use App\Models\Product;
use App\Repositories\AddressRepository;
use App\Repositories\CheckoutRepository;
use App\Repositories\CouponRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;




class CheckoutController extends Controller
{

    private $productRepo;
    private $checkoutRepo;
    private $customerRepo;
    private $addressRepo;

    public function __construct(
        ProductRepository $productRepository,
        CheckoutRepository $checkoutRepository,
        CustomerRepository $customerRepository,
        AddressRepository $addressRepository
    ) {
        $this->productRepo = $productRepository;
        $this->checkoutRepo = $checkoutRepository;
        $this->customerRepo = $customerRepository;
        $this->addressRepo = $addressRepository;
    }

    public function cart(Request $request)
    {
        $service_id = $request->service_id;
        $variant_id = $request->variant_id;

        $query = $this->productRepo->model()::query()
            ->with(['service', 'variant'])
            ->whereNull('product_id')
            ->isActive()
            ->latest('id');

        if ($service_id) {
            $query->where('service_id', $service_id);
        }

        if ($variant_id) {
            $query->where('variant_id', $variant_id);
        }

        $products = $query->paginate(10);

        $services = $products->getCollection()->groupBy('service_id');

        // Get existing cart quantities from session
        $existingCart = session('cart', []);

        // Save cart in session (only id + quantity) for current page products
        $newCart = [];
        foreach ($products->getCollection() as $product) {
            $newCart[$product->id] = [
                'quantity' => $existingCart[$product->id]['quantity'] ?? 1
            ];
        }

        // Merge with existing cart (keep quantities from other pages)
        $mergedCart = array_replace($existingCart, $newCart);
        session(['cart' => $mergedCart]);

        // Get variants for the selected service
        $variants = [];
        if ($service_id) {
            $service = $this->checkoutRepo->getServiceById($service_id);
            if ($service) {
                $variants = $this->checkoutRepo->getServiceVariants($service);
            }
        }

        $taxRate = CheckoutRepository::getTaxRate();

        return view('website.pages.shopping-cart0', compact('products', 'services', 'service_id', 'variant_id', 'variants', 'taxRate'));
    }

    public function update(Request $request, $id)
    {
        $cart = session('cart', []);

        if (!isset($cart[$id])) {
            return response()->json(['success' => false]);
        }

        $quantity = max(0, (int) $request->quantity);

        if ($quantity === 0) {
            unset($cart[$id]);
        } else {
            $cart[$id]['quantity'] = $quantity;
        }

        session(['cart' => $cart]);

        // Recalculate totals
        $productIds = array_keys($cart);
        $products = $this->checkoutRepo->getProductsByIds($productIds);

        $itemTotal = 0;
        foreach ($products as $product) {
            if ($product->id == $id) {
                $price = $product->discount_price ?? $product->price;
                $itemTotal = $price * $quantity;
            }
        }

        $subtotal = $products->sum(fn($p) => ($p->discount_price ?? $p->price) * $cart[$p->id]['quantity']);
        $tax = $subtotal * (CheckoutRepository::getTaxRate() / 100);
        $grandTotal = $subtotal + $tax;

        return response()->json([
            'success' => true,
            'quantity' => $quantity,
            'itemTotal' => number_format($itemTotal, 2),
            'subtotal' => number_format($subtotal, 2),
            'tax' => number_format($tax, 2),
            'grandTotal' => number_format($grandTotal, 2),
        ]);
    }


    public function remove($id)
    {
        $cart = session('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
        }

        return response()->json(['success' => true]);
    }




    public function checkout(Request $request)
    {

        $selectedIds = $request->input('selected_ids'); // CSV of selected product IDs
        $quantitiesInput = $request->input('quantities'); // JSON of {id: quantity}

        if (!$selectedIds) {
            return redirect()->route('web.cart')->with('error', 'لم يتم تحديد أي عناصر.');
        }

        $selectedIds = explode(',', $selectedIds);

        // Parse quantities from JSON or use session
        $quantities = [];
        if ($quantitiesInput) {
            $quantities = json_decode($quantitiesInput, true) ?? [];
        } else {
            // Fallback to session cart
            $cart = session('cart', []);
            $quantities = array_map(fn($item) => $item['quantity'] ?? 1, $cart);
        }

        $productIds = $selectedIds;
        $products = Product::with('service')->whereIn('id', $productIds)->get();

        // Attach quantity from request to each product
        foreach ($products as $product) {
            $product->cart_quantity = $quantities[$product->id] ?? 1;
        }

        // Group products by service
        $services = $products->groupBy('service_id');

        // Calculate totals
        $subtotal = $products->sum(fn($p) => ($p->discount_price ?? $p->price) * $p->cart_quantity);
        $taxRate = CheckoutRepository::getTaxRate();
        $tax = $subtotal * ($taxRate / 100);
        $grandTotal = $subtotal + $tax;

        $user = auth()->user();
        $addresses = collect();

        if ($user) {
            $customer = $this->customerRepo->findByUserId($user->id);
            if (!$customer) {
                $customer = $this->customerRepo->storeByUser((object)$user);
            }
            $addresses = $this->addressRepo->getByCustomerId($customer->id);
        }


        $cartData = [];
        foreach ($selectedIds as $id) {
            $cartData[$id] = ['quantity' => $quantities[$id] ?? 1];
        }

        // Keep only the selected items for checkout so order totals match the displayed amount.
        session(['cart' => $cartData]);

        if (empty($cartData)) {
            return redirect()->route('web.cart')->with('error', 'لم يتم تحديد أي عناصر.');
        }


        return view('website.pages.checkout', compact(
            'products',
            'services',
            'subtotal',
            'tax',
            'grandTotal',
            'addresses',
            'taxRate'
        ));
    }

    public function placeOrder(CheckoutRequest $request)
    {

        $paymentMethod = $request->payment_method;

        $cart = session('cart', []);
        $selectedIds = $request->input('selected_ids');

        if ($selectedIds) {
            if (!is_array($selectedIds)) {
                $selectedIds = explode(',', $selectedIds);
            }
            $selectedIds = array_filter($selectedIds, fn($id) => $id !== null && $id !== '');
            $cart = array_intersect_key($cart, array_flip($selectedIds));
            session(['cart' => $cart]);
        }

        try {
            $order = $this->checkoutRepo->placeOrder($request);
        } catch (\Exception $e) {
            return redirect()->route('web.cart')
                ->with('error', 'فشل إنشاء الطلب. يرجى المحاولة مرة أخرى.');
        }

        if (!$order) {
            session(['cart' => $cart]);
            return redirect()->route('web.cart')
                ->with('error', 'فشل إنشاء الطلب. يرجى المحاولة مرة أخرى.');
        }

        $grandTotal = (float) $order->total_amount;
        $orderCode  = $order->order_code;


        $webSetting = WebSetting::first();

        if ($paymentMethod === 'stripe') {
            try {
                $currency = strtolower($webSetting->currency_name ?? 'usd');
                if ($currency === 'bdt') {
                    $currency = 'usd';
                }

                $order->load('products');

                $paymentUrl = (new PaymentRepository())->stripePaymentURL([
                    'currency' => $currency,
                    'amount' => $grandTotal,
                    'product_data' => "Order #{$orderCode}",
                ], $order->id);

                return redirect($paymentUrl);

            } catch (\Exception $e) {
                $restoredCart = [];
                foreach ($order->products as $product) {
                    $restoredCart[$product->id] = ['quantity' => $product->pivot->quantity ?? 1];
                }
                session(['cart' => $restoredCart]);
                return redirect()->route('web.cart')->with('error', 'فشل الدفع عبر Stripe: ' . $e->getMessage());
            }
        }

        // Cash on Delivery
        return redirect()->route('order.success', $order->id)
            ->with('success', 'تم إنشاء الطلب بنجاح! سيتم تحصيل الدفع عند التسليم.');
    }

    public function orderSuccess($id)
    {

        $order = $this->checkoutRepo->findOrderWithRelations($id);



        return view('website.pages.order-success', compact('order'));
    }

    /**
     * Order details page
     */
    public function orderDetails($id)
    {
        $order = $this->checkoutRepo->findOrderWithRelations($id);
        return view('website.pages.order-details', compact('order'));
    }

    /**
     * Apply coupon to cart
     */


    public function applyCoupon(Request $request)
    {
        $couponCode = $request->code;
        $amount = $request->amount;

        if (!$couponCode) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a coupon code'
            ], 400);
        }

        $coupon = (new CouponRepository())->findByCoupon($couponCode, $amount);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ], 400);
        }

        if ($coupon->min_amount >= $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order price is $' . number_format($coupon->min_amount, 2) . ' for this coupon'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'coupon' => new CouponResource($coupon)
        ]);
    }
}
