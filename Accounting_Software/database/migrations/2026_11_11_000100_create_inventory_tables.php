<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Core inventory module:
//  - inventory_categories: product classification (e.g. مسحوق / مواد كيميائية)
//  - products: sellable retail goods + laundry consumables, with live stock balance
//  - stock_movements: immutable stock ledger (every change logged with balance_after)
//  - items: gain stock tracking columns so laundry items can also carry quantities
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique('name');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\InventoryCategory::class, 'inventory_category_id')
                ->nullable()
                ->constrained('inventory_categories')
                ->nullOnDelete();

            $table->string('name');
            $table->string('sku')->nullable()->unique();
            $table->string('kind', 20)->default('retail'); // retail = منتج للبيع / supply = مستلزمات المغسلة
            $table->string('unit')->default('قطعة');

            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();

            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('min_stock_alert', 12, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('kind');
            $table->index('is_active');
            $table->index('current_stock');
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->morphs('stockable'); // products OR laundry items
            $table->string('type', 40);   // see App\Enums\StockMovementType
            $table->decimal('quantity', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->string('reference')->nullable(); // e.g. order code
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('type');
            $table->index('created_at');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('min_stock_alert', 12, 2)->default(0);
            $table->string('unit')->default('قطعة');
            $table->decimal('purchase_price', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['current_stock', 'min_stock_alert', 'unit', 'purchase_price']);
        });
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('products');
        Schema::dropIfExists('inventory_categories');
    }
};
