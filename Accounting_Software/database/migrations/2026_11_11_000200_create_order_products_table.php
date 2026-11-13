<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Product lines added on top of the order (POS product sales).
// Kept separate from order_details so laundry item lines are untouched.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(App\Models\Product::class)->constrained()->cascadeOnDelete();

            $table->decimal('quantity', 12, 2);
            $table->unsignedInteger('price');
            $table->unsignedInteger('total_price');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
