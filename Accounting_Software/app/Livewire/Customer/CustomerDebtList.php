<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerDebtList extends Component
{
    use WithPagination;

    public string $search = '';
    public $selectedCustomerId = null;
    public $paymentAmount = 0;
    public $paymentNotes = '';
    public $paymentMethod = 'cash';
    public bool $showPaymentModal = false;

    public function render()
    {
        $customers = Customer::query()
            ->whereHas('orders', fn($q) => $q->where('remaining_money', '>', 0))
            ->withSum(['orders' => fn($q) => $q->where('remaining_money', '>', 0)], 'remaining_money')
            ->withSum(['orders' => fn($q) => $q->where('remaining_money', '>', 0)], 'paid_money')
            ->withSum(['orders' => fn($q) => $q->where('remaining_money', '>', 0)], 'total_price')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(15);

        return view('customer.debts', ['customers' => $customers])
            ->layout('layouts.app')
            ->title(config('app.name') . ' | الديون المستحقة');
    }

    public function openPaymentModal($customerId)
    {
        $this->selectedCustomerId = $customerId;
        $this->paymentAmount = 0;
        $this->paymentNotes = '';
        $this->paymentMethod = 'cash';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedCustomerId = null;
    }

    public function submitPayment()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:1',
            'paymentMethod' => 'required|in:cash,visa',
        ], [
            'paymentAmount.required' => 'أدخل مبلغ الدفعة',
            'paymentAmount.min' => 'يجب أن يكون المبلغ أكبر من 0',
        ]);

        $customer = Customer::findOrFail($this->selectedCustomerId);
        $remaining = $this->paymentAmount;

        try {
            DB::transaction(function () use ($customer, &$remaining) {
                // Get orders with remaining balance, oldest first
                $orders = Order::where('customer_id', $customer->id)
                    ->where('remaining_money', '>', 0)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();

                foreach ($orders as $order) {
                    if ($remaining <= 0) break;

                    $deduct = min($remaining, $order->remaining_money);
                    $newPaid = $order->paid_money + $deduct;
                    $newRemaining = $order->remaining_money - $deduct;

                    $newStatus = $newRemaining <= 0
                        ? PaymentStatus::PAID
                        : PaymentStatus::PARTIALLY_PAID;

                    $order->update([
                        'paid_money' => $newPaid,
                        'remaining_money' => $newRemaining,
                        'payment_status' => $newStatus,
                    ]);

                    // Record the partial payment
                    Payment::create([
                        'order_id' => $order->id,
                        'amount' => $deduct,
                        'payment_method' => $this->paymentMethod,
                        'created_by' => auth()->id(),
                        'notes' => $this->paymentNotes ?: 'تحصيل دين',
                    ]);

                    $remaining -= $deduct;
                }
            });

            session()->flash('debt-success', 'تم تسجيل الدفعة بنجاح ✓');
            $this->closePaymentModal();
            $this->resetPage();
        } catch (\Throwable $e) {
            $this->addError('payment', 'حدث خطأ أثناء تسجيل الدفعة: ' . $e->getMessage());
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
