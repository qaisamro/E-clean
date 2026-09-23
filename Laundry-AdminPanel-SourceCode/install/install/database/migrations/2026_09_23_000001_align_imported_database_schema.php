<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vendors')) {
            Schema::create('vendors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->text('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->unsignedBigInteger('logo_media_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamps();
            });
        }

        $columns = [
            'banners' => [
                'vendor_id' => fn (Blueprint $table) => $table->unsignedBigInteger('vendor_id')->nullable(),
            ],
            'coupons' => [
                'vendor_id' => fn (Blueprint $table) => $table->unsignedBigInteger('vendor_id')->nullable(),
            ],
            'orders' => [
                'vendor_id' => fn (Blueprint $table) => $table->unsignedBigInteger('vendor_id')->nullable(),
                'payment_method' => fn (Blueprint $table) => $table->string('payment_method')->nullable(),
                'confirmed_at' => fn (Blueprint $table) => $table->timestamp('confirmed_at')->nullable(),
            ],
            'order_products' => [
                'price' => fn (Blueprint $table) => $table->double('price')->nullable(),
                'title' => fn (Blueprint $table) => $table->string('title')->nullable(),
            ],
            'products' => [
                'vendor_id' => fn (Blueprint $table) => $table->unsignedBigInteger('vendor_id')->nullable(),
            ],
            'services' => [
                'vendor_id' => fn (Blueprint $table) => $table->unsignedBigInteger('vendor_id')->nullable(),
            ],
        ];

        foreach ($columns as $tableName => $tableColumns) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($tableColumns as $columnName => $definition) {
                if (!Schema::hasColumn($tableName, $columnName)) {
                    Schema::table($tableName, $definition);
                }
            }
        }
    }

    public function down(): void
    {
        foreach ([
            'banners' => ['vendor_id'],
            'coupons' => ['vendor_id'],
            'orders' => ['vendor_id', 'payment_method', 'confirmed_at'],
            'order_products' => ['price', 'title'],
            'products' => ['vendor_id'],
            'services' => ['vendor_id'],
        ] as $tableName => $columnNames) {
            if (Schema::hasTable($tableName)) {
                foreach ($columnNames as $columnName) {
                    if (Schema::hasColumn($tableName, $columnName)) {
                        Schema::table($tableName, fn (Blueprint $table) => $table->dropColumn($columnName));
                    }
                }
            }
        }

        Schema::dropIfExists('vendors');
    }
};