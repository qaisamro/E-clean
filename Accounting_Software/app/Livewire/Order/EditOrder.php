<?php

namespace App\Livewire\Order;


use App\Livewire\Concerns\RequiresPermission;
use App\Livewire\Forms\CustomerForm;
use App\Livewire\Forms\OrderForm;
use App\Models\Customer;
use App\Models\ItemService;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Service;
use App\Services\OrderFinancials;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditOrder extends Component
{
    use RequiresPermission;

    public CustomerForm $customerForm;
    public OrderForm $orderForm;

    public Order $order;

    public $selectedOrder = '';

    public $total_price = 0;
    public $paid_money = 0;
    public $remaining_money = 0;
    public $payment_type = 1;
    public $product_subtotal = 0;

    #[Validate('nullable', as: 'تاريخ التسليم')]
    #[Validate('date')]
    public $deliver_date;

    public function mount(Order $order)
    {
        $this->orderForm->orders = $order->orderDetails->load(['item', 'service'])->toArray();
        $this->customerForm->setValues($order->customer);
        $this->deliver_date = $order->deliver_date;

        $this->payment_type = $order->payment_type?->value;
        $this->paid_money = $order->paid_money;
        $this->remaining_money = $order->remaining_money;
        $this->product_subtotal = (float) $order->orderProducts()->sum('total_price');

        $this->orderForm->setDefaultValues();
        $this->orderForm->isEditMode = true;
        $this->calculateTotalPrice();
    }

    public function render()
    {
        return view('order.edit')
            ->title(config('app.name') . ' | ' . 'تعديل طلب');
    }

    public function updated($attr, $value): void
    {
        $this->resetValidation('orderForm.error_msg');
        $this->validateOnly($attr);
    }

    ################### Customer ########################
    public function updatedCustomerFormSearch($value): void
    {
        $this->customerForm->getCustomers($value);
    }

    public function updatedCustomerFormId(Customer $customer): void
    {
        $this->customerForm->setId($customer);
    }

    public function cancelCustomer(): void
    {
        $this->customerForm->reset();
    }
    ################### End Customer ########################
    ################## Orders ################################

    public function updatedOrderFormServiceId(Service $service): void
    {
        $this->reset('selectedOrder');
        $this->orderForm->setItems($service);
    }

    public function updatedOrderFormItemId($item_id): void
    {
        $this->reset('selectedOrder');
        $this->orderForm->addOrder();
        $this->calculateTotalPrice();
    }

    public function selectOrder($index): void
    {
        if (array_key_exists($index, $this->orderForm->orders)) {
            if ($index == $this->selectedOrder) {
                $this->reset('selectedOrder');
            } else {
                $this->selectedOrder = $index;
            }
        }
    }

    public function deleteOrder(): void
    {
        $this->requirePermission('orders.edit');
        if (array_key_exists($this->selectedOrder, $this->orderForm->orders)) {

            $orderToBeDeleted = $this->orderForm->orders[$this->selectedOrder];

            if (isset($orderToBeDeleted['id'])) {
                try {
                    array_splice($this->orderForm->orders, $this->selectedOrder, 1);
                } catch (\Throwable $throwable) {
                    $this->addError('error_msg', 'حدث خطأ اثناء الحذف');
                }
            } else {
                array_splice($this->orderForm->orders, $this->selectedOrder, 1);
            }
            $this->reset('selectedOrder');
            $this->calculateTotalPrice();
        }
    }

    public function applyChanges(): void
    {
        $this->orderForm->orders = array_map(function ($order) {
            $order['total_price'] = floatval($order['price']) * floatval($order['quantity']);
            return $order;
        }, $this->orderForm->orders);

        $this->calculateTotalPrice();
    }

    public function calculateTotalPrice(): void
    {
        $subtotal = (float) collect($this->orderForm->orders)->sum('total_price') + $this->product_subtotal;
        $this->total_price = round(max(0, $subtotal - (float) $this->order->discount), 2);
    }


    public function saveOrder(): void
    {
        $this->requirePermission('orders.edit');
        $this->validate();
        if (count($this->orderForm->orders) === 0) {
            $this->addError('error_msg', 'يجب إضافة طلبات أولا');
            return;
        }

        try {
            DB::transaction(function () {
                $order = Order::query()->lockForUpdate()->findOrFail($this->order->id);
                $ordersToBeUpdatedOrInserted = [];

                // Check if there is no customer
                if (!$this->customerForm->id) {
                    $this->customerForm->create();
                }

                // Prepare the data
                foreach ($this->orderForm->orders as $orderDetail) {

                    $price = round((float) ($orderDetail['price'] ?? 0), 2);
                    $quantity = round((float) ($orderDetail['quantity'] ?? 0), 2);
                    if ($price <= 0 || $quantity <= 0) {
                        throw new \DomainException('يجب أن يكون سعر وكمية كل بند أكبر من صفر.');
                    }

                    $ordersToBeUpdatedOrInserted[] = [
                        'id' => $orderDetail['id'] ?? null,
                        'order_id' => $order->id,
                        'item_id' => $orderDetail['item_id'],
                        'service_id' => $orderDetail['service_id'],
                        'price' => $price,
                        'quantity' => $quantity,
                        'total_price' => round($price * $quantity, 2),
                        'is_payment_deferred' => false,
                        'description' => $orderDetail['description'] ?? '',
                    ];
                }

                // Fill Order Object to check if it changed or not
                $order->fill([
                    'customer_id' => $this->customerForm->id,
                    'deliver_date' => $this->deliver_date,
                    'has_deferred_payment' => false,
                ]);

                if ($order->isDirty()) {
                    $order->save();
                }

                $keptIds = collect($ordersToBeUpdatedOrInserted)->pluck('id')->filter()->all();
                $order->orderDetails()->when($keptIds, fn ($query) => $query->whereNotIn('id', $keptIds))
                    ->when(empty($keptIds), fn ($query) => $query)
                    ->delete();

                foreach ($ordersToBeUpdatedOrInserted as $detail) {
                    $id = $detail['id'];
                    unset($detail['id']);
                    if ($id) {
                        OrderDetail::query()->whereKey($id)->where('order_id', $order->id)->update($detail);
                    } else {
                        OrderDetail::create($detail);
                    }
                }

                app(OrderFinancials::class)->reconcile($order);

            });
        } catch (\Throwable $throwable) {
            $this->addError('error_msg', 'حدث خطأ اثناء حفظ الطلب');
            return;
        }

        toast('تم تعديل الطلب بنجاح', 'success');
        $this->redirectRoute('orders.show', $this->order->id);
    }
}
