<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use DomainException;

/**
 * Keeps the order balance as a derived value of its saved lines and payment
 * ledger.  No screen is allowed to set paid_money or payment_status directly.
 */
class OrderFinancials
{
    private const EPSILON = 0.01;

    public function reconcile(Order $order): Order
    {
        $order = Order::query()->lockForUpdate()->findOrFail($order->id);

        $serviceSubtotal = (float) $order->orderDetails()->sum('total_price');
        $productsSubtotal = (float) $order->orderProducts()->sum('total_price');
        $recordedPayments = round((float) $order->payments()->sum('amount'), 2);
        $financials = $this->calculate($serviceSubtotal, $productsSubtotal, (float) $order->discount, $recordedPayments);
        $storedPaid = round((float) $order->paid_money, 2);

        // A historical mismatch needs an explicit reconciliation, never a
        // silent overwrite caused by editing a delivery date or a line item.
        if (abs($storedPaid - $recordedPayments) > self::EPSILON) {
            throw new DomainException('تعذر حفظ الطلب لأن رصيده لا يطابق سندات الدفع المسجلة. راجع السجل المالي أولاً.');
        }

        $order->update([
            'total_price' => $financials['total'],
            'paid_money' => $recordedPayments,
            'remaining_money' => $financials['remaining'],
            'payment_status' => $financials['status'],
        ]);

        return $order->fresh();
    }

    /**
     * Pure calculation used by reconciliation and its tests. Values are
     * rounded once, at the accounting boundary.
     *
     * @return array{total: float, remaining: float, status: PaymentStatus}
     */
    public function calculate(float $serviceSubtotal, float $productsSubtotal, float $discount, float $payments): array
    {
        $subtotal = round($serviceSubtotal + $productsSubtotal, 2);
        $discount = round($discount, 2);
        $payments = round($payments, 2);

        if ($serviceSubtotal < 0 || $productsSubtotal < 0 || $discount < 0 || $discount > $subtotal + self::EPSILON) {
            throw new DomainException('الخصم يجب أن يكون بين صفر وإجمالي بنود الطلب.');
        }

        $total = round($subtotal - $discount, 2);
        if ($payments < 0 || $payments > $total + self::EPSILON) {
            throw new DomainException('لا يمكن أن تتجاوز الدفعات المسجلة إجمالي الطلب.');
        }

        $remaining = round(max(0, $total - $payments), 2);
        $status = $remaining <= self::EPSILON
            ? PaymentStatus::PAID
            : ($payments > self::EPSILON ? PaymentStatus::PARTIALLY_PAID : PaymentStatus::UNPAID);

        return compact('total', 'remaining', 'status');
    }
}
