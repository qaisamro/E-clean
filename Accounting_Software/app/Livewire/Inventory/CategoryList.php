<?php

namespace App\Livewire\Inventory;


use App\Livewire\Concerns\RequiresPermission;
use App\Models\InventoryCategory;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    use RequiresPermission;

    use WithPagination;

    public string $search = '';
    public string $filter = ''; // '' | active | inactive

    public string $name = '';
    public bool $creating = false;

    public ?int $editingId = null;
    public string $editingName = '';

    protected array $rules = [
        'name' => 'required|string|min:2|max:120',
    ];

    public function render()
    {
        $categories = InventoryCategory::query()
            ->withCount('products')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->filter === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('inventory.categories.index', [
            'categories' => $categories,
        ])->layout('layouts.app')->title('المخزن | إدارة الفئات');
    }

    public function updated($attr): void
    {
        $this->resetPage();
    }

    public function startCreating(): void
    {
        $this->reset(['name', 'editingId', 'editingName']);
        $this->creating = true;
    }

    public function cancelCreating(): void
    {
        $this->reset(['name', 'creating']);
    }

    public function store(): void
    {
        $this->requirePermission('inventory.manage');
        $this->validate(['name' => 'required|string|min:2|max:120|unique:inventory_categories,name']);

        $max = (int) (InventoryCategory::max('sort_order') ?? 0);
        InventoryCategory::create([
            'name' => trim($this->name),
            'sort_order' => $max + 10,
            'is_active' => true,
        ]);

        $this->reset(['name', 'creating']);
        session()->flash('success-msg', 'تمت إضافة الفئة بنجاح');
    }

    public function startEditing(int $id): void
    {
        $category = InventoryCategory::findOrFail($id);
        $this->editingId = $category->id;
        $this->editingName = $category->name;
        $this->creating = false;
    }

    public function cancelEditing(): void
    {
        $this->reset(['editingId', 'editingName']);
    }

    public function update(): void
    {
        $this->requirePermission('inventory.manage');
        $this->validate(['editingName' => 'required|string|min:2|max:120|unique:inventory_categories,name,' . $this->editingId]);

        InventoryCategory::where('id', $this->editingId)->update([
            'name' => trim($this->editingName),
        ]);

        $this->reset(['editingId', 'editingName']);
        session()->flash('success-msg', 'تم حفظ الفئة');
    }

    public function toggleActive(int $id): void
    {
        $this->requirePermission('inventory.manage');
        $cat = InventoryCategory::findOrFail($id);
        $cat->update(['is_active' => ! $cat->is_active]);
        session()->flash('success-msg', $cat->is_active ? 'تم تفعيل الفئة' : 'تم إيقاف الفئة');
    }

    public function moveUp(int $id): void
    {
        $this->requirePermission('inventory.manage');
        $this->swapWithNeighbor($id, -1);
    }

    public function moveDown(int $id): void
    {
        $this->requirePermission('inventory.manage');
        $this->swapWithNeighbor($id, 1);
    }

    private function swapWithNeighbor(int $id, int $direction): void
    {
        $current = InventoryCategory::findOrFail($id);
        $neighbor = InventoryCategory::where('id', '!=', $current->id)
            ->when($direction === -1, fn ($q) => $q->where('sort_order', '<=', $current->sort_order)->orderByDesc('sort_order'))
            ->when($direction === 1, fn ($q) => $q->where('sort_order', '>=', $current->sort_order)->orderBy('sort_order'))
            ->first();

        if (! $neighbor) {
            // If no neighbor exists, normalize the ordering by setting a stable value
            $current->update(['sort_order' => $direction === -1 ? 0 : 100000]);
            return;
        }

        $currentOrder = (int) $current->sort_order;
        $neighborOrder = (int) $neighbor->sort_order;

        if ($currentOrder === $neighborOrder) {
            // Distinguishes between the two; move the neighbor one step away
            $neighbor->update(['sort_order' => $neighborOrder + ($direction === -1 ? 1 : -1)]);
            $neighborOrder = $neighbor->sort_order;
        }

        $current->update(['sort_order' => $neighborOrder]);
        $neighbor->update(['sort_order' => $currentOrder]);
    }

    public function delete(int $id): void
    {
        $this->requirePermission('inventory.manage');
        $category = InventoryCategory::withCount('products')->findOrFail($id);
        $attached = (int) $category->products_count;

        \Illuminate\Support\Facades\DB::transaction(function () use ($category) {
            // Reassign attached products to "بدون فئة" (null)
            $category->products()->update(['inventory_category_id' => null]);
            $category->delete();
        });

        $this->resetPage();
        session()->flash('success-msg',
            $attached > 0
                ? "تم حذف الفئة، وتحويل {$attached} منتج(ات) إلى «بدون فئة»"
                : 'تم حذف الفئة'
        );
    }
}
