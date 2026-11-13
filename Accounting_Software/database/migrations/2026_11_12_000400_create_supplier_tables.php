<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Suppliers module: suppliers, purchase bills (material + service lines)
 * and supplier payments. Money columns are DECIMAL(12,2). A bill line that
 * references a product records a PURCHASE stock movement on save (services
 * are accounting-only). Opening balance is in "we owe them" terms.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('notes')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0); // we owe them this amount since capture
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('supplier_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('bill_number')->nullable(); // supplier's own invoice number (optional)
            $table->date('bill_date');
            $table->date('due_date')->nullable();
            $table->decimal('total', 12, 2);
            $table->decimal('paid', 12, 2)->default(0);
            $table->decimal('remaining', 12, 2);
            $table->string('status')->default('unpaid'); // unpaid | partial | paid
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['supplier_id', 'bill_date']);
        });

        Schema::create('supplier_bill_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('supplier_bills')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();

            $table->index(['bill_id']);
        });

        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bill_id')->nullable()->constrained('supplier_bills')->nullOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->string('method')->default('cash'); // cash | bank | cheque | other
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['supplier_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('supplier_bill_lines');
        Schema::dropIfExists('supplier_bills');
        Schema::dropIfExists('suppliers');
    }
};
