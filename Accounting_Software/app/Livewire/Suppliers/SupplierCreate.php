<?php

namespace App\Livewire\Suppliers;


use App\Livewire\Concerns\RequiresPermission;
use App\Models\Supplier;
use Livewire\Component;

class SupplierCreate extends Component
{
    use RequiresPermission;

    public string $name = '';
    public string $phone = '';
    public string $address = '';
    public string $notes = '';
    public string $opening_balance = '0';
    public bool $is_active = true;

    protected array $rules = [
        'name' => 'required|string|min:2|max:255',
        'phone' => 'nullable|string|max:30',
        'address' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:2000',
        'opening_balance' => 'required|numeric',
        'is_active' => 'boolean',
    ];

    public function save(): void
    {
        $this->requirePermission('suppliers.manage');
        $this->validate();

        Supplier::create([
            'name' => trim($this->name),
            'phone' => trim($this->phone) !== '' ? trim($this->phone) : null,
            'address' => trim($this->address) !== '' ? trim($this->address) : null,
            'notes' => trim($this->notes) !== '' ? trim($this->notes) : null,
            'opening_balance' => (float) $this->opening_balance,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success-msg', 'تم إضافة المورد');
        $this->redirectRoute('suppliers.index', navigate: true);
    }

    public function render()
    {
        return view('suppliers.form', [
            'title' => 'إضافة مورد',
        ])->layout('layouts.app')->title('الموردون | إضافة مورد');
    }
}
