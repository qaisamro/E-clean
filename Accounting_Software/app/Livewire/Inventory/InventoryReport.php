<?php

namespace App\Livewire\Inventory;

use App\Enums\StockMovementType;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class InventoryReport extends Component
{
    public ?string $fromDate = null;
    public ?string $toDate = null;

    public function render()
    {
        $from = $this->fromDate ?: now()->subDays(30)->format('Y-m-d');
        $to = $this->toDate ?: now()->format('Y-m-d');

        $movements = DB::table('stock_movements')
            ->select('type', DB::raw('SUM(quantity) as total_qty'), DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $products = Product::query()->withSum(
            ['stockMovements as purchased' => fn ($q) => $q->where('type', StockMovementType::PURCHASE->value)->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])],
            'quantity'
        )->withSum(
            ['stockMovements as sold' => fn ($q) => $q->where('type', StockMovementType::SALE->value)->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])],
            'quantity'
        )->orderBy('kind')->orderBy('name')->get();

        $productValue = $products->sum(fn ($p) => (float) $p->current_stock * (float) ($p->purchase_price ?? 0));

        $shortageCount = app(\App\Services\StockService::class)->shortageCount();

        return view('inventory.reports', [
            'products' => $products,
            'productValue' => $productValue,
            'shortageCount' => $shortageCount,
            'movements' => $movements,
            'fromDate' => $from,
            'toDate' => $to,
            'types' => StockMovementType::cases(),
        ])->layout('layouts.app')->title('المخزن | التقارير');
    }
}
