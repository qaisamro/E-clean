<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

/**
 * Pre-create every permission from the sub-vendor catalog so permission
 * checks (`can()`) behave deterministically for child accounts — spatie
 * throws PermissionDoesNotExist for unknown names instead of returning
 * false. Idempotent: findOrCreate semantics.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (\App\Support\VendorSubPermissions::all() as $name) {
            Permission::findOrCreate($name, 'web');
        }
    }

    public function down(): void
    {
        // Removing catalog permissions would strip any grants already made
        // to child accounts; keep them on rollback.
    }
};
