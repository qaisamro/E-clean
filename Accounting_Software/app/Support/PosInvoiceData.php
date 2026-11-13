<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Setting;

class PosInvoiceData
{
    public static function make(?Order $order): array
    {
        $order?->loadMissing(['orderDetails.service:id,name', 'orderDetails.item:id,name', 'orderProducts.product:id,name,unit', 'customer', 'user', 'payments']);

        $webLogo = Setting::cachedSettingByKey('web-logo')?->img_url;
        $storeName = 'شركة النخبة للتنظيف';
        $logo = ($webLogo && $webLogo !== '') ? $webLogo : asset('assets/img/logo.webp');

        return [
            'vendor' => [
                'name' => $storeName,
                'address' => null,
                'phone' => null,
                'email' => null,
                'logo' => $logo,
            ],
            'currency' => config('app.currency', '₪'),
            'order' => $order ? self::orderPayload($order) : null,
        ];
    }

    protected static function orderPayload(Order $order): array
    {
        $paid = number_format((float) $order->paid_money, 2);
        $remaining = number_format((float) $order->remaining_money, 2);
        $paidRemainingLine = "المدفوع: {$paid} ₪  ·  المتبقي (دين): {$remaining} ₪";

        $notes = collect([$order->notes, $paidRemainingLine])
            ->filter(fn ($line) => filled($line))
            ->implode("\n");

        return [
            'id' => $order->id,
            'code' => $order->order_code,
            'invoice_number' => $order->order_code,
            'date' => $order->created_at?->format('Y-m-d'),
            'time' => $order->created_at?->format('H:i'),
            'customer_name' => $order->customer?->name,
            'customer_phone' => $order->customer?->phone,
            // This is the account recorded when the order was created, not
            // the account currently viewing or printing the invoice.
            'cashier' => $order->user?->name ?? 'حساب المنشئ غير محدد',
            'subtotal' => (float) $order->orderDetails->sum('total_price') + (float) $order->orderProducts->sum('total_price'),
            'discount' => (float) $order->discount,
            'delivery' => null,
            'tax' => null,
            'total' => (float) $order->total_price,
            'payment_method' => $order->readable_payment_type,
            'payment_status' => $order->readable_payment_status,
            'payment_status_raw' => $order->payment_status?->value,
            'order_notes' => $notes,
            'items' => $order->orderDetails->map(fn ($detail) => [
                'name' => $detail->item?->name,
                'service' => $detail->service?->name,
                'quantity' => (float) $detail->quantity,
                'unit_price' => $detail->price !== null ? (float) $detail->price : null,
                'discount' => null,
                'total' => $detail->total_price !== null ? (float) $detail->total_price : null,
            ])->concat($order->orderProducts->map(fn ($op) => [
                'name' => $op->product?->name,
                'service' => 'منتج — للكاشير',
                'quantity' => (float) $op->quantity,
                'unit_price' => (float) $op->price,
                'discount' => null,
                'total' => (float) $op->total_price,
            ]))->values()->all(),
            'extras' => [],
        ];
    }
}
