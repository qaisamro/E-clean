<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Salary;
use App\Models\Customer;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());

        $fromDt = \Carbon\Carbon::parse($from)->startOfDay();
        $toDt   = \Carbon\Carbon::parse($to)->endOfDay();

        // ===== الإيرادات (من جدول payments فقط) =====
        $payments = Payment::whereBetween('created_at', [$fromDt, $toDt])
            ->selectRaw("payment_method, SUM(amount) as total, COUNT(*) as count")
            ->groupBy('payment_method')
            ->get();

        $total_revenue = Payment::whereBetween('created_at', [$fromDt, $toDt])->sum('amount');
        $cash_revenue  = Payment::whereBetween('created_at', [$fromDt, $toDt])->where('payment_method', 'cash')->sum('amount');
        $visa_revenue  = Payment::whereBetween('created_at', [$fromDt, $toDt])->where('payment_method', 'visa')->sum('amount');

        // ===== الطلبات =====
        $orders = Order::whereBetween('created_at', [$fromDt, $toDt]);
        $total_orders   = (clone $orders)->count();
        $completed_orders = (clone $orders)->where('status', OrderStatus::DELIVERED)->count();
        $pending_orders = (clone $orders)->whereNotIn('status', [OrderStatus::DELIVERED, OrderStatus::CANCELLED])->count();
        $cancelled_orders = (clone $orders)->where('status', OrderStatus::CANCELLED)->count();
        $total_sales_value = (clone $orders)->sum('total_price'); // قيمة المبيعات (ليست الإيرادات)
        $total_debts = Order::where('remaining_money', '>', 0)->sum('remaining_money');

        // ===== المصروفات =====
        $total_expenses = Expense::whereBetween('created_at', [$fromDt, $toDt])->sum('value');

        // ===== الرواتب المدفوعة =====
        $paid_salaries = Salary::where('payment_status', 'paid')
            ->whereBetween('updated_at', [$fromDt, $toDt])
            ->sum('value');

        // ===== الأرباح =====
        $total_costs = $total_expenses + $paid_salaries;
        $net_profit  = $total_revenue - $total_costs;

        // ===== عملاء جدد =====
        $new_customers = Customer::whereBetween('created_at', [$fromDt, $toDt])->count();

        // ===== أكثر الخدمات طلباً =====
        $top_services = \App\Models\OrderDetail::with('service')
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$fromDt, $toDt]))
            ->selectRaw('service_id, SUM(quantity) as total_qty, SUM(total_price) as total_revenue')
            ->groupBy('service_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('reports.index', compact(
            'from', 'to',
            'total_revenue', 'cash_revenue', 'visa_revenue', 'payments',
            'total_orders', 'completed_orders', 'pending_orders', 'cancelled_orders',
            'total_sales_value', 'total_debts',
            'total_expenses', 'paid_salaries', 'total_costs', 'net_profit',
            'new_customers', 'top_services'
        ));
    }
}
