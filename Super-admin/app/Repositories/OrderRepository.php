<?php


namespace App\Repositories;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\Additional;
use App\Models\DeliveryCost;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Enum\PaymentType;
use App\Models\Product;
use Carbon\Carbon;

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

        $lastOrder = $this->query()->latest('id')->first();
        $customer = auth()->user()->customer;
        $getAmount = $this->getAmount($request);

        $order = $this->create([
            'customer_id' => $customer->id,
            'order_code' => $this->generateUniqueOrderCode(),
            'prefix' => 'LM',
            'coupon_id' => $request->coupon_id,
            'discount' => $getAmount['discount'],
            'pick_date' => $request->pick_date,
            'delivery_date' => $request->delivery_date,
            'pick_hour' => $this->setPickOrDeliveryTime($request->pick_date, $request->pick_hour),
            'delivery_hour' => $this->setPickOrDeliveryTime($request->delivery_date, $request->delivery_hour, 'delivery'),
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
            $order->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        return $order;
    }
    public function PosStoreByRequest(Request $request): Order
    {
        $products = $request->products;

        $totalAmount = 0;

        foreach ($products as $product) {
            $totalAmount += ($product['quantity']) * ($product['price']);
        }
        $grandTotal = ($totalAmount + $request->delivery_charge) - $request->discount;

        $order = $this->create([
            'customer_id' => $request->customer_id ?? null,
            'order_code' => $this->generateUniqueOrderCode(),
            'prefix' => 'LM',
            'pick_date' => now()->format('Y-m-d'),
            'pick_hour' => now()->format('H:00:00'),
            'delivery_date' => $request->delivery_date ?? now()->format('Y-m-d'),
            'delivery_hour' => now()->format('H:00:00'),
            'delivery_charge' => (float) $request->delivery_charge,
            'discount' => (float) $request->discount,
            'amount' => (float)$totalAmount,
            'total_amount' => (float)$grandTotal,
            'payment_status' => $request->payment_status ? (config('enums.payment_status.' . strtolower($request->payment_status), $request->payment_status)) : config('enums.payment_status.pending'),
            'payment_type' => $request->payment_id ? $request->payment_id : 'cash',
            'payment_method' => $request->payment_method ? strtolower($request->payment_method) : 'cash',
            'order_status' => OrderStatus::CONFIRM->value,
            'address_id' => $request->address_id ?? 1,
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
        $products = $request->products;

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

    private function resolveProductPrice($productId)
    {
        $product = Product::withTrashed()->find($productId);
        return $product ? (float) ($product->discount_price ?: $product->price) : 0;
    }

    private function resolveProductTitle($productId)
    {
        $product = Product::withTrashed()->find($productId);
        return $product ? $product->name : '';
    }


    public function updateByRequest($request, Order $order)
    {
        $request['coupon_id'] = $order->coupon_id;
        $getAmount = $this->getAmount($request);

        $this->update($order, [
            'discount' => $getAmount['discount'],
            'amount' => $getAmount['subTotal'],
            'total_amount' => $getAmount['total'],
        ]);
        $order->products()->detach($order->products->pluck('id')->toArray());

        foreach ($request->products as $product) {
            $order->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        return $order;
    }

    private function getAmount($request): array
    {


        $totalAmount = 0;
        foreach ($request->products as $item) {
            $product = Product::where('id', $item['id'])->first();
            $price = $product->discount_price ? $product->discount_price : $product->price;
            $totalAmount += (float)$item['quantity'] * $price;
        }

        $totalServiceAmount = 0;
        if ($request->has('additional_service_id')) {
            $totalServiceAmount = Additional::whereIn('id', $request->additional_service_id)->get()->sum('price');
        }

        $total = ($totalAmount + $totalServiceAmount);
        $coupon = (new CouponRepository())->findById($request->coupon_id);
        $couponDiscount = $coupon ? $coupon->calculate($total, $coupon) : 0;

        $deliveryCost = DeliveryCost::first();
        $freeDelivery = $deliveryCost ? $deliveryCost->fee_cost : 0;
        $deliveryCharge = $deliveryCost ? $deliveryCost->cost : 0;

        $total = $total <= $freeDelivery ? $total + $deliveryCharge : $total;
        $total = $total - $couponDiscount;

        return [
            'total' => $total,
            'discount' => $couponDiscount,
            'subTotal' => ($totalAmount + $totalServiceAmount),
            'deliveryCharge' => $deliveryCharge
        ];
    }

    public function getSortedByRequest(Request $request)
    {
        $status = $request->status;
        $searchKey = $request->search;
        $sort = $request->sort;

        $orders = $this->model()::query();

        // للسائق: إظهار فقط الطلبات المعيّنة له (من قبل الأدمن)
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            $driver = auth()->user()->driver;
            if (!$driver) {
                return collect();
            }
            $driverOrderIds = $driver->orders->pluck('id')->merge($driver->orderHistories->pluck('id'))->unique()->filter();
            if ($driverOrderIds->isEmpty()) {
                return collect();
            }
            $orders = $orders->whereIn('id', $driverOrderIds);
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

        if ($sort == 'oldest') {
            return $orders->orderBy('created_at', 'asc')->get();
        }

        return $orders->latest()->get();
    }

    public function orderListByStatus($status = null, $pageNo, $perPage)
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

        if ($drivers && $status == config('enums.order_status.delivered') || $status == config('enums.order_status.picked_order')) {
            foreach ($drivers as $driver) {
                $driver->orderHistories()->attach($driver->pivot->order_id, ['status' => $driver->pivot->status]);
                $order->drivers()->detach($driver->id);
            }
        }
        return $order;
    }

    public function getRevenueReportByBetweenDate($form, $to)
    {
        return  $this->model()::whereBetween('delivery_date', [$form, $to])
            ->where('payment_status', config('enums.payment_status.paid'))
            ->get();
    }


    public function getRevenueReport()
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        $orders = $this->model()::query()->where('payment_status', config('enums.payment_status.paid'));
        if (request()->type == 'month') {
            $orders = $orders->whereMonth('delivery_date', $month)
                ->whereYear('delivery_date', $year);
        } elseif (request()->type  ==  'year') {
            $orders = $orders->whereYear('delivery_date', $year);
        } elseif (request()->type == 'week') {
            $end = now()->format('Y-m-d');
            $start = now()->subWeek()->format('Y-m-d');
            $orders = $orders->whereBetween('delivery_date', [$start, $end]);
        } else {
            $date = now()->format('Y-m-d');
            $orders = $orders->where('delivery_date', $date);
        }
        return  $orders->get();
    }

    /**
     * Dashboard income based on paid orders + period filter (today/week/month/year).
     */
    public function getIncome()
    {
        $query = $this->model()::query()->where('payment_status', config('enums.payment_status.paid'));

        if (request()->type == 'month') {
            $query->whereMonth('delivery_date', now()->format('m'))
                ->whereYear('delivery_date', now()->format('Y'));
        } elseif (request()->type == 'year') {
            $query->whereYear('delivery_date', now()->format('Y'));
        } elseif (request()->type == 'week') {
            $query->whereBetween('delivery_date', [now()->subWeek()->format('Y-m-d'), now()->format('Y-m-d')]);
        } else {
            $query->where('delivery_date', now()->format('Y-m-d'));
        }

        return $query->sum('total_amount');
    }

    /**
     * Build summary statistics for the reports page between two dates.
     */
    public function getReportStats($from, $to)
    {
        $base = $this->model()::query();

        if ($from && $to) {
            $base->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $orders = (clone $base)->get();

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
        ];

        return [
            'total' => $orders->count(),
            'in_progress' => $orders->whereIn('order_status', $inProgressStatuses)->count(),
            'completed' => $orders->where('order_status', config('enums.order_status.delivered'))->count(),
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
