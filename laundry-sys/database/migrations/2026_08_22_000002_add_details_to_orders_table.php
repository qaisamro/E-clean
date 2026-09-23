<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('discount')->default(0)->after('total_price');
            $table->text('notes')->nullable()->after('deliver_date');
            $table->string('deliver_time')->nullable()->after('deliver_date');
            $table->date('actual_delivery_date')->nullable()->after('deliver_date');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['discount', 'notes', 'deliver_time', 'actual_delivery_date']);
        });
    }
};
