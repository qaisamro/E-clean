<?php

use App\Models\Customer;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per store-owner notification campaign. Per-customer inbox rows stay
 * in the existing notifications table and are linked back to this log.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained((new Vendor())->getTable())->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained((new User())->getTable())->nullOnDelete();
            $table->string('audience', 20);
            $table->foreignId('customer_id')->nullable()->constrained((new Customer())->getTable())->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('message');
            $table->unsignedInteger('recipients_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->unsignedInteger('push_success_count')->default(0);
            $table->unsignedInteger('push_failed_count')->default(0);
            $table->string('push_status', 30)->nullable();
            $table->string('status', 20)->default('sending');
            $table->string('fingerprint', 64)->index();
            $table->string('request_token', 80)->unique();
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_notification_logs');
    }
};
