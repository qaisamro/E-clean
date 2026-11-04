<?php

namespace App\Livewire\Order;

use App\Livewire\Forms\CustomerForm;
use App\Livewire\Forms\OrderForm;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Service;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateOrder extends Component
{
    public CustomerForm $customerForm;
    public OrderForm $orderForm;

    public $selectedOrder = '';

    public $total_price = 0;
    
    #[Validate('numeric|min:0', as: 'المبلغ المدفوع')]
    public $paid_money = 0;
    
    public $remaining_money = 0;
    
    #[Validate('required|in:1,2,3,4', as: 'نوع الدفع')]
    public $payment_type = 1; // 1: cash, 2: deferred, 3: visa, 4: partial

    #[Validate('nullable', as: 'تاريخ التسليم')]
    #[Validate('date')]
    public $deliver_date;

    #[Validate('nullable|string', as: 'وقت التسليم')]
    public $deliver_time = '';

    #[Validate('integer|min:0', as: 'الخصم')]
    public $discount = 0;

    #[Validate('nullable|string', as: 'ملاحظات')]
    public $notes = '';

    public function render()
    {
        return view('order.create')
            ->title(config('app.name') . ' | ' . 'إضافة طلب');
    }

    public function mount()
    {
        $this->orderForm->setDefaultValues();
        $this->deliver_date = now()->format('Y-m-d');
        $this->deliver_time = now()->addHours(2)->format('H:i');
    }

    public function updated($attr, $value): void
    {
        $this->resetValidation('orderForm.error_msg');
        $this->validateOnly($attr);
    }

    public function updatedDiscount(): void
    {
        $this->calculateTotalPrice();
    }

    public function updatedPaymentType(): void
    {
        $this->calculateTotalPrice();
    }

    public function updatedPaidMoney(): void
    {
        $this->calculateTotalPrice();
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
            if ($index === $this->selectedOrder) {
                $this->reset('selectedOrder');
            } else {
                $this->selectedOrder = $index;
            }
        }
    }

    public function deleteOrder(): void
    {
        if (array_key_exists($this->selectedOrder, $this->orderForm->orders)) {
            array_splice($this->orderForm->orders, $this->selectedOrder, 1);
            $this->reset('selectedOrder');
            $this->calculateTotalPrice();
        }
    }

    public function applyChanges(): void
    {
        $this->orderForm->orders = array_map(function ($order) {
            $order['total_price'] = intval($order['price']) * intval($order['quantity']);
            return $order;
        }, $this->orderForm->orders);

        $this->calculateTotalPrice();
    }

    public function calculateTotalPrice(): void
    {
        $subtotal = collect($this->orderForm->orders)->sum('total_price');
        $this->total_price = max(0, $subtotal - intval($this->discount));

        if ($this->payment_type == 1 || $this->payment_type == 3) {
            $this->paid_money = $this->total_price;
            $this->remaining_money = 0;
        } elseif ($this->payment_type == 2) {
            $this->paid_money = 0;
            $this->remaining_money = $this->total_price;
        } else { // Partial
            $this->remaining_money = max(0, $this->total_price - intval($this->paid_money));
        }
    }

    public function saveOrder(): void
    {
        $this->validate();
        if (count($this->orderForm->orders) === 0) {
            $this->addError('error_msg', 'يجب إضافة طلبات أولا');
            return;
        }

        try {
            DB::transaction(function () {
                $ordersToBeInserted = [];
                $deferredFound = false;

                if (!$this->customerForm->id) {
                    $this->customerForm->create();
                }

                // Determine financial balances
                $paid = 0;
                $remaining = 0;
                $paymentStatus = \App\Enums\PaymentStatus::UNPAID;

                if ($this->payment_type == 1 || $this->payment_type == 3) {
                    $paid = $this->total_price;
                    $remaining = 0;
                    $paymentStatus = \App\Enums\PaymentStatus::PAID;
                } elseif ($this->payment_type == 2) {
                    $paid = 0;
                    $remaining = $this->total_price;
                    $paymentStatus = \App\Enums\PaymentStatus::UNPAID;
                } else { // Partial
                    $paid = intval($this->paid_money);
                    $remaining = max(0, $this->total_price - $paid);
                    if ($remaining == 0) {
                        $paymentStatus = \App\Enums\PaymentStatus::PAID;
                    } elseif ($paid > 0) {
                        $paymentStatus = \App\Enums\PaymentStatus::PARTIALLY_PAID;
                    } else {
                        $paymentStatus = \App\Enums\PaymentStatus::UNPAID;
                    }
                }

                $order = Order::create([
                    'total_price' => $this->total_price,
                    'paid_money' => $paid,
                    'remaining_money' => $remaining,
                    'payment_type' => $this->payment_type,
                    'payment_status' => $paymentStatus,
                    'customer_id' => $this->customerForm->id,
                    'deliver_date' => $this->deliver_date,
                    'deliver_time' => $this->deliver_time,
                    'discount' => intval($this->discount),
                    'notes' => $this->notes,
                    'status' => \App\Enums\OrderStatus::NEW, // starts at NEW
                ]);

                foreach ($this->orderForm->orders as $orderDetail) {
                    $ordersToBeInserted[] = [
                        'order_id' => $order->id,
                        'item_id' => $orderDetail['item_id'],
                        'service_id' => $orderDetail['service_id'],
                        'price' => $orderDetail['price'],
                        'quantity' => $orderDetail['quantity'],
                        'total_price' => $orderDetail['total_price'],
                        'is_payment_deferred' => !$orderDetail['price'],
                        'description' => $orderDetail['description'] ?? '',
                        'created_at' => now()
                    ];
                    if (!$orderDetail['price']) {
                        $deferredFound = true;
                    }
                }

                if ($deferredFound) {
                    $order->update(['has_deferred_payment' => true]);
                }

                OrderDetail::insert($ordersToBeInserted);

                // Insert payment record if cash flow exists
                if ($paid > 0) {
                    Payment::create([
                        'order_id' => $order->id,
                        'amount' => $paid,
                        'payment_method' => $this->payment_type == 3 ? 'visa' : 'cash',
                        'created_by' => auth()->id(),
                        'notes' => 'الدفعة الأولى عند إنشاء الطلب',
                    ]);
                }
            });
        } catch (\Throwable $throwable) {
            $this->addError('error_msg', 'حدث خطأ اثناء حفظ الطلب: ' . $throwable->getMessage());
            return;
        }

        $this->orderForm->reset();
        $this->resetExcept('customerForm', 'orderForm');
        $this->customerForm->resetForNextOrder();
        $this->orderForm->setDefaultValues();
        $this->deliver_date = now()->format('Y-m-d');
        $this->deliver_time = now()->addHours(2)->format('H:i');
        session()->flash('success-msg', 'تم إضافه الطلب بنجاح');
    }
}
