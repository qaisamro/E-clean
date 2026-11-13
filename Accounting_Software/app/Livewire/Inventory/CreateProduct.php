<?php

namespace App\Livewire\Inventory;


use App\Livewire\Concerns\RequiresPermission;
use App\Enums\ProductKind;
use App\Enums\StockMovementType;
use App\Models\InventoryCategory;
use App\Models\Product;
use App\Services\StockService;
use Livewire\Attributes\Url;
use Livewire\Component;

class CreateProduct extends Component
{
    use RequiresPermission;

    public string $name = '';
    public string $sku = '';

    #[Url(as: 'kind', history: false)]
    public string $kind = 'retail';
    public string $unit = 'قطعة';
    public ?string $inventory_category_id = null;
    public string $new_category = '';
    public ?string $purchase_price = null;
    public ?string $sale_price = null;
    public string $current_stock = '0';
    public string $min_stock_alert = '0';
    public string $notes = '';

    protected array $rules = [
        'name' => 'required|string|min:2|max:255',
        'sku' => 'nullable|string|max:100|unique:products,sku',
        'kind' => 'required|in:retail,supply,laundry',
        'unit' => 'required|string|max:50',
        'inventory_category_id' => 'nullable|exists:inventory_categories,id',
        'purchase_price' => 'nullable|numeric|min:0',
        'sale_price' => 'nullable|numeric|min:0',
        'current_stock' => 'required|numeric|min:0',
        'min_stock_alert' => 'required|numeric|min:0',
        'notes' => 'nullable|string|max:2000',
    ];

    public function render()
    {
        return view('inventory.products.create', [
            'categories' => InventoryCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'kinds' => ProductKind::cases(),
            'units' => ['قطعة', 'علبة', 'كرتون', 'كيلو', 'لتر', 'زجاجة', 'حبة', 'طقم', 'علبة'],
        ])->layout('layouts.app')->title('المخزن | إضافة منتج');
    }

    public function save(): void
    {
        $this->requirePermission('inventory.manage');
        $this->validate();

        if ($this->new_category !== '') {
            $category = InventoryCategory::firstOrCreate(['name' => trim($this->new_category)]);
            $this->inventory_category_id = (string) $category->id;
        }

        $product = Product::create([
            'inventory_category_id' => $this->inventory_category_id ?: null,
            'name' => trim($this->name),
            'sku' => $this->sku !== '' ? trim($this->sku) : null,
            'kind' => $this->kind,
            'unit' => $this->unit,
            'purchase_price' => $this->purchase_price !== '' ? $this->purchase_price : null,
            'sale_price' => $this->sale_price !== '' ? $this->sale_price : null,
            'current_stock' => 0,
            'min_stock_alert' => $this->min_stock_alert,
            'notes' => $this->notes !== '' ? trim($this->notes) : null,
            'is_active' => true,
        ]);

        $opening = (float) $this->current_stock;
        if ($opening > 0) {
            app(StockService::class)->record($product, StockMovementType::INITIAL, $opening, null, 'رصيد افتتاحي عند إضافة المنتج');
        }

        session()->flash('success-msg', 'تم إضافة المنتج بنجاح');
        $this->redirectRoute('inventory.products.index', navigate: true);
    }
}
