<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * The POS "order created" screen lets the store confirm payment, but the
 * order.payment-status permission was never granted to the vendor_admin
 * role — every confirmation attempt was rejected by the permission_check
 * middleware (403) before it reached the controller, so the button did
 * nothing for store owners.
 *
 * Grants order.payment-status to vendor_admin. Idempotent.
 * Root keeps full access; the driver path stays as-is (allow-list).
 */
return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'order.payment-status',
            'guard_name' => 'web',
        ]);

        $role = Role::where('name', 'vendor_admin')->where('guard_name', 'web')->first();
        if ($role && ! $role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        $role = Role::where('name', 'vendor_admin')->where('guard_name', 'web')->first();
        if ($role) {
            $role->revokePermissionTo('order.payment-status');
        }
    }
};
