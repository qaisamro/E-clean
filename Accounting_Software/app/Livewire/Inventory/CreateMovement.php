<?php

namespace App\Livewire\Inventory;


use App\Livewire\Concerns\RequiresPermission;
use App\Enums\StockMovementType;
use App\Enums\ProductKind;
use App\Models\Product;
use App\Services\StockService;
use Livewire\Component;

class CreateMovement extends Component
{
    use RequiresPermission;

    public string $kind = ''; // '' | retail | supply | laundry — filter dropdown only
    public ?string $product_id = null;
    public string $type = 'purchase';
    public string $quantity = '1';
    public string $reference = '';
    public string $notes = '';

    protected array $rules = [
        'kind' => 'nullable|in:retail,supply,laundry',
        'product_id' => 'required|integer|exists:products,id',
        'type' => 'required',
        'quantity' => 'required|numeric|min:0.01',
        'reference' => 'nullable|string|max:100',
        'notes' => 'nullable|string|max:2000',
    ];

    public function render()
    {
        $query = Product::orderBy('kind')->orderBy('name');
        if ($this->kind !== '') {
            $query->where('kind', $this->kind);
        }
        $products = $query->get(['id', 'name', 'current_stock', 'unit', 'kind', 'is_active']);

        return view('inventory.movements.create', [
            'products' => $products,
            'types' => StockMovementType::cases(),
            'kinds' => ProductKind::cases(),
        ])->layout('layouts.app')->title('المخزن | حركة مخزن جديدة');
    }

    public function updatedKind(): void
    {
        $this->reset('product_id');
    }

    public function save(): void
    {
        $this->requirePermission('inventory.manage');
        $this->validate();

        $product = Product::find($this->product_id);
        abort_if(! $product, 404);

        try {
            app(StockService::class)->record(
                $product,
                StockMovementType::from($this->type),
                (float) $this->quantity,
                $this->reference !== '' ? trim($this->reference) : null,
                $this->notes !== '' ? trim($this->notes) : null,
            );
        } catch (\RuntimeException $e) {
            session()->flash('error-msg', $e->getMessage());
            return;
        }

        session()->flash('success-msg', 'تم تسجيل حركة المخزن بنجاح');
        $this->redirectRoute('inventory.movements.index', navigate: true);
    }
}
