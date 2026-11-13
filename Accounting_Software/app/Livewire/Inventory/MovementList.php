<?php

namespace App\Livewire\Inventory;

use App\Enums\StockMovementType;
use App\Enums\ProductKind;
use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class MovementList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $kind = ''; // '' | retail | supply | laundry
    public ?string $fromDate = null;
    public ?string $toDate = null;

    public function render()
    {
        $movements = StockMovement::query()
            ->with(['stockable', 'user'])
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->kind, fn ($q) => $q->where('stockable_type', Product::class)
                ->whereHasMorph('stockable', [Product::class], fn ($sq) => $sq->where('kind', $this->kind)))
            ->when($this->fromDate, fn ($q) => $q->whereDate('created_at', '>=', $this->fromDate))
            ->when($this->toDate, fn ($q) => $q->whereDate('created_at', '<=', $this->toDate))
            ->when($this->search, function ($q) {
                $q->where(function ($s) {
                    $s->where('reference', 'like', "%{$this->search}%")
                        ->orWhere('notes', 'like', "%{$this->search}%")
                        ->orWhere(fn ($sub) => $sub->where('stockable_type', Product::class)->whereHasMorph('stockable', [Product::class], fn ($sq) => $sq->where('name', 'like', "%{$this->search}%")->orWhere('sku', 'like', "%{$this->search}%")));
                });
            })
            ->latest();

        return view('inventory.movements.index', [
            'movements' => $movements->paginate(config('app.default_pagination')),
            'types' => StockMovementType::cases(),
            'kinds' => ProductKind::cases(),
        ])->layout('layouts.app')->title('المخزن | حركات المخزن');
    }

    public function updated($attr): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'type', 'kind', 'fromDate', 'toDate']);
        $this->resetPage();
    }
}
