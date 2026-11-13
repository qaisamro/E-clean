<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Web-checkout orders (guest + customer web checkout) attached products
 * WITHOUT price/title in order_products, so their pivot rows stayed NULL.
 * Invoice/order builders multiplied NULL by quantity and rendered 0.
 * Backfills existing rows: price = discount_price (when > 0) else price,
 * title = product name. Idempotent — safe on a populated database.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('order_products as op')
            ->join('products as p', 'p.id', '=', 'op.product_id')
            ->whereNull('op.price')
            ->update([
                'op.price' => DB::raw('COALESCE(NULLIF(p.discount_price, 0), p.price)'),
            ]);

        if (Schema::hasColumn('order_products', 'title')) {
            DB::table('order_products as op')
                ->join('products as p', 'p.id', '=', 'op.product_id')
                ->whereNull('op.title')
                ->orWhere('op.title', '')
                ->update(['op.title' => DB::raw('p.name')]);
        }
    }

    public function down(): void
    {
        // Non-destructive: backfilled prices stay — they reflect the
        // catalog at repair time, which is the correct historical record.
    }
};
