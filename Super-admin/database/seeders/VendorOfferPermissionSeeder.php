<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class VendorOfferPermissionSeeder extends Seeder
{
    /**
     * Grant offer permissions to vendor_admin (store owners)
     * so they can manage their own offers from the dashboard.
     *
     * @return void
     */
    public function run()
    {
        $offerPermissions = [
            'offer.index',
            'offer.create',
            'offer.store',
            'offer.edit',
            'offer.update',
            'offer.destroy',
            'offer.status.toggle',
        ];

        foreach ($offerPermissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = ['vendor_admin', 'root'];
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($offerPermissions);
                $users = \App\Models\User::role($roleName)->get();
                foreach ($users as $user) {
                    $user->givePermissionTo($offerPermissions);
                }
            }
        }
    }
}