<?php

namespace App\Livewire\Suppliers;


use App\Livewire\Concerns\RequiresPermission;
use App\Models\SupplierBill;
use App\Enums\StockMovementType;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BillShow extends Component
{
    use RequiresPermission;

    public SupplierBill $bill;

    public function mount(SupplierBill $bill): void
    {
        $this->bill = $bill->load(['supplier', 'lines.product', 'payments']);
    }

    public function deleteBill(): void
    {
        $this->requirePermission('suppliers.manage');
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        if ($this->bill->payments()->exists()) {
            session()->flash('error-msg', 'لا يمكن حذف فاتورة عليها دفعات — احذف الدفعات أولاً');
            return;
        }

        $bill = $this->bill;

        try {
            DB::transaction(function () use ($bill) {
                $stockService = app(StockService::class);
                foreach ($bill->lines()->whereNotNull('product_id')->with('product')->get() as $line) {
                    $product = $line->product;
                    if ($product) {
                        // reverse the goods received — they effectively go back to the supplier
                        $stockService->record(
                            $product,
                            StockMovementType::RETURN_SUPPLIER,
                            (float) $line->quantity,
                            'إلغاء فاتورة مورد #' . $bill->id,
                            'إلغاء فاتورة الشراء',
                        );
                    }
                }
                $bill->delete();
            });
        } catch (\Throwable $e) {
            session()->flash('error-msg', 'تعذر حذف الفاتورة: ' . $e->getMessage());
            return;
        }

        session()->flash('success-msg', 'تم حذف الفاتورة وعكس حركات المخزن');
        $this->redirectRoute('supplier-bills.index', navigate: true);
    }

    public function render()
    {
        return view('suppliers.bills.show', [
            'bill' => $this->bill->fresh(['supplier', 'lines.product', 'payments']),
        ])->layout('layouts.app')->title('الموردون | فاتورة شراء #' . $this->bill->id);
    }
}
