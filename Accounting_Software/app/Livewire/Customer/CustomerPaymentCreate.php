<?php

namespace App\Livewire\Customer;


use App\Livewire\Concerns\RequiresPermission;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderFinancials;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomerPaymentCreate extends Component
{
    use RequiresPermission;

    public string $search = '';
    public string $customer_id = '';
    public string $order_id = '';
    public string $order_code_filter = '';
    public string $date_from = '';
    public string $date_to = '';
    public string $payment_date = '';
    public string $amount = '';
    public string $payment_method = 'cash';
    public string $notes = '';

    public function mount(): void
    {
        $this->payment_date = now()->toDateString();
        $customer = request()->integer('customer');
        if ($customer && Customer::find($customer)) {
            $this->customer_id = (string) $customer;
            $this->search = Customer::find($customer)->name;

            $order = request()->integer('order');
            if ($order && Order::query()->whereKey($order)->where('customer_id', $customer)->exists()) {
                $this->order_id = (string) $order;
            }
        }
    }

    public function selectCustomer(int $customerId): void
    {
        $customer = Customer::findOrFail($customerId);
        $this->customer_id = (string) $customer->id;
        $this->search = $customer->name;
        $this->order_id = '';
        $this->resetValidation();
    }

    public function clearCustomer(): void
    {
        $this->reset('customer_id', 'order_id', 'search', 'order_code_filter', 'date_from', 'date_to', 'amount', 'notes');
        $this->payment_date = now()->toDateString();
        $this->payment_method = 'cash';
    }

    public function save(): void
    {
        $this->requirePermission('customers.manage');
        $this->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'order_id' => 'nullable|integer|exists:orders,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,visa',
            'notes' => 'nullable|string|max:1000',
        ], [], [
            'customer_id' => 'العميل',
            'payment_date' => 'تاريخ الدفع',
            'amount' => 'مبلغ الدفعة',
            'payment_method' => 'طريقة الدفع',
        ]);

        $amount = round((float) $this->amount, 2);
        $customerId = (int) $this->customer_id;
        $selectedOrderId = $this->order_id !== '' ? (int) $this->order_id : null;

        try {
            DB::transaction(function () use ($amount, $customerId, $selectedOrderId) {
                $orders = Order::query()
                    ->where('customer_id', $customerId)
                    ->where('remaining_money', '>', 0)
                    ->when($selectedOrderId, fn ($query) => $query->whereKey($selectedOrderId))
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();

                if ($orders->isEmpty()) {
                    throw new \RuntimeException('لا توجد مديونية مستحقة لهذا العميل أو للطلب المحدد.');
                }

                $available = round((float) $orders->sum('remaining_money'), 2);
                if ($amount > $available + 0.0001) {
                    throw new \RuntimeException('مبلغ الدفعة أكبر من الرصيد المستحق (' . number_format($available, 2) . ' ₪).');
                }

                $remaining = $amount;
                foreach ($orders as $order) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $deduct = min($remaining, (float) $order->remaining_money);

                    Payment::create([
                        'order_id' => $order->id,
                        'amount' => $deduct,
                        'payment_method' => $this->payment_method,
                        'created_by' => auth()->id(),
                        'notes' => trim($this->notes) !== '' ? trim($this->notes) : 'دفعة عميل',
                        'created_at' => $this->payment_date . ' ' . now()->format('H:i:s'),
                        'updated_at' => now(),
                    ]);

                    app(OrderFinancials::class)->reconcile($order);

                    $remaining = round($remaining - $deduct, 2);
                }
            });
        } catch (\Throwable $exception) {
            $this->addError('payment', $exception->getMessage());
            return;
        }

        session()->flash('success-msg', 'تم تسجيل الدفعة بنجاح.');
        $this->reset('order_id', 'amount', 'notes');
        $this->payment_date = now()->toDateString();
        $this->payment_method = 'cash';
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($customer) {
                    $customer->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('id', $this->search);
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        $customer = $this->customer_id !== '' ? Customer::find((int) $this->customer_id) : null;
        $outstanding = collect();
        $totalDue = 0.0;

        if ($customer) {
            $base = $customer->orders()->where('remaining_money', '>', 0);
            $totalDue = (float) (clone $base)->sum('remaining_money');
            $outstanding = $base
                ->when($this->order_code_filter !== '', fn ($query) => $query->where('order_code', 'like', '%' . $this->order_code_filter . '%'))
                ->when($this->date_from !== '', fn ($query) => $query->whereDate('created_at', '>=', $this->date_from))
                ->when($this->date_to !== '', fn ($query) => $query->whereDate('created_at', '<=', $this->date_to))
                ->orderBy('created_at')
                ->get();
        }

        return view('customer.payments.create', compact('customers', 'customer', 'outstanding', 'totalDue'))
            ->layout('layouts.app')
            ->title(config('app.name') . ' | إضافة دفعة عميل');
    }
}
