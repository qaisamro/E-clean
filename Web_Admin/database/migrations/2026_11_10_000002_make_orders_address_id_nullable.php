<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Allow POS orders to be created without a delivery address.
        DB::statement('ALTER TABLE orders MODIFY address_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE orders MODIFY address_id BIGINT UNSIGNED NOT NULL');
    }
};
