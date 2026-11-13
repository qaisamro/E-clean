<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class VendorPosPermissionSeeder extends Seeder
{
    public const PERMISSIONS = [
        'pos.index',
        'pos.sales',
        'pos.store',
        'pos.payment',
        'pos.customerStore',
        'pos.addressStore',
        'pos.fetch.variants',
        'pos.fetch.products',
    ];

    public function run(): void
    {
        $role = Role::where('name', 'vendor_admin')->where('guard_name', 'web')->first();
        if (!$role) {
            return;
        }

        DB::transaction(function () use ($role) {
            foreach (self::PERMISSIONS as $name) {
                $role->givePermissionTo(Permission::findOrCreate($name, 'web'));
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
