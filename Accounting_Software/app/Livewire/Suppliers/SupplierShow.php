<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Support\SupplierStatement;
use Livewire\Component;
use Livewire\Attributes\Url;

class SupplierShow extends Component
{
    public Supplier $supplier;

    #[Url(as: 'tab', history: true)]
    public string $tab = 'statement';

    public function mount(Supplier $supplier): void
    {
        $this->supplier = $supplier;
    }

    public function render()
    {
        $statement = SupplierStatement::build($this->supplier);

        return view('suppliers.show', [
            'supplier' => $this->supplier,
            'statement' => $statement,
            'bills' => $this->supplier->bills()->with('lines')->withCount('lines')->orderByDesc('bill_date')->orderByDesc('id')->paginate(10, ['*'], 'billsPage'),
            'payments' => $this->supplier->payments()->with('bill:id,bill_number')->orderByDesc('payment_date')->orderByDesc('id')->paginate(10, ['*'], 'paymentsPage'),
        ])->layout('layouts.app')->title('الموردون | كشف حساب: ' . $this->supplier->name);
    }
}
