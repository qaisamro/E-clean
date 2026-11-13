<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Models\SupplierBill;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class BillList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    #[Url(as: 'supplier', history: true)]
    public string $supplier_id = '';

    public function render()
    {
        $bills = SupplierBill::query()
            ->with(['supplier:id,name'])
            ->withCount('lines')
            ->when($this->search, fn ($q) => $q->where(fn ($t) => $t
                ->where('bill_number', 'like', "%{$this->search}%")
                ->orWhere('id', 'like', "%{$this->search}%")
                ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$this->search}%"))))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->supplier_id, fn ($q) => $q->where('supplier_id', $this->supplier_id))
            ->orderByDesc('bill_date')
            ->orderByDesc('id');

        return view('suppliers.bills.index', [
            'bills' => $bills->paginate(config('app.default_pagination')),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.app')->title('الموردون | فواتير الشراء');
    }

    public function updated($attr): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'supplier_id']);
        $this->resetPage();
    }
}
