<?php

namespace App\Livewire\Suppliers;


use App\Livewire\Concerns\RequiresPermission;
use App\Models\Supplier;
use App\Models\SupplierBill;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaymentCreate extends Component
{
    use RequiresPermission;

    public string $supplier_id = '';
    public string $bill_id = '';
    public string $payment_date = '';
    public string $amount = '';
    public string $method = 'cash';
    public string $notes = '';

    public function mount(): void
    {
        $this->payment_date = now()->toDateString();
        $supplier = request()->integer('supplier');
        $bill = request()->integer('bill');
        if ($supplier && Supplier::find($supplier)) {
            $this->supplier_id = (string) $supplier;
        }
        if ($bill && ($sb = SupplierBill::find($bill))) {
            $this->bill_id = (string) $sb->id;
            $this->supplier_id = (string) $sb->supplier_id;
        }
    }

    public function updatedSupplierId(): void
    {
        $this->bill_id = '';
    }

    public function save(): void
    {
        $this->requirePermission('suppliers.manage');
        $this->validate([
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'bill_id' => 'nullable|integer|exists:supplier_bills,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank,cheque,other',
            'notes' => 'nullable|string|max:2000',
        ], [], [
            'supplier_id' => 'المورد',
            'payment_date' => 'تاريخ الدفع',
            'amount' => 'المبلغ',
        ]);

        $amount = round((float) $this->amount, 2);

        if ($this->bill_id !== '') {
            $bill = SupplierBill::findOrFail((int) $this->bill_id);
            if ($bill->supplier_id !== (int) $this->supplier_id) {
                $this->addError('bill_id', 'الفاتورة لا تخص هذا المورد');
                return;
            }
            $remaining = max(0, (float) $bill->remaining);
            if ($amount > $remaining + 0.0001) {
                $this->addError('amount', 'المبلغ أكبر من المتبقي على هذه الفاتورة (' . number_format($remaining, 2) . ' ₪) — لو كان الدفع زائدًا عن الفاتورة فسجّله كدفعة على الحساب');
                return;
            }
        }

        $payment = null;
        DB::transaction(function () use ($amount, &$payment) {
            $payment = SupplierPayment::create([
                'supplier_id' => (int) $this->supplier_id,
                'bill_id' => $this->bill_id !== '' ? (int) $this->bill_id : null,
                'payment_date' => $this->payment_date,
                'amount' => $amount,
                'method' => $this->method,
                'notes' => trim($this->notes) !== '' ? trim($this->notes) : null,
                'created_by' => auth()->id(),
            ]);

            if ($payment->bill_id) {
                SupplierBill::recalculate($payment->bill()->first());
            }
        });

        session()->flash('success-msg', 'تم تسجيل الدفعة #' . $payment->id);
        $this->redirectRoute('suppliers.show', ['supplier' => $this->supplier_id, 'tab' => 'statement'], navigate: true);
    }

    public function render()
    {
        $supplierId = $this->supplier_id !== '' ? (int) $this->supplier_id : null;
        $bills = $supplierId
            ? SupplierBill::where('supplier_id', $supplierId)->where('remaining', '>', 0)->orderByDesc('bill_date')->get()
            : collect();

        return view('suppliers.payments.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'bills' => $bills,
        ])->layout('layouts.app')->title('الموردون | تسجيل دفعة');
    }
}
