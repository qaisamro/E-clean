<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\WebSetting;
use Illuminate\Support\Facades\DB;

class CheckoutRepository extends Repository
{
    private $path = 'images/products/';

    public static function getTaxRate()
    {
        return WebSetting::first()?->tax_rate ?? 0;
    }

    public function model()
    {
        return Order::class;
    }

    /**
     * Get service by ID
     */
    public function getServiceById($serviceId)
    {
        return Service::find($serviceId);
    }

    /**
     * Get variants for a service
     */
    public function getServiceVariants($service)
    {
        return $service ? $service->variants : collect();
    }

    /**
     * Get products by IDs
     */
    public function getProductsByIds($productIds)
    {
        return Product::findMany($productIds);
    }

    /**
     * Get products with service and variant
     */
    public function getProductsWithRelations($productIds)
    {
        return Product::query()
            ->with(['service', 'variant'])
            ->whereNull('product_id')
            ->whereIn('id', $productIds)
            ->get();
    }

    /**
     * Place order from cart
     */
    public function placeOrder($request)
    {

      $cart = session('cart');

    if (empty($cart)) {
        throw new \Exception('No items selected in cart');
    }


        // Get or create customer record
        $user = auth()->user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            $customer = Customer::create([
                'user_id' => $user->id,
                'email'   => $user->email,
                'name'    => $user->first_name . ' ' . $user->last_name,
                'phone'   => $user->mobile,
            ]);
        }

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get();

        $subtotal = $products->sum(fn($p) => ($p->discount_price ?? $p->price) * $cart[$p->id]['quantity']);
        $tax = $subtotal * (self::getTaxRate() / 100);
        $grandTotal = $subtotal + $tax;

        // Generate order code
        $userId = $customer->id;
        $orderCode = (new OrderRepository())->generateUniqueOrderCode();

        $order = Order::create([
            'prefix' => 'IM',
            'order_code' => $orderCode,
            'customer_id' => $customer->id,  // Use Customer's ID, not User's ID
            'coupon_id' => null,
            'discount' => 0,
            'pick_date' => $request->pick_date,
            'delivery_date' => $request->delivery_date,
            'pick_hour' => $request->pick_hour ? (new OrderRepository())->setPickOrDeliveryTime($request->pick_date, $request->pick_hour) : null,
            'delivery_hour' => $request->delivery_hour ? (new OrderRepository())->setPickOrDeliveryTime($request->delivery_date, $request->delivery_hour, 'delivery') : null,
            'amount' => $subtotal,
            'total_amount' => $grandTotal,
            'payment_status' => 'Pending',
            'order_status' => 'Pending',
            'payment_type' => $request->payment_method,
            'address_id' => $request->address_id,
        ]);

        // Attach products to order (pivot table)
        foreach ($products as $product) {
            $order->products()->attach($product->id, [
                'quantity' => $cart[$product->id]['quantity'],
            ]);
        }

        // Clear cart
        session()->forget('cart');

        return $order;
    }

    /**
     * Calculate cart totals
     */
    public function calculateCartTotals($productIds, $quantities)
    {
        $products = Product::with('service')->whereIn('id', $productIds)->get();

        // Attach quantity from request to each product
        foreach ($products as $product) {
            $product->cart_quantity = $quantities[$product->id] ?? 1;
        }

        $subtotal = $products->sum(fn($p) => ($p->discount_price ?? $p->price) * $p->cart_quantity);
        $tax = $subtotal * (self::getTaxRate() / 100);
        $grandTotal = $subtotal + $tax;

        return [
            'products' => $products,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'grandTotal' => $grandTotal,
        ];
    }

    /**
     * Get cart products
     */
    public function getCartProducts($productIds)
    {
        return Product::with('service')->whereIn('id', $productIds)->get();
    }

    /**
     * Find order by id
     */
    public function findOrderWithRelations($id)
    {
        return Order::with(['products.service', 'address'])->findOrFail($id);
    }
}
