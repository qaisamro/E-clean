<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds `is_active` so the admin can temporarily disable a category
 * without deleting it. Disabled categories disappear from the
 * products dropdown until re-enabled again.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('sort_order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_categories', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_categories', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
