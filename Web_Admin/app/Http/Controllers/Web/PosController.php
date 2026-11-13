<?php

namespace App\Http\Controllers\Web;

use App\Enum\OrderStatus;
use App\Enum\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\ProductResource;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\Vendor;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Repositories\TransationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PaymentGatewayRepository;
use App\Models\PaymentGateway;
use App\Repositories\AddressRepository;
use App\Services\VendorStoreCustomerService;
use App\Support\OrderVendorOwnership;

class PosController extends Controller
{
    /**
     * POS and invoice actions are store staff tools: store owners only.
     * Driver accounts are scoped to viewing orders and status changes,
     * so every entry point in this controller rejects drivers outright.
     */
    public function __construct()
    {
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            abort(403, 'Drivers are not allowed to access the POS.');
        }
    }

    /**
     * The store (vendor) the current staff member belongs to, if any.
     * Super admin returns null (sees everything); store owners and their
     * drivers are scoped to their own store only.
     */
    protected function posVendorId(): ?int
    {
        if (!auth()->check()) {
            return null;
        }
        if (auth()->user()->hasRole('vendor_admin') || auth()->user()->hasRole('vendor_sub')) {
            return \App\Support\VendorAccount::idFor(auth()->user());
        }
        if (auth()->user()->hasRole('driver')) {
            return auth()->user()->driver?->vendor_id;
        }
        return null;
    }

    /**
     * Whether the current staff member may touch the given order.
     * Store owners and drivers are limited to orders with explicit ownership
     * matching their own store; unresolved legacy orders remain inaccessible.
     */
    protected function canTouchOrder(Order $order): bool
    {
        if ($this->isVendorStaff()) {
            $vendorId = $this->posVendorId();
            return $vendorId !== null
                && (int) $order->vendor_id === $vendorId
                && OrderVendorOwnership::productsMatchOrder($order);
        }
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            $driver = auth()->user()->driver;
            return $driver
                && $driver->vendor_id
                && (int) $order->vendor_id === (int) $driver->vendor_id
                && $driver->orders()->whereKey($order->id)->exists()
                && OrderVendorOwnership::productsMatchOrder($order);
        }
        return true;
    }

    /**
     * Whether the current staff member belongs to a store: store owner or a
     * driver hired by the store owner. Store staff are scoped to their own
     * store only (see posVendorId()).
     */
    protected function isVendorStaff(): bool
    {
        return auth()->check()
            && (auth()->user()->hasRole('vendor_admin')
                || auth()->user()->hasRole('vendor_sub')
                || auth()->user()->hasRole('driver'));
    }

    protected function customersForCurrentVendor()
    {
        if (!$this->isVendorStaff()) {
            return Customer::latest('id')->get();
        }
        $vendorId = $this->posVendorId();
        $vendor = $vendorId ? Vendor::find($vendorId) : null;
        if (!$vendor) {
            return collect();
        }

        // Store staff (owner or driver) see exactly the customers of their
        // store: delivered-order clients UNION customers registered in the
        // POS (store_customers). Creating a customer via the POS therefore
        // makes it selectable immediately, without an order first.
        return app(VendorStoreCustomerService::class)
            ->customersQuery($vendor)
            ->get()
            ->sortBy(fn (Customer $customer) => $customer->user?->name ?? '', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    protected function customerIsAvailableToVendor(int $customerId, $currentOrderCustomerId = null): bool
    {
        $vendorId = $this->posVendorId();
        if (!$vendorId) {
            return false;
        }
        if ($currentOrderCustomerId && (int) $currentOrderCustomerId === $customerId) {
            return true;
        }

        $hasVendorOrder = Customer::whereKey($customerId)
            ->whereHas('orders', fn ($query) => $query->where('vendor_id', $vendorId))
            ->exists();
        $linkedToStore = DB::table('store_customers')
            ->where('vendor_id', $vendorId)
            ->where('customer_id', $customerId)
            ->exists();
        $createdInVendorPos = in_array(
            $customerId,
            array_map('intval', session('pos_customer_ids.' . $vendorId, [])),
            true
        );

        return $hasVendorOrder || $linkedToStore || $createdInVendorPos;
    }

    public function index()
    {
        $repository = new PaymentGatewayRepository();
            $gateways = $repository->query()
            ->where('is_active', 1)
            ->get();

        $data = [
            'paymentGateways' => $gateways,
            'customers' => $this->customersForCurrentVendor(),
            'services' => Service::isActive()->orderBy('name')->get(),

        ];
        $vendorId = $this->posVendorId();
        if ($this->isVendorStaff()) {
            // Store owners and drivers see the ACTIVE global catalog (added by
            // the super admin) directly, plus their own store services.
            $data['services'] = $vendorId
                ? Service::query()
                    ->isActive()
                    ->where(fn ($query) => $query
                        ->whereNull('vendor_id')
                        ->orWhere('vendor_id', $vendorId))
                    ->orderBy('name')
                    ->get()
                : Service::query()->whereRaw('1 = 0')->get();
        }
        return view('pos.index', $data);
    }

    public function sales()
    {
        $orderStatus = OrderStatus::cases();

        $orders = (new OrderRepository())->query();
        $vendorId = $this->posVendorId();
        if ($this->isVendorStaff()) {
            $vendorId
                ? $orders->where('vendor_id', $vendorId)
                : $orders->whereRaw('1 = 0');
        }
        $orders = $orders->with('products.service')->latest()->get();
        if ($this->isVendorStaff()) {
            $orders = $orders
                ->filter(fn (Order $order) => OrderVendorOwnership::productsMatchOrder($order))
                ->values();
        }

        return view('pos.sales', compact('orders', 'orderStatus'));
    }

    public function store(Request $request)
    {
        // An empty address selection must behave like "no address", not fail
        // the integer/exists validation rules below.
        if (!$request->filled('address_id')) {
            $request->merge(['address_id' => null]);
        }
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer|exists:customers,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'address_id' => 'nullable|integer|exists:addresses,id',
        ]);

        if ($validator->fails()) {
            return $this->json(__('Validation Error'), $validator->errors()->all(), 422);
        }

        if ($this->isVendorStaff() && !$this->customerIsAvailableToVendor((int) $request->customer_id)) {
            return $this->json(__('غير مصرح لك'), [], 404);
        }
        if ($request->filled('address_id') && !Address::whereKey($request->address_id)
            ->where('customer_id', $request->customer_id)->exists()) {
            return $this->json(__('غير مصرح لك'), [], 404);
        }

        OrderVendorOwnership::resolveProductVendor(
            collect($request->products)->pluck('id')->all(),
            $this->isVendorStaff() ? $this->posVendorId() : null
        );

        $order = (new OrderRepository())->PosStoreByRequest($request);
        (new TransationRepository())->storeForOrder($order);

        if ($order->payment_type != 'cash') {
            $paymentUrl = route('pos.payment', ['order' => $order->id, 'gateway' => $order->payment_type]);

            return $this->json(__('Order Successful'), [
                'message' => __('Order is added successfully'),
                'payment_url' => $paymentUrl,
                'payment_type' => $order->payment_type,
                'orders' => $order,
            ]);
        }

        return $this->json(__('Order Successful'), [
            'message' => __('Order is added successfully'),
            'payment_type' => $order->payment_type,
            'payment_method' => $order->payment_method,
            'orders' => $order,
        ]);
    }

    /**
     * Show the order edit page (POS style) so staff can edit products, quantities,
     * prices, customer, notes and delivery date.
     */
    public function edit(Order $order)
    {
        // Stores may only edit their own store's orders
        if (!$this->canTouchOrder($order)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        $data = [
            'paymentGateways' => (new PaymentGatewayRepository())->query()->where('is_active', 1)->get(),
            'customers' => $this->customersForCurrentVendor(),
            'services' => Service::isActive()->orderBy('name')->get(),
            'order' => $order->load(['products', 'address']),
        ];

        $vendorId = $this->posVendorId();
        if ($this->isVendorStaff()) {
            // Store owners and drivers see the ACTIVE global catalog (added by
            // the super admin) directly, plus their own store services.
            $data['services'] = $vendorId
                ? Service::query()
                    ->isActive()
                    ->where(fn ($query) => $query
                        ->whereNull('vendor_id')
                        ->orWhere('vendor_id', $vendorId))
                    ->orderBy('name')
                    ->get()
                : Service::query()->whereRaw('1 = 0')->get();
        }

        return view('orders.edit', $data);
    }

    /**
     * Update an existing order with edited products / quantities / prices / customer / notes.
     */
    public function update(Request $request, Order $order)
    {
        // Stores may only update their own store's orders
        if (!$this->canTouchOrder($order)) {
            return $this->json(__('غير مصرح لك'), [], 403);
        }
        // An empty address selection must behave like "no address".
        if (!$request->filled('address_id')) {
            $request->merge(['address_id' => null]);
        }
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'address_id' => 'nullable|integer|exists:addresses,id',
        ]);

        if ($validator->fails()) {
            return $this->json(__('Validation Error'), $validator->errors()->all(), 422);
        }
        if ($this->isVendorStaff() && $request->filled('customer_id')
            && !$this->customerIsAvailableToVendor((int) $request->customer_id, $order->customer_id)) {
            return $this->json(__('غير مصرح لك'), [], 404);
        }
        $effectiveCustomerId = $request->customer_id ?: $order->customer_id;
        $effectiveAddressId = $request->address_id ?: $order->address_id;
        if ($effectiveAddressId && !Address::whereKey($effectiveAddressId)
            ->where('customer_id', $effectiveCustomerId)->exists()) {
            return $this->json(__('غير مصرح لك'), [], 404);
        }

        // Only allow price changes if the user has permission
        if (!$request->user()->can('order.edit.price')) {
            $orderProducts = $order->products->keyBy('id');
            $repository = new OrderRepository();
            $products = collect($request->products)->map(function ($product) use ($orderProducts, $repository) {
                $existing = $orderProducts->get($product['id']);
                $existingPrice = $existing ? (float) $existing->pivot->price : 0.0;
                // A stored pivot price of 0 is a missing-price artifact, not a
                // real price — fall back to catalog pricing instead of zeroing
                // the order total.
                $product['price'] = $existingPrice > 0
                    ? $existingPrice
                    : $repository->resolveProductPrice((int) $product['id']);
                return $product;
            })->values()->toArray();

            $request->merge(['products' => $products]);
        }

        $order = (new OrderRepository())->PosUpdateByRequest($request, $order);

        return $this->json(__('Order Updated'), [
            'message' => __('Order updated successfully'),
            'orders' => $order,
        ]);
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        // Stores may only update their own store's orders
        if (!$this->canTouchOrder($order)) {
            return $this->json(__('غير مصرح لك'), [], 403);
        }
        $status = $request->payment_status ?: 'paid';
        $status = config('enums.payment_status.' . strtolower($status), $status);

        $order->update([
            'payment_status' => $status,
        ]);

        return $this->json(__('Payment Status Updated'), [
            'message' => __('Payment status updated successfully'),
            'payment_status' => $order->payment_status,
        ]);
    }

    public function payment(){
        $stripeGateway  = PaymentGateway::where('name', 'stripe')->first();
        $razorpayGateway = PaymentGateway::where('name', 'razorpay')->first();
        $payStackGateway = PaymentGateway::where('name', 'paystack')->first();
        $stripe_publish_key = optional(json_decode($stripeGateway->config))->published_key ?? '';
        $razorpay_publish_key = optional(json_decode($razorpayGateway->config))->key ?? '';
        $paystack_publish_key = optional(json_decode($payStackGateway->config))->public_key ?? '';

        $data = [
            'stripe_publish_key' => $stripe_publish_key,
            'razorpay_publish_key' => $razorpay_publish_key,
            'paystack_publish_key' => $paystack_publish_key,
        ];
        return view('pos.payment',compact('data'));
    }


    public function storeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'mobile' => 'required|unique:users,mobile',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->json(__('Validation Error'), $validator->errors(), 422);
        }

        $request['is_active'] = 1;

        $user = (new UserRepository())->registerUser($request);

        $user->assignRole(Roles::CUSTOMER->value);

        (new CustomerRepository())->storeByUser($user);

        if ($this->isVendorStaff() && $this->posVendorId()) {
            $vendorId = (int) $this->posVendorId();
            $customerId = (int) $user->customer->id;
            $allowed = session('pos_customer_ids.' . $vendorId, []);
            $allowed[] = $customerId;
            session(['pos_customer_ids.' . $vendorId => array_values(array_unique($allowed))]);

            // Persist the store ↔ customer link so the customer appears in
            // the store's customer list (POS picker and «عملاء المتجر»).
            DB::table('store_customers')->insertOrIgnore([
                'vendor_id' => $vendorId,
                'customer_id' => $customerId,
                'source' => 'pos',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $this->json(__('تم الإنشاء بنجاح'), [
            'user' => (object)[
                'id' => $user->customer->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
            ],
        ], 200);
    }

    public function storeAddress(AddressRequest $request)
    {
        if ($this->isVendorStaff() && !$this->customerIsAvailableToVendor((int) $request->customer_id)) {
            return $this->json(__('غير مصرح لك'), [], 404);
        }

        $address = (new AddressRepository())->storeByPos($request);

        return $this->json(__('تم الإنشاء بنجاح'), [
            'address' => (object)[
                'id' => $address->id,
                'address_name' => $address->address_name,
                'road_no' => $address->road_no,
            ],
        ], 200);
    }

    public function fetchProducts(Request $request)
    {

        $products = (new ProductRepository())->getByRequest($request);

        // Products with no store ownership (added by the super admin) are
        // sold by every store; products with a store are store-only.
        $vendorId = $this->posVendorId();
        if ($this->isVendorStaff()) {
            $products = $products->filter(function ($product) use ($vendorId) {
                return $vendorId && (
                    (int) $product->vendor_id === (int) $vendorId
                    || $product->vendor_id === null
                );
            })->values();
        }

        return $this->json(__('product list'), [
            'products' => ProductResource::collection($products)
        ]);
    }
}