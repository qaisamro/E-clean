<?php

namespace App\Http\Controllers\API\Order;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Repositories\OrderRepository;
use App\Events\OrderNotificationEvent;
use App\Services\NotificationServices;
use App\Http\Resources\ScheduleResource;
use App\Models\NotificationManage;
use App\Repositories\ScheduleRepository;
use App\Repositories\TransationRepository;
use App\Models\PaymentGateway;
use App\Repositories\PaymentRepository;
use App\Support\OrderVendorOwnership;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = config('enums.order_status.' . request('status'));
        $pageNo = $request->page ?? null;
        $perPage = $request->per_page ?? null;

        $orders = (new OrderRepository())->orderListByStatus($pageNo, $perPage, $status);

        return $this->json('customer order list', [
            'orders' => OrderResource::collection($orders),
            'total' => $orders->total(),

        ]);
    }

    public function store(OrderRequest $request)
    {
        // Ownership is derived from the selected product/service relations;
        // vendor_id supplied by a client is only a consistency check.
        $vendorId = OrderVendorOwnership::resolveProductVendor(
            collect($request->products)->pluck('id')->all(),
            $request->filled('vendor_id') ? (int) $request->vendor_id : null
        );

        $appointmentsEnabled = $vendorId && app(\App\Services\VendorAppointments::class)->settings($vendorId)['enabled'];
        $pickTime = $appointmentsEnabled ? true : (new OrderRepository())->setPickOrDeliveryTime($request->pick_date, $request->pick_hour);

        $deliveryTime = $appointmentsEnabled ? true : (new OrderRepository())->setPickOrDeliveryTime($request->delivery_date, $request->delivery_hour, 'delivery');

        if ($appointmentsEnabled || ($pickTime != null && $deliveryTime != null)) {

            $order = (new OrderRepository())->storeByRequest($request);
            $transaction = (new TransationRepository())->storeForOrder($order);

            try{
                $message  = 'New order add from ' . $order->customer->name;
                OrderNotificationEvent::dispatch($message);
            } catch (\Exception $e) {}

            $notificationOrder = NotificationManage::where('name', 'new_order')->first();

            if ($notificationOrder?->is_active) {
                $keys = $order->customer->devices->pluck('key')->toArray();

                $message = $notificationOrder->message;
            }

            if ($order->payment_type != 'cash') {
                // $paymentUrl = route('order.payment', ['order' => $order->id, 'gateway' => $order->payment_type, 'status' => $order->payment_status]);

                $paymentUrl =(new PaymentRepository())->{$order->payment_type . "PaymentURL"}([
                    'currency' => 'GBP',
                    'amount' => $order->total_amount
                ], $order->id);

                return $this->json('Order Successfully', [
                    'message' => 'Order is added successfully',
                    'payment_url' => $paymentUrl,
                    'payment_type' => $order->payment_type,
                    'orders' => OrderResource::make($order),
                ]);
            }

            return $this->json('Order Successfully', [
                'message' => 'Order is added successfully',
                'payment_url' => null,
                'payment_type' => $order->payment_type,
                'orders' => OrderResource::make($order),
            ]);
        }

        return $this->json('pick up slot or delivery slot not available', [], Response::HTTP_BAD_REQUEST);
    }

    public function show($id)
    {
        $customerId = auth()->user()->customer?->id;
        abort_unless($customerId, 403);

        $order = Order::query()
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return $this->json('order details', [
            'order' => new OrderResource($order)
        ]);
    }

    /**
     * Cancel own order while it is still awaiting confirmation.
     * The window closes as soon as the store accepts the order.
     */
    public function cancel(Request $request, Order $order)
    {
        $customerId = auth()->user()->customer?->id;
        abort_unless($customerId, 403);
        abort_unless((int) $order->customer_id === (int) $customerId, 404);

        $status = strtolower(trim((string) $order->order_status));
        if ($status !== 'pending') {
            return $this->json('لا يمكن إلغاء الطلب بعد تأكيد المتجر له', [], Response::HTTP_FORBIDDEN);
        }

        $order->update(['order_status' => 'Cancelled']);

        return $this->json('Order cancelled', [
            'message' => 'تم إلغاء الطلب بنجاح',
            'order_status' => 'Cancelled',
        ]);
    }

    /**
     * Edit delivery details (address / date / hour / instruction) while
     * the order is still pending confirmation. Changing the product list
     * is handled by PUT /orders/{order} (products only).
     */
    public function customerEdit(Request $request, Order $order)
    {
        $customerId = auth()->user()->customer?->id;
        abort_unless($customerId, 403);
        abort_unless((int) $order->customer_id === (int) $customerId, 404);

        $status = strtolower(trim((string) $order->order_status));
        if ($status !== 'pending') {
            return $this->json('لا يمكن تعديل الطلب بعد تأكيد المتجر له', [], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'address_id' => 'nullable|integer|exists:addresses,id',
            'delivery_date' => 'nullable|date_format:Y-m-d',
            'delivery_hour' => 'nullable|string|max:10',
            'instruction' => 'nullable|string|max:500',
        ]);

        if (!empty($validated['address_id'])) {
            $address = \App\Models\Address::withTrashed()
                ->whereKey($validated['address_id'])
                ->where('customer_id', $customerId)
                ->first();
            abort_unless($address, 403, 'Address does not belong to you');
            // A trashed address may still own legacy orders, but it must not
            // be re-selected as the delivery target.
            if (method_exists($address, 'trashed') && $address->trashed()) {
                return $this->json('لا يمكن اختيار عنوان محذوف — أضف عنواناً جديداً.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $hour = null;
        if (!empty($validated['delivery_hour'])) {
            $hour = $this->normalizeHour($validated['delivery_hour']);
        }

        $order->update(array_filter([
            'address_id' => $validated['address_id'] ?? null,
            'delivery_date' => $validated['delivery_date'] ?? null,
            'delivery_hour' => $hour,
            'instruction' => $validated['instruction'] ?? null,
        ], fn ($value) => $value !== null));

        return $this->json('Order updated', [
            'message' => 'تم تحديث بيانات الطلب بنجاح',
            'order' => new OrderResource($order->fresh()),
        ]);
    }

    /**
     * Accept "H:i" or "H:i:s" and return "H:i:s".
     */
    private function normalizeHour(string $hour): string
    {
        $hour = trim($hour);
        if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)/', $hour, $m)) {
            return $m[1] . ':' . $m[2] . ':00';
        }

        return $hour;
    }

    /**
     * Normalize a schedule date coming from the mobile app.
     * iOS with an Arabic locale sends Eastern Arabic numerals
     * (٢٠٢٦-١٠-٠٩) which Carbon cannot parse — convert them to
     * Latin digits and strictly validate Y-m-d. Returns null when
     * the input is unusable so callers answer 422 instead of 500.
     */
    protected function normalizeScheduleDate($date): ?string
    {
        $date = trim((string) $date);
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $latin = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $date = str_replace($eastern, $latin, $date);
        $date = str_replace('/', '-', $date);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return null;
        }
        try {
            return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function pickSchedule($date)
    {
        if (request()->filled('vendor_id')) {
            return $this->vendorSchedule($date, 'pickup');
        }
        $date = $this->normalizeScheduleDate($date);
        if (!$date) {
            return $this->json('Invalid date', ['schedules' => []], 422);
        }
        $day = Carbon::parse($date)->format('l');
        $schedule = (new ScheduleRepository())->findByDay($day, 'pickup');

        if (!$schedule) {
            return $this->json('Sorry, Our service is not abailable', [
                'schedules' => [],
            ]);
        }

        $orders = (new OrderRepository())->getByDatePickOrDelivery($date);
        $hours = [];

        $today = date('Y-m-d');
        if ($today == $date) {
            $time = date('G');
            $i = $time % 2 == 0  ? $time : $time + 1;
        } else {
            $i = $schedule->start_time;
        }

        for ($i; $i < ($schedule->end_time - 1); $i += 2) {
            $per = 0;
            foreach ($orders as $order) {
                $hour = Carbon::parse($order->pick_hour)->format('H');
                if ($i == $hour) {
                    $per++;
                }
            }

            if ($per < ($schedule->per_hour * 2)) {
                $hours[] = [
                    'hour' => (string) $i . '-' . (string) ($i + 1),
                    'title' => sprintf('%02s', $i) . ':00' . ' - ' . sprintf('%02s', $i + 1) . ':59',
                    'is_booked' => false,
                ];
            } else {
                $hours[] = [
                    'hour' => (string) $i . '-' . (string) ($i + 1),
                    'title' => sprintf('%02s', $i) . ':00' . ' - ' . sprintf('%02s', $i + 1) . ':59',
                    'is_booked' => true,
                ];
            }
        }

        $hours = collect($hours);
        return $this->json('picked scheduls', [
            'schedules' => ScheduleResource::collection($hours)
        ]);
    }

    public function deliverySchedule($date)
    {
        if (request()->filled('vendor_id')) {
            return $this->vendorSchedule($date, 'delivery');
        }
        $date = $this->normalizeScheduleDate($date);
        if (!$date) {
            return $this->json('Invalid date', ['schedules' => []], 422);
        }
        $day = Carbon::parse($date)->format('l');
        $schedule = (new ScheduleRepository())->findByDay($day, 'delivery');

        if (!$schedule) {
            return $this->json('Sorry, Our service is not abailable', [
                'schedules' => [],
            ]);
        }

        $orders = (new OrderRepository())->getByDatePickOrDelivery($date, 'delivery');

        $hours = [];
        for ($i = $schedule->start_time; $i < ($schedule->end_time - 1); $i += 2) {
            $per = 0;
            foreach ($orders as $order) {
                $hour = Carbon::parse($order->delivery_hour)->format('H');
                if ($i == $hour) {
                    $per++;
                }
            }

if ($per < ($schedule->per_hour * 2)) {
                $hours[] = [
                    'hour' => (string) $i . '-' . (string) ($i + 1),
                    'title' => sprintf('%02s', $i) . ':00' . ' - ' . sprintf('%02s', $i + 1) . ':59',
                    'is_booked' => false,
                ];
            } else {
                $hours[] = [
                    'hour' => (string) $i . '-' . (string) ($i + 1),
                    'title' => sprintf('%02s', $i) . ':00' . ' - ' . sprintf('%02s', $i + 1) . ':59',
                    'is_booked' => true,
                ];
            }
        }

$hours = collect($hours);
        return $this->json('Delivery scheduls', [
            'schedules' => ScheduleResource::collection($hours)
        ]);
    }

    private function vendorSchedule($date, string $type)
    {
        request()->validate(['vendor_id' => 'required|integer|exists:vendors,id',
            'pick_date' => 'nullable|required_with:pick_hour|date_format:Y-m-d',
            'pick_hour' => 'nullable|required_with:pick_date|date_format:H:i']);
        $vendor = \App\Models\Vendor::findOrFail(request('vendor_id'));
        abort_unless($vendor->is_active, 404);
        $service = app(\App\Services\VendorAppointments::class);
        if (!$service->settings($vendor->id)['enabled']) {
            // Preserve the pre-existing global API when the store has not opted in.
            request()->query->remove('vendor_id');
            request()->request->remove('vendor_id');
            return $type === 'pickup' ? $this->pickSchedule($date) : $this->deliverySchedule($date);
        }
        $date = $this->normalizeScheduleDate($date);
        if (!$date) return $this->json('Invalid date', ['schedules' => []], 422);
        $data = $service->availability($vendor->id, $date, request('pick_date'), request('pick_hour'));
        return $this->json('Store schedules', ['schedules' => $data['days'][0][$type], 'timezone' => $data['timezone']]);
    }

    public function update(Request $request, Order $order)
    {
        $customerId = $request->user()->customer?->id;
        abort_unless($customerId, 403);

        abort_unless((int) $order->customer_id === (int) $customerId, 404);

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($order->order_status == 'Pending') {
            OrderVendorOwnership::assertProductsMatchOrder(
                $order,
                collect($request->products)->pluck('id')->all()
            );
            (new OrderRepository())->updateByRequest($request, $order);
            return $this->json('Order is edited successful.');
        }

        return $this->json('Sorry, You can\'t edit this order');
    }

    public function newOrder()
    {
        $orders = (new OrderRepository())->query()->where('is_show', false)
            ->with('products.service');
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $vendorId = \App\Support\VendorAccount::idFor(auth()->user());
            $vendorId
                ? $orders->where('vendor_id', $vendorId)
                : $orders->whereRaw('1 = 0');
        }
        $orders = $orders->latest('id')->get();
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $orders = $orders
                ->filter(fn (Order $order) => OrderVendorOwnership::productsMatchOrder($order))
                ->values();
        }
        return $this->json('New Order', [
            'orders' => OrderResource::collection($orders)
        ]);
    }
}
