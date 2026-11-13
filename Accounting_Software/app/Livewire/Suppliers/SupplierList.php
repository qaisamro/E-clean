<?php

namespace App\Livewire\Suppliers;


use App\Livewire\Concerns\RequiresPermission;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class SupplierList extends Component
{
    use RequiresPermission;

    use WithPagination;

    public string $search = '';
    public string $active = '';
    public bool $balanceOnly = false;

    public function render()
    {
        $suppliers = Supplier::query()
            ->withSum('bills as bills_total', 'total')
            ->withSum('payments as payments_total', 'amount')
            ->withCount('bills')
            ->when($this->search, fn ($q) => $q->where(fn ($s) => $s
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")))
            ->when($this->active !== '', fn ($q) => $q->where('is_active', $this->active === '1'))
            ->when($this->balanceOnly, fn ($q) => $q->whereRaw('opening_balance + COALESCE((SELECT SUM(total) FROM supplier_bills WHERE supplier_bills.supplier_id = suppliers.id), 0) - COALESCE((SELECT SUM(amount) FROM supplier_payments WHERE supplier_payments.supplier_id = suppliers.id), 0) > 0.0001'))
            ->orderBy('name');

        return view('suppliers.index', [
            'suppliers' => $suppliers->paginate(config('app.default_pagination')),
        ])->layout('layouts.app')->title('الموردون | قائمة الموردين');
    }

    public function updated($attr): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'active', 'balanceOnly']);
        $this->resetPage();
    }

    public function toggleActive(Supplier $supplier): void
    {
        $this->requirePermission('suppliers.manage');
        $supplier->update(['is_active' => ! $supplier->is_active]);
        session()->flash('success-msg', 'تم تحديث حالة المورد');
    }

    public function deleteSupplier(int $id): void
    {
        $this->requirePermission('suppliers.manage');
        $supplier = Supplier::withCount(['bills', 'payments'])->findOrFail($id);
        if ($supplier->bills_count > 0 || $supplier->payments_count > 0) {
            session()->flash('error-msg', 'لا يمكن حذف مورد عليه فواتير أو دفعات — يمكنك فقط تعطيله');
            return;
        }
        $supplier->delete();
        session()->flash('success-msg', 'تم حذف المورد');
    }
}
