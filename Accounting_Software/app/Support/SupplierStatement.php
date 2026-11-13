<?php

namespace App\Support;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Support\Collection;

/**
 * Builds a chronological running-balance statement for a supplier:
 * opening balance → purchase bills (debit) → payments (credit).
 *
 * Positive running balance means: نحن مدينون للمورد.
 */
class SupplierStatement
{
    /** @return array<string, mixed> */
    public static function build(Supplier $supplier): array
    {
        $rows = new Collection();

        foreach ($supplier->bills()->orderBy('bill_date')->orderBy('id')->get(['id', 'bill_number', 'bill_date', 'total', 'status', 'notes']) as $bill) {
            $rows->push([
                'date' => $bill->bill_date,
                'type' => 'bill',
                'title' => 'فاتورة شراء #' . $bill->id . ($bill->bill_number ? ' — ' . $bill->bill_number : ''),
                'reference_id' => $bill->id,
                'debit' => (float) $bill->total,   // increases what we owe
                'credit' => 0.0,                    // decreases what we owe
                'notes' => $bill->notes,
            ]);
        }

        foreach ($supplier->payments()->orderBy('payment_date')->orderBy('id')->get(['id', 'payment_date', 'amount', 'method', 'bill_id', 'notes']) as $payment) {
            $rows->push([
                'date' => $payment->payment_date,
                'type' => 'payment',
                'title' => 'دفعة #' . $payment->id . ($payment->bill_id ? ' — لفاتورة #' . $payment->bill_id : ' (على الحساب)'),
                'reference_id' => $payment->id,
                'debit' => 0.0,
                'credit' => (float) $payment->amount,
                'notes' => $payment->notes . (in_array($payment->method, ['bank', 'cheque', 'other'], true) ? ' (وسائل: ' . (SupplierPayment::METHODS[$payment->method] ?? $payment->method) . ')' : ''),
            ]);
        }

        $rows = $rows->sortBy(['date', 'reference_id'])->values();

        $opening = (float) $supplier->opening_balance;
        $running = $opening;
        $statement = [];

        if (abs($opening) > 0.0001) {
            $statement[] = [
                'date' => null,
                'type' => 'opening',
                'title' => 'الرصيد الافتتاحي',
                'debit' => $opening > 0 ? $opening : 0,
                'credit' => $opening < 0 ? abs($opening) : 0,
                'balance_after' => $opening,
                'notes' => 'رصيد سابق قبل البدء باستعمال النظام',
            ];
        }

        foreach ($rows as $row) {
            $running += $row['debit'] - $row['credit'];
            $statement[] = [
                'date' => $row['date'],
                'type' => $row['type'],
                'title' => $row['title'],
                'debit' => $row['debit'],
                'credit' => $row['credit'],
                'balance_after' => $running,
                'notes' => $row['notes'],
            ];
        }

        $billsTotal = $rows->where('type', 'bill')->sum('debit');
        $paymentsTotal = $rows->where('type', 'payment')->sum('credit');

        return [
            'opening' => $opening,
            'bills_total' => (float) $billsTotal,
            'payments_total' => (float) $paymentsTotal,
            'balance' => $running,
            'bills_count' => $rows->where('type', 'bill')->count(),
            'payments_count' => $rows->where('type', 'payment')->count(),
            'rows' => $statement,
        ];
    }
}
