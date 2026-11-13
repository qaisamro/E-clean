<?php

namespace App\Livewire\Suppliers;


use App\Livewire\Concerns\RequiresPermission;
use App\Models\Supplier;
use App\Models\SupplierBill;
use App\Models\SupplierPayment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentList extends Component
{
    use RequiresPermission;

    use WithPagination;

    public string $search = '';
    public string $supplier_id = '';

    public function render()
    {
        $payments = SupplierPayment::query()
            ->with(['supplier:id,name', 'bill:id,bill_number,total,remaining'])
            ->when($this->search, fn ($q) => $q->where(fn ($t) => $t
                ->where('id', 'like', "%{$this->search}%")
                ->orWhere('notes', 'like', "%{$this->search}%")
                ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$this->search}%"))))
            ->when($this->supplier_id, fn ($q) => $q->where('supplier_id', $this->supplier_id))
            ->orderByDesc('payment_date')
            ->orderByDesc('id');

        return view('suppliers.payments.index', [
            'payments' => $payments->paginate(config('app.default_pagination')),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.app')->title('الموردون | الدفعات');
    }

    public function updated($attr): void
    {
        $this->resetPage();
    }

    public function deletePayment(int $id): void
    {
        $this->requirePermission('suppliers.manage');
        $payment = SupplierPayment::with('bill')->find($id);
        if (! $payment) {
            return;
        }
        $bill = $payment->bill;
        $payment->delete();
        if ($bill) {
            SupplierBill::recalculate($bill);
        }
        session()->flash('success-msg', 'تم حذف الدفعة وإعادة احتساب الفاتورة');
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'supplier_id']);
        $this->resetPage();
    }
}
