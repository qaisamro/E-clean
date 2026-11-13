<?php

namespace App\Livewire\Inventory;


use App\Livewire\Concerns\RequiresPermission;
use App\Enums\ProductKind;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ProductList extends Component
{
    use RequiresPermission;

    use WithPagination;

    public string $search = '';

    #[Url(as: 'kind', history: true)]
    public string $kind = '';
    public string $isActive = '';
    public bool $lowStockOnly = false;

    public function render()
    {
        $products = Product::query()
            ->with('category')
            ->when($this->search, function ($q) {
                $q->where(fn ($s) => $s->where('name', 'like', "%{$this->search}%")->orWhere('sku', 'like', "%{$this->search}%"));
            })
            ->when($this->kind, fn ($q) => $q->where('kind', $this->kind))
            ->when($this->isActive !== '', fn ($q) => $q->where('is_active', $this->isActive === '1'))
            ->when($this->lowStockOnly, fn ($q) => $q
                ->where(fn ($s) => $s->where(fn ($sub) => $sub->where('kind', '!=', 'laundry')->where('current_stock', '<=', 0))
                    ->orWhere(fn ($ss) => $ss->where('min_stock_alert', '>', 0)->whereColumn('current_stock', '<=', 'min_stock_alert'))))
            ->orderBy('name');

        return view('inventory.products.index', [
            'products' => $products->paginate(config('app.default_pagination')),
            'kinds' => ProductKind::cases(),
        ])->layout('layouts.app')->title('المخزن | المنتجات');
    }

    public function updated($attr): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'kind', 'isActive', 'lowStockOnly']);
        $this->resetPage();
    }

    public function toggleActive(Product $product): void
    {
        $this->requirePermission('inventory.manage');
        $product->update(['is_active' => ! $product->is_active]);
        session()->flash('success-msg', 'تم تحديث حالة المنتج');
    }

    public function deleteProduct(int $id): void
    {
        $this->requirePermission('inventory.manage');
        $product = Product::find($id);
        if ($product) {
            $product->delete();
        }
        session()->flash('success-msg', 'تم حذف المنتج');
    }
}
