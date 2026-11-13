<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Records whether an account was created under an "OTP required at
     * registration" policy.
     *
     * Why a marker column instead of checking mobile_verified_at directly:
     * accounts created before this policy (or while OTP was disabled) have a
     * NULL mobile_verified_at and must keep full access when the admin enables
     * OTP later. Only accounts registered while OTP was required are gated,
     * so existing customers are never locked out.
     *
     * Additive and safe: existing rows default to 0 (not required).
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'phone_verification_required')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('phone_verification_required')->default(0)->after('mobile_verified_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'phone_verification_required')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('phone_verification_required');
            });
        }
    }
};
