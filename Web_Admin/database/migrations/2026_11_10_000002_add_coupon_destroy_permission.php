<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Coupons previously had no delete capability (no route, no permission).
 * Adds the coupon.destroy permission and grants it to the roles that already
 * manage coupons. Idempotent: safe to run on a populated database.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Permission::where('name', 'coupon.destroy')->where('guard_name', 'web')->exists()) {
            $permission = Permission::create([
                'name' => 'coupon.destroy',
                'guard_name' => 'web',
            ]);

            // Mirror the roles that hold the other coupon permissions.
            $roleIds = DB::table('role_has_permissions')
                ->whereIn('permission_id', function ($query) {
                    $query->select('id')
                        ->from('permissions')
                        ->whereIn('name', ['coupon.index', 'coupon.edit', 'coupon.update']);
                })
                ->pluck('role_id')
                ->unique();

            foreach ($roleIds as $roleId) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['permission_id' => $permission->id, 'role_id' => $roleId],
                    []
                );
            }
        }
    }

    public function down(): void
    {
        // Non-destructive: leave the permission in place.
    }
};
