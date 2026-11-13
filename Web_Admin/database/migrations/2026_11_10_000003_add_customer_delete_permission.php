<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

/**
 * The customers list/edit screens gate the delete button on the
 * customer.delete permission, but it was never created in the Spatie
 * permission store — so the button stayed hidden even for the root
 * (super admin), and config/acl.php was aspirational only.
 *
 * Creates customer.delete and grants it to the roles that already manage
 * customers (customer.edit / customer.update), mirroring coupon.destroy.
 * Idempotent: safe to run on a populated database.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Permission::where('name', 'customer.delete')->where('guard_name', 'web')->exists()) {
            $permission = Permission::create([
                'name' => 'customer.delete',
                'guard_name' => 'web',
            ]);

            $roleIds = DB::table('role_has_permissions')
                ->whereIn('permission_id', function ($query) {
                    $query->select('id')
                        ->from('permissions')
                        ->whereIn('name', ['customer.edit', 'customer.update']);
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
