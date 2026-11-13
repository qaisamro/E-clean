<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;

class ShortagesList extends Component
{
    public function render()
    {
        // products = entire unified inventory (laundry items + sellable + supplies)
        $products = Product::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->where(fn ($s) => $s->where('kind', '!=', 'laundry')->where('current_stock', '<=', 0))
                ->orWhere(fn ($s) => $s->where('min_stock_alert', '>', 0)->whereColumn('current_stock', '<=', 'min_stock_alert')))
            ->orderBy('current_stock')
            ->get();

        return view('inventory.shortages', [
            'products' => $products,
        ])->layout('layouts.app')->title('المخزن | نواقص المخزون');
    }
}
