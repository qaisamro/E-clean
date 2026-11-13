<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links a customer to the store (vendor) that registered/owns them.
     *
     * The vendor "store customers" page is derived from Delivered orders only.
     * Customers registered in a store's POS (by the store owner or one of the
     * store's drivers) must belong to that store immediately, before any order
     * is delivered — and be visible in the same customer list. A dedicated
     * pivot table keeps this explicit and avoids polluting the orders table.
     */
    public function up(): void
    {
        Schema::create('store_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('source', 30)->default('pos');
            $table->timestamps();

            $table->unique(['vendor_id', 'customer_id']);
            $table->index('customer_id');

            $table->foreign('vendor_id')
                ->references('id')->on('vendors')
                ->cascadeOnDelete();
            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_customers');
    }
};
