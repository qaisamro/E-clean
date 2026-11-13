<?php

namespace App\Livewire\Suppliers;


use App\Livewire\Concerns\RequiresPermission;
use App\Enums\StockMovementType;
use App\Enums\ProductKind;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierBill;
use App\Models\SupplierBillLine;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BillCreate extends Component
{
    use RequiresPermission;

    public int|string $supplier_id = '';
    public string $bill_number = '';
    public string $bill_date = '';
    public string $due_date = '';
    public string $notes = '';

    /**
     * @var array<int, array<string, mixed>>
     *   product line: ['type' => 'product', 'product_id', 'quantity', 'unit_price']
     *   service line: ['type' => 'service', 'description', 'quantity', 'unit_price']
     */
    public array $lines = [];

    public function mount(): void
    {
        $this->bill_date = now()->toDateString();
        $this->supplier_id = request()->integer('supplier') ?: '';
        if (Supplier::find($this->supplier_id) === null) {
            $this->supplier_id = '';
        }
        $this->lines = [$this->emptyLine('product')];
    }

    private function emptyLine(string $type): array
    {
        return [
            'type' => $type,
            'product_id' => '',
            'description' => '',
            'quantity' => '1',
            'unit_price' => '0',
        ];
    }

    public function addProductLine(): void
    {
        $this->lines[] = $this->emptyLine('product');
    }

    public function addServiceLine(): void
    {
        $this->lines[] = $this->emptyLine('service');
    }

    public function removeLine(int $index): void
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
    }

    public function updated(string $key): void
    {
        if (preg_match('/^lines\.(\d+)\.product_id$/', $key, $m)) {
            $index = (int) $m[1];
            $product = Product::find((int) ($this->lines[$index]['product_id'] ?? 0));
            if ($product) {
                $this->lines[$index]['unit_price'] = number_format((float) $product->purchase_price, 2, '.', '');
            }
            return;
        }
    }

    public function lineTotal(array $line): float
    {
        return (float) ($line['quantity'] ?? 0) * (float) ($line['unit_price'] ?? 0);
    }

    public function subtotal(): float
    {
        return collect($this->lines)->sum(fn ($l) => $this->lineTotal($l));
    }

    public function save(): void
    {
        $this->requirePermission('suppliers.manage');
        $validated = $this->validate([
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'bill_number' => 'nullable|string|max:50',
            'bill_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:bill_date',
            'notes' => 'nullable|string|max:2000',
            'lines' => 'required|array|min:1',
            'lines.*.type' => 'required|in:product,service',
            'lines.*.product_id' => 'nullable|integer|exists:products,id',
            'lines.*.description' => 'nullable|string|max:500',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ], [], [
            'supplier_id' => 'المورد',
            'bill_date' => 'تاريخ الفاتورة',
        ]);

        // business rule: product lines need a product; service lines need a description
        $linesCount = 0;
        foreach ($this->lines as $line) {
            if (($line['type'] ?? '') === 'product') {
                if (empty($line['product_id'])) {
                    $this->addError('lines', 'كل سطر منتج يحتاج اختيار منتج');
                    return;
                }
            } else {
                if (trim((string) ($line['description'] ?? '')) === '') {
                    $this->addError('lines', 'كل سطر خدمة يحتاج وصفًا للخدمة');
                    return;
                }
            }
            if ($this->lineTotal($line) <= 0) {
                $this->addError('lines', 'يجب أن تكون قيمة كل سطر أكبر من صفر');
                return;
            }
            $linesCount++;
        }
        if ($linesCount === 0) {
            $this->addError('lines', 'أضف سطرًا واحدًا على الأقل للفاتورة');
            return;
        }

        $total = round($this->subtotal(), 2);

        try {
            DB::transaction(function () use ($total, $validated) {
                $bill = SupplierBill::create([
                    'supplier_id' => (int) $this->supplier_id,
                    'bill_number' => trim($this->bill_number) !== '' ? trim($this->bill_number) : null,
                    'bill_date' => $this->bill_date,
                    'due_date' => $this->due_date !== '' ? $this->due_date : null,
                    'total' => $total,
                    'paid' => 0,
                    'remaining' => $total,
                    'status' => 'unpaid',
                    'notes' => trim($this->notes) !== '' ? trim($this->notes) : null,
                    'created_by' => auth()->id(),
                ]);

                $stockService = app(StockService::class);

                foreach ($this->lines as $index => $line) {
                    $lineTotal = round($this->lineTotal($line), 2);

                    if ($line['type'] === 'product') {
                        $product = Product::whereKey((int) $line['product_id'])->lockForUpdate()->firstOrFail();

                        SupplierBillLine::create([
                            'bill_id' => $bill->id,
                            'product_id' => $product->id,
                            'description' => $product->name,
                            'quantity' => (float) $line['quantity'],
                            'unit_price' => (float) $line['unit_price'],
                            'total' => $lineTotal,
                        ]);

                        $stockService->record(
                            $product,
                            StockMovementType::PURCHASE,
                            (float) $line['quantity'],
                            'فاتورة مورد #' . $bill->id . ($bill->bill_number ? ' — ' . $bill->bill_number : ''),
                            'شراء من المورد #' . $bill->supplier_id,
                        );
                    } else {
                        SupplierBillLine::create([
                            'bill_id' => $bill->id,
                            'product_id' => null,
                            'description' => trim($line['description']),
                            'quantity' => (float) $line['quantity'],
                            'unit_price' => (float) $line['unit_price'],
                            'total' => $lineTotal,
                        ]);
                    }
                }

                session()->flash('success-msg', 'تم حفظ الفاتورة #' . $bill->id . ' وتحديث المخزن والأرصدة');
                $this->redirectRoute('suppliers.show', ['supplier' => $bill->supplier_id, 'tab' => 'bills'], navigate: true);
            });
        } catch (\Throwable $e) {
            $this->addError('lines', 'تعذر حفظ الفاتورة: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderByRaw("CASE kind WHEN 'supply' THEN 0 WHEN 'retail' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'purchase_price', 'kind']);

        return view('suppliers.bills.create', [
            'products' => $products,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.app')->title('الموردون | فاتورة شراء جديدة');
    }
}
