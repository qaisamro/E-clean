<?php

namespace App\Livewire\Inventory;


use App\Livewire\Concerns\RequiresPermission;
use App\Enums\ProductKind;
use App\Models\InventoryCategory;
use App\Models\Product;
use Livewire\Component;

class EditProduct extends Component
{
    use RequiresPermission;

    public Product $product;

    public string $name = '';
    public string $sku = '';
    public string $kind = 'retail';
    public string $unit = 'قطعة';
    public ?string $inventory_category_id = null;
    public string $new_category = '';
    public ?string $purchase_price = null;
    public ?string $sale_price = null;
    public string $min_stock_alert = '0';
    public string $notes = '';
    public bool $is_active = true;

    protected array $rules = [
        'name' => 'required|string|min:2|max:255',
        'sku' => 'nullable|string|max:100|unique:products,sku,' . 'CUSTOM_ID',
        'kind' => 'required|in:retail,supply,laundry',
        'unit' => 'required|string|max:50',
        'inventory_category_id' => 'nullable|exists:inventory_categories,id',
        'purchase_price' => 'nullable|numeric|min:0',
        'sale_price' => 'nullable|numeric|min:0',
        'min_stock_alert' => 'required|numeric|min:0',
        'notes' => 'nullable|string|max:2000',
        'is_active' => 'boolean',
    ];

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->sku = $product->sku ?? '';
        $this->kind = $product->kind;
        $this->unit = $product->unit;
        $this->inventory_category_id = $product->inventory_category_id ? (string) $product->inventory_category_id : null;
        $this->purchase_price = $product->purchase_price ?? '';
        $this->sale_price = $product->sale_price ?? '';
        $this->min_stock_alert = (string) $product->min_stock_alert;
        $this->notes = $product->notes ?? '';
        $this->is_active = $product->is_active;
    }

    public function render()
    {
        $categories = InventoryCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Keep the currently-assigned category visible even if it was deactivated since
        if ($this->product->inventory_category_id && ! $categories->contains('id', (int) $this->product->inventory_category_id)) {
            $this->product->loadMissing('category');
            if ($this->product->category) {
                $categories->push($this->product->category);
            }
        }

        return view('inventory.products.edit', [
            'categories' => $categories,
            'kinds' => ProductKind::cases(),
            'units' => ['قطعة', 'علبة', 'كرتون', 'كيلو', 'لتر', 'زجاجة', 'حبة', 'طقم'],
        ])->layout('layouts.app')->title('المخزن | تعديل المنتج');
    }

    public function save(): void
    {
        $this->requirePermission('inventory.manage');
        $this->rules['sku'] = 'nullable|string|max:100|unique:products,sku,' . $this->product->id;
        $this->validate();

        if ($this->new_category !== '') {
            $category = InventoryCategory::firstOrCreate(['name' => trim($this->new_category)]);
            $this->inventory_category_id = (string) $category->id;
        }

        $this->product->update([
            'inventory_category_id' => $this->inventory_category_id ?: null,
            'name' => trim($this->name),
            'sku' => $this->sku !== '' ? trim($this->sku) : null,
            'kind' => $this->kind,
            'unit' => $this->unit,
            'purchase_price' => $this->purchase_price !== '' ? $this->purchase_price : null,
            'sale_price' => $this->sale_price !== '' ? $this->sale_price : null,
            'min_stock_alert' => $this->min_stock_alert,
            'notes' => $this->notes !== '' ? trim($this->notes) : null,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success-msg', 'تم حفظ المنتج');
        $this->redirectRoute('inventory.products.index', navigate: true);
    }
}
