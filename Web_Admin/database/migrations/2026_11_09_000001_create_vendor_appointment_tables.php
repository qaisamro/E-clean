<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_appointment_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('settings');
            $table->timestamps();
        });
        Schema::create('order_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained();
            $table->dateTime('pickup_start');
            $table->dateTime('pickup_end');
            $table->dateTime('delivery_start');
            $table->dateTime('delivery_end');
            $table->unsignedInteger('processing_minutes');
            $table->json('reschedule_history')->nullable();
            $table->timestamps();
            $table->index(['vendor_id', 'pickup_start']);
            $table->index(['vendor_id', 'delivery_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_appointments');
        Schema::dropIfExists('vendor_appointment_settings');
    }
};
