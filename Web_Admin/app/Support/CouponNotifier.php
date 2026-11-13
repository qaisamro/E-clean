<?php

namespace App\Support;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\DeviceKey;
use App\Models\Notification;
use App\Services\NotificationServices;

/**
 * Shared "notify customers about this coupon" pipeline used by both the
 * Super Admin global coupons and the vendor-scoped coupons.
 *
 * It always:
 *  - pushes a Firebase notification to every registered device key;
 *  - inserts an in-app notifications row for every customer so the
 *    notification appears inside the app's notification list;
 *  - includes the code, discount amount/percent and minimum order.
 */
class CouponNotifier
{
    /**
     * @return array<string, mixed>  small report for the UI alert
     */
    public static function notify(Coupon $coupon): array
    {
        $isGlobal = $coupon->vendor_id === null;
        $storeName = $isGlobal
            ? null
            : (string) ($coupon->vendor?->name ?: 'متجرنا');

        $discountLabel = $coupon->discount_type === 'percent'
            ? rtrim(rtrim(number_format((float) $coupon->discount, 2), '0'), '.') . '٪'
            : currencyPosition((float) $coupon->discount);
        $minLabel = currencyPosition((float) $coupon->min_amount);
        $code = (string) $coupon->code;

        if ($isGlobal) {
            $message = sprintf(
                'كوبون خصم بـ %s — الحد الأدنى للطلب %s — كود: %s — يعمل في جميع المتاجر',
                $discountLabel,
                $minLabel,
                $code
            );
            $title = 'كوبون خصم عام';
        } else {
            $message = sprintf(
                'كوبون خصم بـ %s — الحد الأدنى للطلب %s — كود: %s — من %s',
                $discountLabel,
                $minLabel,
                $code,
                $storeName
            );
            $title = 'عرض خاص من ' . $storeName;
        }

        $keys = DeviceKey::get()
            ->pluck('key')
            ->filter()
            ->values()
            ->toArray();
        $deviceResult = [
            'status' => empty($keys) ? 'no_recipients' : null,
            'sent' => 0,
            'failed' => 0,
            'message' => null,
            'recipients' => count($keys),
        ];

        if (!empty($keys)) {
            $send = app(NotificationServices::class)->sendNotification($message, $keys, $title);
            $deviceResult['status'] = $send['status'] ?? null;
            $deviceResult['sent'] = (int) ($send['success'] ?? 0);
            $deviceResult['failed'] = (int) ($send['failed'] ?? 0);
            $deviceResult['message'] = $send['message'] ?? null;
        }

        // In-app notifications for every registered customer.
        $customerIds = Customer::pluck('id');
        $now = now();
        $inserted = 0;
        foreach ($customerIds->chunk(200) as $chunk) {
            $rows = [];
            foreach ($chunk as $customerId) {
                $rows[] = [
                    'customer_id' => $customerId,
                    'vendor_id' => $isGlobal ? null : $coupon->vendor_id,
                    'title' => $title,
                    'message' => $message,
                    'isRead' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if ($rows !== []) {
                Notification::insert($rows);
                $inserted += count($rows);
            }
        }

        return [
            'requested' => true,
            'recipients' => count($keys),
            'store' => $storeName,
            'scope' => $isGlobal ? 'global' : 'vendor',
            'status' => $deviceResult['status'],
            'sent' => $deviceResult['sent'],
            'failed' => $deviceResult['failed'],
            'message' => $deviceResult['message'],
            'in_app_inserted' => $inserted,
            'in_app_total_customers' => $customerIds->count(),
            'coupon' => $code,
        ];
    }
}
