<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;

class CustomerStatementController extends Controller
{
    public function show(Request $request, Customer $customer)
    {
        return view('customer.statement', $this->statementData($request, $customer));
    }

    public function print(Request $request, Customer $customer)
    {
        return view('customer.statement-print', $this->statementData($request, $customer));
    }

    private function statementData(Request $request, Customer $customer): array
    {
        $from = $request->filled('from') ? $request->date('from')->startOfDay() : null;
        $to = $request->filled('to') ? $request->date('to')->endOfDay() : null;

        $orders = $customer->orders()
            ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('created_at', '<=', $to))
            ->get(['id', 'order_code', 'total_price', 'created_at']);

        $payments = Payment::query()
            ->whereHas('order', fn ($query) => $query->where('customer_id', $customer->id))
            ->with('order:id,order_code')
            ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('created_at', '<=', $to))
            ->get(['id', 'order_id', 'amount', 'payment_method', 'notes', 'created_at']);

        $opening = 0.0;
        if ($from) {
            $openingOrders = (float) $customer->orders()->where('created_at', '<', $from)->sum('total_price');
            $openingPayments = (float) Payment::whereHas('order', fn ($query) => $query->where('customer_id', $customer->id))
                ->where('created_at', '<', $from)->sum('amount');
            $opening = $openingOrders - $openingPayments;
        }

        $rows = collect();
        foreach ($orders as $order) {
            $rows->push(['date' => $order->created_at, 'type' => 'order', 'reference' => $order->order_code, 'description' => 'طلب', 'debit' => (float) $order->total_price, 'credit' => 0.0]);
        }
        foreach ($payments as $payment) {
            $method = $payment->payment_method === 'visa' ? 'فيزا' : 'نقدي';
            $rows->push(['date' => $payment->created_at, 'type' => 'payment', 'reference' => $payment->order?->order_code ?? ('#' . $payment->order_id), 'description' => 'دفعة ' . $method . ($payment->notes ? ' — ' . $payment->notes : ''), 'debit' => 0.0, 'credit' => (float) $payment->amount]);
        }

        $balance = $opening;
        $rows = $rows->sortBy(fn (array $row) => [$row['date']->timestamp, $row['type'] === 'order' ? 0 : 1])
            ->values()
            ->map(function (array $row) use (&$balance) {
                $balance += $row['debit'] - $row['credit'];
                $row['balance'] = $balance;
                return $row;
            });

        return [
            'customer' => $customer,
            'rows' => $rows,
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'opening' => $opening,
            'periodDebit' => (float) $orders->sum('total_price'),
            'periodCredit' => (float) $payments->sum('amount'),
            'currentDue' => (float) $customer->orders()->where('remaining_money', '>', 0)->sum('remaining_money'),
        ];
    }
}
