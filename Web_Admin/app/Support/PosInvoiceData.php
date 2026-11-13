<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Vendor;
use App\Models\WebSetting;

class PosInvoiceData
{
    public static function make(Vendor $vendor, ?Order $order): array
    {
        $order?->loadMissing(['products.service', 'customer.user', 'additionals']);
        $attributes = $order?->getAttributes() ?? [];
        return [
            'vendor' => ['name' => $vendor->name, 'address' => $vendor->address, 'phone' => $vendor->phone,
                'email' => $vendor->email, 'logo' => $vendor->logo_media_id ? $vendor->logo_path : null],
            'currency' => WebSetting::first()?->currency ?: config('enums.currency', ''),
            'order' => $order ? [
                'id' => $order->id, 'code' => $order->order_code, 'invoice_number' => $order->order_code,
                'date' => $order->created_at?->format('Y-m-d'), 'time' => $order->created_at?->format('H:i'),
                'delivery_date' => $order->delivery_date?->toDateString(), 'delivery_time' => $order->delivery_hour ?: null,
                'customer_name' => $order->customer?->user?->name, 'customer_phone' => $order->customer?->user?->mobile,
                'cashier' => null, // No persisted cashier association exists in this schema.
                'subtotal' => $order->amount, 'discount' => $order->discount, 'delivery' => $order->delivery_charge,
                'tax' => $attributes['tax_amount'] ?? null, 'total' => $order->total_amount,
                'payment_method' => $order->payment_method_label, 'payment_status' => __($order->payment_status),
                'payment_status_raw' => $order->payment_status,
                'order_notes' => $order->instruction,
                'items' => $order->products->map(fn ($p) => [
                    'name' => $p->pivot->title ?: $p->name, 'service' => $p->service?->name,
                    'quantity' => $p->pivot->quantity, 'unit_price' => $p->pivot->price,
                    'discount' => null, // Catalog discounts are not historical line discounts.
                    'total' => $p->pivot->price !== null ? $p->pivot->quantity * $p->pivot->price : null,
                ])->values()->all(),
                // Legacy extras have no persisted historical price; never borrow today's catalog price.
                'extras' => $order->additionals->pluck('name')->all(),
            ] : null,
        ];
    }
}
