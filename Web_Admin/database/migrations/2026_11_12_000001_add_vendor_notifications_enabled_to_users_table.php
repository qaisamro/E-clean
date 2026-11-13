<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a customer opt out of marketing notifications sent by the stores they
 * ordered from. Transactional notifications (orders, OTP, ...) are unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('vendor_notifications_enabled')
                ->default(true)
                ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('vendor_notifications_enabled');
        });
    }
};
