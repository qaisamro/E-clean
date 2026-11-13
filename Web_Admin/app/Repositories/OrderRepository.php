<?php


namespace App\Repositories;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\DeliveryCost;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Enum\PaymentType;
use App\Models\Product;
use Carbon\Carbon;
use App\Support\OrderVendorOwnership;

class OrderRepository extends Repository
{
    public function model()
    {
        return Order::class;
    }
    public function countByStatus(array $status)
    {
        return $this->query()
            ->whereIn(
                'order_status',
                $status
            )
            ->count();
    }


    public function getByStatus($status)
    {
        return $this->query()->where('order_status', $status)->get();
    }

    /**
     * Get all orders for a customer along with their address relation.
     */
    public function getByCustomerIdWithAddress(int $customerId)
    {
        return $this->query()
            ->where('customer_id', $customerId)
            ->with('address')
            ->latest()
            ->get();
    }
    public function getByTodays()
    {
        return $this->model()::whereDate('created_at', Carbon::today())->get();
    }

    /**
     * Determine if any orders exist for a given customer id.
     *
     * Used for legacy compatibility when customer_id might be user id.
     */
    public function existsByCustomerId(int $customerId): bool
    {
        return $this->query()->where('customer_id', $customerId)->exists();
    }

    public function storeByRequest(OrderRequest $request): Order
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $vendorId = OrderVendorOwnership::resolveProductVendor(
                collect($request->products)->pluck('id')->all(),
                $request->filled('vendor_id') ? (int) $request->vendor_id : null
            );
            // A stable row serializes booking and schedule edits, even before settings exist.
            if ($vendorId) \App\Models\Vendor::whereKey($vendorId)->lockForUpdate()->firstOrFail();
            $service = app(\App\Services\VendorAppointments::class);
            $pair = $vendorId && $service->settings($vendorId)['enabled']
                ? $service->validatePair($vendorId, $request->all()) : null;
            $order = $this->createCustomerOrder($request, $pair);
            if ($pair) $service->snapshot($order, $pair);
            return $order;
        }, 3);
    }

    private function createCustomerOrder(OrderRequest $request, ?array $pair): Order
    {
        $customer = auth()->user()->customer;
        if (!$customer) {
            abort(403);
        }
        $vendorId = OrderVendorOwnership::resolveProductVendor(
            collect($request->products)->pluck('id')->all(),
            $request->filled('vendor_id') ? (int) $request->vendor_id : null
        );
        $getAmount = $this->getAmount($request, $vendorId);

        $order = $this->create([
            'customer_id' => $customer->id,
            'vendor_id' => $vendorId,
            'order_code' => $this->generateUniqueOrderCode(),
            'prefix' => 'LM',
            'coupon_id' => $request->coupon_id,
            'discount' => $getAmount['discount'],
            'pick_date' => $request->pick_date,
            'delivery_date' => $request->delivery_date,
            'pick_hour' => $pair ? $pair['pickup']['hour'].':00' : $this->setPickOrDeliveryTime($request->pick_date, $request->pick_hour),
            'delivery_hour' => $pair ? $pair['delivery']['hour'].':00' : $this->setPickOrDeliveryTime($request->delivery_date, $request->delivery_hour, 'delivery'),
            'amount' => $getAmount['subTotal'],
            'total_amount' => $getAmount['total'],
            'delivery_charge' => $getAmount['deliveryCharge'],
            'payment_status' => config('enums.payment_status.pending'),
            'payment_type' => $request->payment_type,
            'order_status' => config('enums.payment_status.pending'),
            'address_id' => $request->address_id,
            'instruction' => $request->instruction
        ]);

        foreach ($request->products as $product) {
            $order->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'price' => $this->resolveProductPrice($product['id']),
                'title' => $this->resolveProductTitle($product['id']),
            ]);
        }

        return $order;
    }
    public function PosStoreByRequest(Request $request): Order
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $vendorId = OrderVendorOwnership::resolveProductVendor(collect($request->products)->pluck('id')->all(), $this->actorVendorId());
            if ($vendorId) \App\Models\Vendor::whereKey($vendorId)->lockForUpdate()->firstOrFail();
            return $this->createPosOrder($request);
        }, 3);
    }

    private function createPosOrder(Request $request): Order
    {
        $products = $request->products;
        $actorVendorId = $this->actorVendorId();
        $vendorId = OrderVendorOwnership::resolveProductVendor(
            collect($products)->pluck('id')->all(),
            $actorVendorId
        );

        $totalAmount = 0;

        foreach ($products as $product) {
            $totalAmount += ($product['quantity']) * ($product['price']);
        }
        $grandTotal = ($totalAmount + $request->delivery_charge) - $request->discount;

        $order = $this->create([
            'customer_id' => $request->customer_id ?? null,
            'vendor_id' => $vendorId,
            'order_code' => $this->generateUniqueOrderCode(),
            'prefix' => 'LM',
            'pick_date' => now()->format('Y-m-d'),
            'pick_hour' => now()->format('H:00:00'),
            'delivery_date' => $request->delivery_date ?? now()->format('Y-m-d'),
            'delivery_hour' => $this->posDeliveryHour($request),
            'delivery_charge' => (float) $request->delivery_charge,
            'discount' => (float) $request->discount,
            'amount' => (float)$totalAmount,
            'total_amount' => (float)$grandTotal,
            'payment_status' => $request->payment_status ? (config('enums.payment_status.' . strtolower($request->payment_status), $request->payment_status)) : config('enums.payment_status.pending'),
            'payment_type' => $request->payment_id ? $request->payment_id : 'cash',
            'payment_method' => $request->payment_method ? strtolower($request->payment_method) : 'cash',
            'order_status' => OrderStatus::CONFIRM->value,
            'address_id' => $request->address_id ?: null,
            'instruction' => $request->instruction ?? null,
            'confirmed_at' => now(),
        ]);


        foreach ($products as $product) {
            $order->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'price' => isset($product['price']) ? (float) $product['price'] : $this->resolveProductPrice($product['id']),
                'title' => isset($product['title']) ? $product['title'] : $this->resolveProductTitle($product['id']),
            ]);
        }

        // إذا كان المنشئ سائقاً، اربط الطلب به تلقائياً ليظهر في قائمته
        if (auth()->check() && auth()->user()->hasRole('driver') && auth()->user()->driver) {
            try {
                $order->drivers()->attach(auth()->user()->driver->id, ['is_accept' => 1, 'status' => 'delivery']);
            } catch (\Throwable $e) {
                // تجاهل إذا كان مرتبطاً مسبقاً
            }
        }

        return $order;
    }

    /**
     * Update an existing order (POS) with new products / quantities / prices / customer / notes / delivery date.
     */
    public function PosUpdateByRequest(Request $request, Order $order): Order
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $order) {
            if ($order->vendor_id) \App\Models\Vendor::whereKey($order->vendor_id)->lockForUpdate()->firstOrFail();
            return $this->updatePosOrder($request, Order::whereKey($order->id)->lockForUpdate()->firstOrFail());
        }, 3);
    }

    private function updatePosOrder(Request $request, Order $order): Order
    {
        if (\App\Models\OrderAppointment::where('order_id', $order->id)->exists() &&
            $request->filled('delivery_date') &&
            Carbon::parse($request->delivery_date)->toDateString() !== $order->delivery_date?->toDateString()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'delivery_date' => 'هذا الطلب له موعد محجوز؛ استخدم إعادة الجدولة بعد موافقة العميل.',
            ]);
        }
        $products = $request->products;
        OrderVendorOwnership::assertProductsMatchOrder(
            $order,
            collect($products)->pluck('id')->all()
        );

        $totalAmount = 0;
        foreach ($products as $product) {
            $totalAmount += (float) $product['quantity'] * (float) $product['price'];
        }
        $grandTotal = ($totalAmount + (float) $request->delivery_charge) - (float) $request->discount;
        if ($grandTotal < 0) {
            $grandTotal = 0;
        }

        $this->update($order, [
            'customer_id' => $request->customer_id ?? $order->customer_id,
            'delivery_date' => $request->delivery_date ?? $order->delivery_date,
            'delivery_charge' => (float) $request->delivery_charge,
            'discount' => (float) $request->discount,
            'amount' => (float) $totalAmount,
            'total_amount' => (float) $grandTotal,
            'payment_status' => $request->payment_status ? (config('enums.payment_status.' . strtolower($request->payment_status), $request->payment_status)) : $order->payment_status,
            'payment_type' => $request->payment_id ? $request->payment_id : ($order->payment_type ?: 'cash'),
            'payment_method' => $request->payment_method ? strtolower($request->payment_method) : $order->payment_method,
            'address_id' => $request->address_id ?? $order->address_id,
            'instruction' => $request->instruction ?? $order->instruction,
        ]);

        // Sync products with quantity / price / title snapshot
        $syncData = [];
        foreach ($products as $product) {
            $syncData[$product['id']] = [
                'quantity' => (int) $product['quantity'],
                'price' => isset($product['price']) ? (float) $product['price'] : $this->resolveProductPrice($product['id']),
                'title' => isset($product['title']) ? $product['title'] : $this->resolveProductTitle($product['id']),
            ];
        }

        $order->products()->sync($syncData);

        return $order->fresh();
    }

    public function resolveProductPrice($productId)
    {
        $product = Product::withTrashed()->find($productId);
        return $product ? (float) ($product->discount_price ?: $product->price) : 0;
    }

    /**
     * Delivery hour chosen on the POS screen (H:i or H:i:s).
     * Falls back to the current hour when missing or unparsable.
     */
    private function posDeliveryHour($request): string
    {
        $raw = $request->delivery_hour ?? $request->get('delivery_hour');
        if (is_string($raw) && preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', trim($raw))) {
            $time = trim($raw);
            return strlen($time) === 5 ? $time . ':00' : $time;
        }

        return now()->format('H:00:00');
    }

    public function resolveProductTitle($productId)
    {
        $product = Product::withTrashed()->find($productId);
        return $product ? $product->name : '';
    }


    public function updateByRequest($request, Order $order)
    {
        $request['coupon_id'] = $order->coupon_id;
        $getAmount = $this->getAmount($request, $order->vendor_id);

        $this->update($order, [
            'discount' => $getAmount['discount'],
            'amount' => $getAmount['subTotal'],
            'total_amount' => $getAmount['total'],
        ]);
        $order->products()->detach($order->products->pluck('id')->toArray());

        foreach ($request->products as $product) {
            $order->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'price' => $this->resolveProductPrice($product['id']),
                'title' => $this->resolveProductTitle($product['id']),
            ]);
        }

        return $order;
    }

    private function getAmount($request, $vendorId = null): array
    {
        $totalAmount = 0;
        foreach ($request->products as $item) {
            // Catalog prices — mirror resolveProductPrice (trashed products
            // keep their order lines; try/catch on the null-friendly path).
            $product = Product::withTrashed()->find($item['id']);
            $price = $product ? (float) ($product->discount_price ?: $product->price) : 0;
            $totalAmount += (float)$item['quantity'] * $price;
        }

        $total = $totalAmount;
        // Coupon is validated ONLY at apply time — re-check it at order
        // placement: wrong store or below minimum means NO discount (never
        // silently give a lower-scoped coupon than the customer asked for).
        $coupon = (new CouponRepository())->findById($request->coupon_id);
        if ($coupon) {
            $matchesScope = $coupon->vendor_id === null
                || ($vendorId && (int) $coupon->vendor_id === (int) $vendorId);
            $minOk = $total >= (float) $coupon->min_amount;
            if (!$matchesScope || !$minOk) {
                $coupon = null;
            }
        }
        $couponDiscount = $coupon ? $coupon->calculate($total, $coupon) : 0;

        $deliveryCost = DeliveryCost::first();
        $freeDelivery = $deliveryCost ? $deliveryCost->fee_cost : 0;
        $deliveryCharge = $deliveryCost ? $deliveryCost->cost : 0;

        $total = $total <= $freeDelivery ? $total + $deliveryCharge : $total;
        $total = $total - $couponDiscount;

        return [
            'total' => $total,
            'discount' => $couponDiscount,
            'subTotal' => $totalAmount,
            'deliveryCharge' => $deliveryCharge
        ];
    }

    public function getSortedByRequest(Request $request)
    {
        $status = $request->status;
        $searchKey = $request->search;
        $sort = $request->sort;

        $orders = $this->model()::query()->with('products.service');

        if ($this->isStoreScopedUser()) {
            $vendorId = $this->actorVendorId();
            $orders = $vendorId
                ? $orders->where('vendor_id', $vendorId)
                : $orders->whereRaw('1 = 0');
        }

        // Drivers see orders currently assigned to them, not another
        // driver's history or an order linked to a different store.
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            $driver = auth()->user()->driver;
            if (!$driver || !$driver->vendor_id) {
                return collect();
            }
            $orders->where('vendor_id', $driver->vendor_id)
                ->whereHas('drivers', fn ($query) => $query->where('drivers.id', $driver->id));
        }

        if ($status) {
            $status = config('enums.order_status.' . $status);

            $orders = $orders->where('order_status', $status);
        }

        if ($searchKey) {
            $orders = $orders->where(function ($query) use ($searchKey) {
                $query->orWhere('order_code', 'like', "%{$searchKey}%")
                    ->orWhereHas('customer', function ($customer) use ($searchKey) {
                        $customer->whereHas('user', function ($user) use ($searchKey) {
                            $user->where('first_name', $searchKey)
                                ->orWhere('last_name', $searchKey)
                                ->orWhere('mobile', $searchKey);
                        });
                    })
                    ->orWhere('prefix', 'like', "%{$searchKey}%")
                    ->orWhere('amount', 'like', "%{$searchKey}%")
                    ->orWhere('payment_status', 'like', "%{$searchKey}%")
                    ->orWhere('order_status', 'like', "%{$searchKey}%");
            });
        }

        $results = $sort == 'oldest'
            ? $orders->orderBy('created_at', 'asc')->get()
            : $orders->latest()->get();

        if ($this->isStoreScopedUser() || (auth()->check() && auth()->user()->hasRole('driver'))) {
            $results = $results
                ->filter(fn (Order $order) => OrderVendorOwnership::productsMatchOrder($order))
                ->values();
        }

        return $results;
    }

    public function orderListByStatus($pageNo, $perPage, $status = null)
    {
        $customer = auth()->user()->customer;
        $perPage = $perPage ?? 10;
        $orders = $this->query()->where('customer_id', $customer->id);

        if ($status) {
            $orders = $orders->where('order_status', $status);
        }

        return $orders->latest()->paginate($perPage, ['*'], 'page', $pageNo);
    }

    public function statusUpdateByRequest(Order $order, $status): Order
    {
        $order->update([
            'order_status' => $status,
        ]);

        $drivers = $order->drivers;

        if ($drivers && in_array($status, [
            config('enums.order_status.delivered'),
            config('enums.order_status.picked_order'),
        ], true)) {
            foreach ($drivers as $driver) {
                $driver->orderHistories()->attach($driver->pivot->order_id, ['status' => $driver->pivot->status]);
                $order->drivers()->detach($driver->id);
            }
        }
        return $order;
    }

    /**
     * Scope an order query to the current store owner's own store
     * Orders without a proven store are excluded for store owners.
     */
    protected function scopeReportVendor($query)
    {
        if ($this->isStoreScopedUser()) {
            $vendorId = $this->actorVendorId();
            if ($vendorId) {
                $query->where('vendor_id', $vendorId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        return $query;
    }

    protected function isStoreScopedUser(): bool
    {
        return \App\Support\VendorAccount::isStoreStaff(auth()->user());
    }

    protected function actorVendorId(): ?int
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();
        if ($user->hasRole('driver')) {
            return $user->driver?->vendor_id ? (int) $user->driver->vendor_id : null;
        }

        return \App\Support\VendorAccount::idFor($user);
    }

    public function getRevenueReportByBetweenDate($form, $to)
    {
        $query = $this->model()::query();
        $query = $this->scopeReportVendor($query);
        $query->where('payment_status', config('enums.payment_status.paid'));
        // When no date range is supplied, show all paid orders instead of
        // an empty result (delivery_date BETWEEN NULL AND NULL).
        if ($form && $to) {
            $query->whereBetween('delivery_date', [$form, $to]);
        }
        $orders = $query->with('products.service')->get();
        return $this->isStoreScopedUser()
            ? OrderVendorOwnership::filterConsistentOrders($orders)
            : $orders;
    }


    /**
     * Super-admin dashboard income: sum of ALL orders from ALL stores
     * (any vendor including legacy orders without vendor, any status,
     * any date) so the figure is never stuck at zero.
     */
    public function getIncome()
    {
        return $this->model()::query()->sum('total_amount');
    }

    /**
     * Build summary statistics for the reports page between two dates.
     */
    public function getReportStats($from, $to)
    {
        $base = $this->model()::query();
        $base = $this->scopeReportVendor($base);

        if ($from && $to) {
            $base->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $orders = (clone $base)->with('products.service')->get();
        if ($this->isStoreScopedUser()) {
            $orders = OrderVendorOwnership::filterConsistentOrders($orders);
            $base->whereIn('id', $orders->pluck('id'));
        }

        $statuses = array_values(config('enums.order_status'));

        $byStatus = [];
        foreach ($statuses as $status) {
            $byStatus[$status] = (clone $base)->where('order_status', $status)->count();
        }

        $topProducts = (clone $base)
            ->with(['products' => function ($q) {
                $q->withTrashed();
            }])
            ->get()
            ->flatMap(function ($order) {
                return $order->products->map(function ($product) {
                    return [
                        'name' => $product->pivot->title ?: $product->name,
                        'quantity' => $product->pivot->quantity,
                        'amount' => ($product->pivot->price ?: ($product->discount_price ?: $product->price)) * $product->pivot->quantity,
                    ];
                });
            })
            ->groupBy('name')
            ->map(function ($group) {
                return [
                    'name' => $group->first()['name'],
                    'quantity' => $group->sum('quantity'),
                    'amount' => round($group->sum('amount'), 2),
                ];
            })
            ->sortByDesc('quantity')
            ->take(10)
            ->values();

        return [
            'total_sales' => round($orders->sum('total_amount'), 2),
            'total_orders' => $orders->count(),
            'paid_orders' => $orders->where('payment_status', config('enums.payment_status.paid'))->count(),
            'unpaid_orders' => $orders->whereIn('payment_status', [config('enums.payment_status.pending'), config('enums.payment_status.unpaid')])->count(),
            'cash_orders' => $orders->where('payment_method', 'cash')->count(),
            'visa_orders' => $orders->whereIn('payment_method', ['visa', 'card'])->count(),
            'by_status' => $byStatus,
            'top_products' => $topProducts,
        ];
    }



    public function getByDatePickOrDelivery($date, $type = 'picked')
    {
        $orders = $this->model()::query();

        if ($type == 'picked') {
            $orders = $orders->where('pick_date', $date);
        }

        if ($type == 'delivery') {
            $orders = $orders->where('delivery_date', $date);
        }

        return $orders->get();
    }

    public function findById($id)
    {

        return $this->find($id);
    }

    /**
     * Get order with all relationships for order details page
     */
    public function getOrderDetailsById($id)
    {
        return $this->query()
            ->with([
                'products.service',
                'products.variant',
                'address',
                'customer.user',
                'payment',
                'transaction',
                'coupon',
                'drivers'
            ])
            ->find($id);
    }

    /**
     * Get orders by customer ID with relationships
     */
    public function getByCustomerId(int $customerId)
    {
        return $this->query()
            ->where('customer_id', $customerId)
            ->with([
                'products.service',
                'products.variant',
                'address',
                'customer',
                'payment',
                'transaction',
                'coupon'
            ])
            ->latest()
            ->get();
    }

    /**
     * Get order statistics by customer ID
     */
    public function getStatsByCustomerId(int $customerId, int $userId = null): array
    {
        // Check if there are orders stored with user_id as customer_id (legacy data)
        $customerIdToUse = $customerId;
        if ($userId && $this->query()->where('customer_id', $userId)->exists()) {
            $customerIdToUse = $userId;
        }

        $orders = $this->query()->where('customer_id', $customerIdToUse)->get();

        // Use config values instead of enum to match what's stored in database
        $inProgressStatuses = [
            config('enums.order_status.pending'),
            config('enums.order_status.order_confirmed'),
            config('enums.order_status.picked_order'),
            config('enums.order_status.processing'),
            config('enums.order_status.on_the_way'),
        ];

        return [
            'total' => $orders->count(),
            'in_progress' => $orders->whereIn('order_status', $inProgressStatuses)->count(),
            'completed' => $orders->where('order_status', config('enums.order_status.delivered'))->count(),
            'ready_for_pickup' => $orders->where('order_status', config('enums.order_status.ready_for_pickup'))->count(),
            'on_the_way' => $orders->where('order_status', config('enums.order_status.on_the_way'))->count(),
            'cancelled' => $orders->where('order_status', config('enums.order_status.cancelled'))->count(),
        ];
    }

    public function setPickOrDeliveryTime($date, $times, $type = 'picked')
    {
        if (str_contains($times, ':')) {
            return Carbon::parse($times)->format('H:i:00');
        }

        $times = explode('-', $times);

        foreach ($times as $time) {
            $orders = $this->model()::query();
            if ($type == 'picked') {
                $orders = $orders->where('pick_date', $date)->where('pick_hour', 'LIKE', "%$time%");
            }

            if ($type == 'delivery') {
                $orders = $orders->where('delivery_date', $date)->where('delivery_hour', 'LIKE', "%$time%");
            }

            if ($orders->count() < 2) {
                return sprintf('%02s', $time) . ':' . sprintf('%02s', ($orders->count() * 30)) . ':00';
            }
        }
    }
    public  function generateUniqueOrderCode()
    {
        $maxId = (int) $this->query()->max('id');
        do {
            $maxId++;
            $code = str_pad($maxId, 4, '0', STR_PAD_LEFT);
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}
