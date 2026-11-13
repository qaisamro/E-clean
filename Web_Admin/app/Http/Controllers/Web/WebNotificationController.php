<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CustomerJoinRequest;
use App\Models\DriverNotification;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WebNotificationController extends Controller
{
    private const LIMIT = 20;

    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user && $this->supportsNotifications($user), 403);

        $items = collect();

        if ($this->isSuperAdmin($user)) {
            // Super admins receive account-approval events only. New orders
            // belong exclusively to their vendor owner and must not appear in
            // the global administration notification feed.
            $items = $this->joinRequestItems();
        } elseif (\App\Support\VendorAccount::isStoreStaff($user)) {
            $vendorId = \App\Support\VendorAccount::idFor($user);
            if ($vendorId) {
                $items = $this->orderItems(Order::query()->where('vendor_id', $vendorId));
            }
        } elseif ($user->hasRole('driver')) {
            $items = $this->driverItems($user);
        }

        $items = $items
            ->sortByDesc('timestamp')
            ->take(self::LIMIT)
            ->values();

        return response()->json([
            'data' => [
                'items' => $items,
                'count' => $items->count(),
                'polled_at' => now()->toIso8601String(),
            ],
        ]);
    }

    private function orderItems($query): Collection
    {
        return $query
            ->where('is_show', false)
            ->with('customer.user')
            ->latest('id')
            ->limit(self::LIMIT)
            ->get()
            ->map(function (Order $order) {
                $customer = trim((string) ($order->customer?->user?->name ?? ''));
                $code = $order->order_code ?: $order->id;

                return [
                    'key' => 'order:' . $order->id,
                    'type' => 'order',
                    'title' => 'طلب جديد',
                    'message' => $customer !== ''
                        ? "طلب جديد من {$customer}، رقم الطلب: {$code}"
                        : "طلب جديد، رقم الطلب: {$code}",
                    'url' => route('order.show', $order),
                    'timestamp' => optional($order->created_at)->toIso8601String() ?? now()->toIso8601String(),
                    'time' => optional($order->created_at)->diffForHumans(),
                ];
            });
    }

    private function joinRequestItems(): Collection
    {
        return CustomerJoinRequest::query()
            ->pending()
            ->with('user')
            ->latest('id')
            ->limit(self::LIMIT * 2)
            ->get()
            ->filter(fn (CustomerJoinRequest $joinRequest) =>
                ($joinRequest->request_metadata['source'] ?? null) === 'mobile_app'
            )
            ->take(self::LIMIT)
            ->map(function (CustomerJoinRequest $joinRequest) {
                $name = trim((string) ($joinRequest->user?->name ?? ''));

                return [
                    'key' => 'customer-join-request:' . $joinRequest->id,
                    'type' => 'customer_join_request',
                    'title' => 'طلب انضمام جديد',
                    'message' => $name !== ''
                        ? "طلب انضمام جديد من {$name} عبر التطبيق"
                        : 'طلب انضمام جديد عبر التطبيق',
                    'url' => route('customer-join-requests.show', $joinRequest),
                    'timestamp' => optional($joinRequest->created_at)->toIso8601String() ?? now()->toIso8601String(),
                    'time' => optional($joinRequest->created_at)->diffForHumans(),
                ];
            });
    }

    private function driverItems(User $user): Collection
    {
        $driver = $user->driver;
        if (!$driver || !$driver->vendor_id || !$driver->is_approve || !$user->is_active) {
            return collect();
        }

        $assignments = DB::table('driver_orders')
            ->where('driver_id', $driver->id)
            ->latest('updated_at')
            ->limit(self::LIMIT)
            ->get(['order_id', 'updated_at'])
            ->keyBy('order_id');

        $orderItems = $assignments->isEmpty()
            ? collect()
            : Order::query()
                ->whereIn('id', $assignments->keys())
                ->where('vendor_id', $driver->vendor_id)
                ->with('customer.user')
                ->get()
                ->map(function (Order $order) use ($assignments) {
                $assignedAt = $assignments->get($order->id)?->updated_at;
                $customer = trim((string) ($order->customer?->user?->name ?? ''));
                $code = $order->order_code ?: $order->id;

                return [
                    'key' => 'driver-order:' . $order->id . ':' . strtotime((string) $assignedAt),
                    'type' => 'driver_order',
                    'title' => 'تم إسناد طلب إليك',
                    'message' => $customer !== ''
                        ? "الطلب {$code} للعميل {$customer}"
                        : "تم إسناد الطلب {$code} إليك",
                    'url' => route('order.show', $order),
                    'timestamp' => ($assignedAt ? Carbon::parse($assignedAt) : $order->created_at)?->toIso8601String()
                        ?? now()->toIso8601String(),
                    'time' => ($assignedAt ? Carbon::parse($assignedAt) : $order->created_at)?->diffForHumans(),
                ];
            });

        $messageItems = DriverNotification::query()
            ->where('driver_id', $driver->id)
            ->where('isRead', false)
            ->latest('id')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (DriverNotification $notification) => [
                'key' => 'driver-notification:' . $notification->id,
                'type' => 'driver_notification',
                'title' => 'إشعار للسائق',
                'message' => $this->cleanDriverMessage($notification->message),
                'url' => route('driver.account'),
                'timestamp' => optional($notification->created_at)->toIso8601String() ?? now()->toIso8601String(),
                'time' => optional($notification->created_at)->diffForHumans(),
            ]);

        return $orderItems->concat($messageItems);
    }

    /**
     * A driver notification message may have been stored as a raw JSON blob
     * (e.g. when the sender posted a JSON body) or left empty. Decode it and
     * fall back to a readable Arabic string so the web bell never shows
     * a raw `{"message": ""}` fragment to the driver.
     */
    private function cleanDriverMessage($message): string
    {
        $raw = trim((string) $message);

        if (str_starts_with($raw, '{') || str_starts_with($raw, '[')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                foreach (['message', 'body', 'title', 'notification'] as $key) {
                    $value = $decoded[$key] ?? null;
                    if (is_string($value) && trim($value) !== '') {
                        return trim($value);
                    }
                    if (is_array($value) && isset($value['message']) && is_string($value['message']) && trim($value['message']) !== '') {
                        return trim($value['message']);
                    }
                }
                $raw = '';
            }
        }

        return $raw !== '' ? $raw : 'إشعار للسائق';
    }

    private function supportsNotifications(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || \App\Support\VendorAccount::isStoreStaff($user)
            || $user->hasRole('driver');
    }

    private function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('root')
            || $user->hasRole('admin')
            || ($user->hasRole('Admin') && $user->can('root'));
    }
}
