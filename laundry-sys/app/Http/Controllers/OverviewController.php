<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Salary;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = [];

        if (auth()->user()->isSuperAdmin()) {

            // ===== تعريف النطاقات الزمنية =====
            $today       = now()->toDateString();
            $weekStart   = now()->startOfWeek()->toDateString();
            $monthStart  = now()->startOfMonth()->toDateString();
            $yearStart   = now()->startOfYear()->toDateString();

            // ===== الإيرادات المقبوضة فعلياً (من جدول payments) =====
            $daily_revenue   = Payment::whereDate('created_at', $today)->sum('amount');
            $weekly_revenue  = Payment::whereBetween('created_at', [$weekStart . ' 00:00:00', now()])->sum('amount');
            $monthly_revenue = Payment::whereBetween('created_at', [$monthStart . ' 00:00:00', now()])->sum('amount');

            // ===== الطلبات (من جدول orders) =====
            $daily_orders_q   = Order::whereDate('created_at', $today);
            $monthly_orders_q = Order::whereBetween('created_at', [$monthStart . ' 00:00:00', now()]);

            $daily_orders_count   = (clone $daily_orders_q)->count();
            $daily_pending_count  = (clone $daily_orders_q)->where('status', OrderStatus::NEW)->count();
            $daily_completed_count = (clone $daily_orders_q)->where('status', OrderStatus::DELIVERED)->count();

            $weekly_orders_count  = Order::whereBetween('created_at', [$weekStart . ' 00:00:00', now()])->count();
            $monthly_orders_count = (clone $monthly_orders_q)->count();

            // ===== العملاء الجدد =====
            $monthly_customers_q    = Customer::whereBetween('created_at', [$monthStart . ' 00:00:00', now()]);
            $new_monthly_customer_count = (clone $monthly_customers_q)->count();
            $new_weekly_customer_count  = Customer::whereBetween('created_at', [$weekStart . ' 00:00:00', now()])->count();
            $new_daily_customer_count   = Customer::whereDate('created_at', $today)->count();

            // ===== المصروفات =====
            $daily_expenses   = Expense::whereDate('created_at', $today)->sum('value');
            $weekly_expenses  = Expense::whereBetween('created_at', [$weekStart . ' 00:00:00', now()])->sum('value');
            $monthly_expenses = Expense::whereBetween('created_at', [$monthStart . ' 00:00:00', now()])->sum('value');

            // ===== الرواتب المدفوعة هذا الشهر =====
            $paid_salaries_monthly = Salary::where('payment_status', 'paid')
                ->whereBetween('updated_at', [$monthStart . ' 00:00:00', now()])
                ->sum('value');

            // ===== صافي الأرباح (الإيرادات - التكاليف) =====
            $daily_profit   = $daily_revenue   - $daily_expenses;
            $weekly_profit  = $weekly_revenue  - $weekly_expenses;
            $monthly_profit = $monthly_revenue - $monthly_expenses - $paid_salaries_monthly;

            // ===== الإيرادات المقبوضة فعلياً (من جدول payments) - السنة
            $yearStart       = now()->startOfYear()->toDateString();
            $yearly_revenue  = Payment::whereBetween('created_at', [$yearStart . ' 00:00:00', now()])->sum('amount');

            // ===== المصروفات - السنة
            $yearly_expenses = Expense::whereBetween('created_at', [$yearStart . ' 00:00:00', now()])->sum('value');

            // ===== الرواتب المدفوعة هذا العام
            $paid_salaries_yearly = Salary::where('payment_status', 'paid')
                ->whereBetween('updated_at', [$yearStart . ' 00:00:00', now()])
                ->sum('value');

            // ===== صافي الأرباح السنوية
            $yearly_profit = $yearly_revenue - $yearly_expenses - $paid_salaries_yearly;


            // ===== الديون الكلية =====
            $total_outstanding_debt = Order::where('remaining_money', '>', 0)->sum('remaining_money');

            // ===== الخدمات الأكثر طلباً اليوم =====
            $serviceCounts = [];
            $today_orders = Order::whereDate('created_at', $today)
                ->with(['orderDetails' => ['service']])
                ->get();

            $today_orders->each(function ($order) use (&$serviceCounts) {
                $order->orderDetails->each(function (OrderDetail $detail) use (&$serviceCounts) {
                    $name = $detail->service->name ?? 'غير محدد';
                    $serviceCounts[$name] = ($serviceCounts[$name] ?? 0) + 1;
                });
            });
            arsort($serviceCounts);

            // ===== رسم بياني =====
            $services_chart = app()->chartjs
                ->name('pieChartTest')
                ->type('pie')
                ->size(['width' => 500, 'height' => 500])
                ->labels(array_keys($serviceCounts))
                ->datasets([
                    [
                        'backgroundColor' => $this->generateRandomColor(count($serviceCounts)),
                        'data' => array_values($serviceCounts)
                    ]
                ]);

            $data = [
                // Daily
                'daily_orders_count'   => $daily_orders_count,
                'daily_paid_sum'       => $daily_revenue,
                'daily_pending_count'  => $daily_pending_count,
                'daily_completed_count' => $daily_completed_count,
                'daily_profit'         => $daily_profit,

                // Weekly
                'weekly_orders_count'  => $weekly_orders_count,
                'weekly_paid_sum'      => $weekly_revenue,
                'weekly_profit'        => $weekly_profit,

                // Monthly
                'monthly_orders_count' => $monthly_orders_count,
                'monthly_paid_sum'     => $monthly_revenue,
                'monthly_profit'       => $monthly_profit,
                'yearly_profit'        => $yearly_profit,

                // Customers
                'new_monthly_customer_count' => $new_monthly_customer_count,
                'new_weekly_customer_count'  => $new_weekly_customer_count,
                'new_daily_customer_count'   => $new_daily_customer_count,

                // Debt
                'total_outstanding_debt' => $total_outstanding_debt,

                // Chart
                'daily_services_count' => $serviceCounts,
                'services_chart'       => $services_chart,
            ];
        }

        return view('overview', $data);
    }

    private function generateRandomColor($count)
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }
        return $colors;
    }
}
