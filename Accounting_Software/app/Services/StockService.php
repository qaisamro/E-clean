<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for every stock change in the app.
 *
 * Every movement (in/out/initial/adjustments) is written as an immutable
 * row in the `stock_movements` ledger with the balance *after* applying it,
 * then applied atomically to the stockable's `current_stock` column.
 *
 * Deductions never push the balance negative — they throw instead.
 */
class StockService
{
    public const FORMULAS = [
        'in'  => ['initial', 'purchase', 'adjust_in', 'return_customer'],
        'out' => ['sale', 'consume', 'adjust_out', 'damage', 'return_supplier'],
    ];

    public function currentBalance(Model $stockable): float
    {
        return (float) $stockable->getAttribute('current_stock');
    }

    public function isDeduction(StockMovementType $type): bool
    {
        return $type->isDeduction();
    }

    /**
     * Record a movement and update the stockable's balance atomically.
     *
     * @throws \RuntimeException when a deduction would push the balance below zero
     */
    public function record(Model $stockable, StockMovementType $type, float $quantity, ?string $reference = null, ?string $note = null, ?int $userId = null): StockMovement
    {
        if ($quantity <= 0) {
            throw new \RuntimeException('الكمية يجب أن تكون أكبر من صفر');
        }

        return DB::transaction(function () use ($stockable, $type, $quantity, $reference, $note, $userId) {
            /** @var Model $stockable */
            $stockable = $stockable->newQuery()->whereKey($stockable->getKey())->lockForUpdate()->firstOrFail();

            $current = (float) $stockable->getAttribute('current_stock');

            if ($type->isDeduction()) {
                if ($current < $quantity) {
                    $name = $stockable->getAttribute('name') ?? ('#' . $stockable->getKey());
                    throw new \RuntimeException(
                        "الرصيد غير كافٍ لـ «{$name}» — المتوفر: {$current}، المطلوب: {$quantity}"
                    );
                }
                $balance = $current - $quantity;
            } else {
                $balance = $current + $quantity;
            }

            $movement = new StockMovement([
                'stockable_type' => $stockable->getMorphClass(),
                'stockable_id'   => $stockable->getKey(),
                'type'           => $type,
                'quantity'       => $quantity,
                'balance_after'  => $balance,
                'reference'      => $reference,
                'notes'          => $note,
                'user_id'        => $userId ?? (auth()->check() ? auth()->id() : null),
            ]);
            $movement->save();

            $stockable->setAttribute('current_stock', $balance);
            $stockable->saveQuietly();
            Cache::forget('inventory_shortages_count');

            return $movement;
        });
    }

    public function addIn(Model $stockable, StockMovementType $type, float $quantity, ?string $reference = null, ?string $note = null): StockMovement
    {
        return $this->record($stockable, $type, $quantity, $reference, $note);
    }

    /**
     * Shortage alert count across the unified products table.
     *  - Sellable products (retail) & consumables (supply): alert on empty
     *    or below a user-set minimum.
     *  - Laundry items: alert only when a user configured a minimum AND stock reached it
     *    (laundry items carry no intrinsic stock level — zero is fine by default).
     */
    public function shortageCount(): int
    {
        return Cache::remember('inventory_shortages_count', 300, function () {
            return Product::query()
                ->where('is_active', true)
                ->where(fn ($q) => $q->where(fn ($s) => $s->where('kind', '!=', 'laundry')->where('current_stock', '<=', 0))
                    ->orWhere(fn ($s) => $s->where('min_stock_alert', '>', 0)->whereColumn('current_stock', '<=', 'min_stock_alert')))
                ->count();
        });
    }
}
