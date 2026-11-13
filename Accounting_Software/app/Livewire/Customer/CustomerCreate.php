<?php

namespace App\Livewire\Customer;


use App\Livewire\Concerns\RequiresPermission;
use App\Models\Customer;
use Livewire\Component;

class CustomerCreate extends Component
{
    use RequiresPermission;

    public string $name = '';
    public string $phone = '';
    public string $address = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:15'],
            'address' => ['required', 'string', 'max:1000'],
        ];
    }

    protected array $validationAttributes = [
        'name' => 'اسم العميل',
        'phone' => 'رقم الهاتف',
        'address' => 'العنوان',
    ];

    public function save(): void
    {
        $this->requirePermission('customers.manage');
        $data = $this->validate();

        $customer = Customer::create([
            'name' => trim($data['name']),
            'phone' => trim($data['phone']),
            'address' => trim($data['address']),
        ]);

        session()->flash('success-msg', 'تمت إضافة العميل بنجاح');
        $this->redirectRoute('customers.show', ['customer' => $customer->id], navigate: true);
    }

    public function render()
    {
        return view('customer.create')
            ->layout('layouts.app')
            ->title(config('app.name') . ' | إضافة عميل جديد');
    }
}
