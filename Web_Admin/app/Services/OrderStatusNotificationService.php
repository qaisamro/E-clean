<?php

namespace App\Services;

use App\Models\NotificationManage;
use App\Models\Order;
use App\Repositories\NotificationRepository;

class OrderStatusNotificationService
{
    private const TEMPLATE_NAMES = [
        'pending' => 'new_order',
        'order_confirmed' => 'order_confirmed',
        'picked_order' => 'order_picked',
        'processing' => 'order_processing',
        'cancelled' => 'order_cancelled',
        'delivered' => 'order_delivered',
        'ready_for_pickup' => 'order_ready_for_pickup',
        'on_the_way' => 'order_on_the_way',
    ];

    public function notify(Order $order, string $statusKey): array
    {
        $order->loadMissing(['customer.user', 'customer.devices']);
        $customer = $order->customer;

        if (! $customer) {
            return ['status' => 'skipped', 'reason' => 'missing_customer'];
        }

        $templateName = self::TEMPLATE_NAMES[$statusKey] ?? $statusKey;
        $template = NotificationManage::query()->where('name', $templateName)->first();

        // An explicitly disabled template remains disabled. If an installation
        // has no row for a newer status, use a readable fallback instead.
        if ($template && ! $template->is_active) {
            return ['status' => 'skipped', 'reason' => 'disabled'];
        }

        $status = config('enums.order_status.'.$statusKey, $order->order_status);
        $customerName = trim((string) ($customer->user?->name ?? $customer->name ?? ''));
        $title = trim((string) ($template?->title ?: 'تحديث حالة الطلب'));
        $message = trim((string) ($template?->message ?: $this->fallbackMessage(
            $customerName,
            (string) $status,
            (string) ($order->order_code ?: $order->id)
        )));

        // Always persist the notification in the customer's inbox. Push is an
        // additional delivery channel and must not be required for visibility.
        (new NotificationRepository())->storeByRequest($customer->id, $message, $title);

        $tokens = $customer->devices
            ->pluck('key')
            ->filter(fn ($token) => is_string($token) && trim($token) !== '')
            ->unique()
            ->values()
            ->all();

        $push = $tokens === []
            ? ['status' => 'skipped', 'reason' => 'missing_device_token']
            : (new NotificationServices())->sendNotification($message, $tokens, $title);

        return [
            'status' => 'stored',
            'notification' => ['title' => $title, 'message' => $message],
            'push' => $push,
        ];
    }

    private function fallbackMessage(string $customerName, string $status, string $orderCode): string
    {
        $greeting = $customerName !== '' ? "مرحبًا {$customerName}، " : '';

        return $greeting.'تم تحديث حالة طلبك إلى '.__($status).". رقم الطلب: {$orderCode}";
    }
}
