<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Store owners (vendor_admin) were shown no "Profile" entry in the
 * sidebar because they lacked the profile.* permissions that gate the
 * @can('profile.index') item. Grants the complete profile permission
 * set to vendor_admin. Idempotent — safe on a populated database.
 */
return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'profile.index',
            'profile.edit',
            'profile.update',
            'profile.change-password',
            'profile.change-password.form',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $role = Role::where('name', 'vendor_admin')->where('guard_name', 'web')->first();
        if ($role) {
            $ids = Permission::whereIn('name', $permissions)->pluck('id')->all();
            foreach ($ids as $id) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['permission_id' => $id, 'role_id' => $role->id],
                    []
                );
            }
        }
    }

    public function down(): void
    {
        $role = Role::where('name', 'vendor_admin')->where('guard_name', 'web')->first();
        if ($role) {
            foreach (['profile.index', 'profile.edit', 'profile.update', 'profile.change-password', 'profile.change-password.form'] as $name) {
                $permission = Permission::where('name', $name)->where('guard_name', 'web')->first();
                if ($permission) {
                    DB::table('role_has_permissions')
                        ->where('permission_id', $permission->id)
                        ->where('role_id', $role->id)
                        ->delete();
                }
            }
        }
    }
};
