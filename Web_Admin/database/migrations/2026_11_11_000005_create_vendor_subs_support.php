<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

/**
 * Adds support for child ("sub") vendor accounts.
 *
 * A store owner (vendor_admin) may create sub accounts that belong to the
 * same store; the owner grants capabilities permission-by-permission,
 * directly on the child user. This migration only creates the empty link
 * table and the (permissionless) `vendor_sub` role — no data is touched.
 * Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vendor_subs')) {
            Schema::create('vendor_subs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique('user_id');
            });
        }

        Role::findOrCreate('vendor_sub', 'web');
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_subs');
        Role::where('name', 'vendor_sub')->where('guard_name', 'web')->delete();
    }
};
