<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('Pending','Order confirmed','Picked your order','Processing','Cancelled','Delivered','Ready for pickup','On the way') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('Pending','Order confirmed','Picked your order','Processing','Cancelled','Delivered','Ready for pickup') NOT NULL");
    }
};
