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

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = config('enums.order_status.' . request('status'));
        $pageNo = $request->page ?? null;
        $perPage = $request->per_page ?? null;

        $orders = (new OrderRepository())->orderListByStatus($status, $pageNo, $perPage);

        return $this->json('customer order list', [
            'orders' => OrderResource::collection($orders),
            'total' => $orders->total(),

        ]);
    }

    public function store(OrderRequest $request)
    {

        $pickTime = (new OrderRepository())->setPickOrDeliveryTime($request->pick_date, $request->pick_hour);

        $deliveryTime = (new OrderRepository())->setPickOrDeliveryTime($request->delivery_date, $request->delivery_hour, 'delivery');

        if ($pickTime != null && $deliveryTime != null) {

            $order = (new OrderRepository())->storeByRequest($request);
            $transaction = (new TransationRepository())->storeForOrder($order);

            if ($request->has('additional_service_id')) {
                $order->additionals()->sync($request->additional_service_id);
            }

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
        $order = (new OrderRepository())->findById($id);
        return $this->json('order details', [
            'order' => new OrderResource($order)
        ]);
    }

    public function pickSchedule($date)
    {
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
                    'title' => sprintf('%02s', $i) . ':00' . ' - ' . sprintf('%02s', $i + 1) . ':59'
                ];
            }
        }

        $hours = collect($hours);
        return $this->json('Delivery scheduls', [
            'schedules' => ScheduleResource::collection($hours)
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'products' => 'required|array'
        ]);

        if ($order->order_status == 'Pending') {
            (new OrderRepository())->updateByRequest($request, $order);
            return $this->json('Order is edited successful.');
        }

        return $this->json('Sorry, You can\'t edit this order');
    }

    public function newOrder()
    {
        $orders = (new OrderRepository())->query()->where('is_show', false)->latest('id')->get();
        return $this->json('New Order', [
            'orders' => OrderResource::collection($orders)
        ]);
    }
}
