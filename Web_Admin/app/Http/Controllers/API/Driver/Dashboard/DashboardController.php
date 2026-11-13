<?php

namespace App\Http\Controllers\API\Driver\Dashboard;

use App\Events\OrderMailEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\DriverRepository;
use App\Repositories\OrderRepository;
use App\Services\OrderStatusNotificationService;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    public function today()
    {
        $orders = (new DriverRepository())->getTodaysOrder();

        return $this->json('Todays orders list', [
            'orders' => OrderResource::collection($orders)
        ]);
    }

    public function todayPending()
    {
        $orders = (new DriverRepository())->getTodaysTotalPending();

        return $this->json('Today pending orders', [
            'total' => count($orders),
            'orders' => OrderResource::collection($orders)
        ]);
    }

    public function todayOrders()
    {
        $status = \request()->status;
        $orders = (new DriverRepository())->getTodaysOrderByRequest($status);

        return $this->json('Today orders', [
            'total' => count($orders),
            'orders' => OrderResource::collection($orders)
        ]);
    }


    public function orderDetails($id)
    {
        $order = (new OrderRepository())->findById($id);

        // Drivers may only view orders assigned to them (their own store)
        if (!$this->driverOwnsOrder($order?->id, true)) {
            abort(404);
        }

        return $this->json('order details', [
            'order' => (new OrderResource($order))
        ]);
    }

    public function statusUpdate(Order $order)
    {
        // Drivers may only update orders assigned to them (their own store)
        if (!$this->driverOwnsOrder($order->id, false)) {
            return $this->json('هذا الطلب غير معيّن لك', [], Response::HTTP_FORBIDDEN);
        }
        $status = config('enums.order_status.' . request('status'));

        if (!in_array($status, config('enums.order_status'))) {
            return $this->json('حالة غير صالحة', [], Response::HTTP_BAD_REQUEST);
        }
        $order = (new OrderRepository())->StatusUpdateByRequest($order, $status);

        app(OrderStatusNotificationService::class)->notify($order, (string) request('status'));

        if ($order->customer->user->email) {
            OrderMailEvent::dispatch($order);
        }

        return $this->json('Order status is updated successfully', [
            'order' => (new OrderResource($order))
        ]);
    }

    public function thisWeek()
    {
        $orders = (new DriverRepository())->getThisWeekDelivery();
        return $this->json('Orders list', [
            'total' => count($orders),
            'orders' => OrderResource::collection($orders)
        ]);
    }

    public function lastWeek()
    {
        $orders = (new DriverRepository())->getLastWeek();
        return $this->json('Orders list', [
            'total' => count($orders),
            'orders' => OrderResource::collection($orders)
        ]);
    }

    public function totalOrder()
    {
        $orders = (new DriverRepository())->getTotalOrder();
        return $this->json('Orders list', [
            'total' => count($orders),
            'orders' => OrderResource::collection($orders)
        ]);
    }

    public function acceptOrder(Order $order)
    {
        $auth = auth()->user()->driver;
        // Drivers may only accept orders actually assigned to them
        if (!$auth || !$order->drivers()->where('drivers.id', $auth->id)->exists()) {
            return $this->json('هذا الطلب غير معيّن لك', [], Response::HTTP_FORBIDDEN);
        }
        if (\request()->isAccept == 1) {
            $order = $order->drivers()->updateExistingPivot($auth->id,['is_accept' => 1]);
            return $this->json('Order accepted successfully');
        }

        $order->drivers()->detach($auth->id);
        return $this->json('Order deleted');

    }

    public function history()
    {
        $driver = auth()->user()?->driver;
        if (!$driver || !$driver->vendor_id) {
            $orders = collect();
        } else {
            $orders = $driver->orderHistories;
        }
        return $this->json('Order histories list', [
            'total' => count($orders),
            'orders' => OrderResource::collection($orders)
        ]);
    }

    /**
     * Whether the authenticated driver is assigned to the given order
     * (directly or via history) — i.e. it belongs to their own store.
     */
    protected function driverOwnsOrder($orderId, bool $allowHistory = false): bool
    {
        $driver = auth()->user()?->driver;
        if (!$driver || !$driver->vendor_id || !$driver->is_approve
            || !auth()->user()->is_active || !$orderId) {
            return false;
        }
        if ($driver->orders()->where('orders.id', $orderId)->exists()) {
            return true;
        }
        return $allowHistory
            && $driver->orderHistories()->where('orders.id', $orderId)->exists();
    }
}
