<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

/**
 * Grantable dashboard capabilities for child (sub) vendor accounts.
 *
 * The store owner now picks, per child account, whether they may see the
 * dashboard, each stat box, and the recent-orders table — via the new
 * "لوحة التحكم والبطاقات" permission group.
 *
 * Existing child accounts are grandfathered: they get every one of these
 * permissions directly on their user, so their dashboard stays fully
 * visible exactly as it is today. Newly created child accounts start
 * with nothing granted. Idempotent.
 */
return new class extends Migration
{
    public const DASHBOARD_PERMISSIONS = [
        'vendor.dashboard',
        'vendor.dashboard.revenue',
        'vendor.dashboard.total_orders',
        'vendor.dashboard.pending',
        'vendor.dashboard.delivered',
        'vendor.dashboard.in_progress',
        'vendor.dashboard.services',
        'vendor.dashboard.products',
        'vendor.dashboard.offers',
        'vendor.dashboard.recent_orders',
    ];

    public function up(): void
    {
        foreach (self::DASHBOARD_PERMISSIONS as $name) {
            Permission::findOrCreate($name, 'web');
        }

        // Grandfather existing sub accounts so their dashboard remains fully
        // visible. From now on the owner controls this from the permission grid.
        foreach (\App\Models\User::role('vendor_sub')->get() as $user) {
            foreach (self::DASHBOARD_PERMISSIONS as $name) {
                $permission = Permission::findByName($name, 'web');
                if (!$user->checkPermissionTo($permission)) {
                    $user->givePermissionTo($permission);
                }
            }
        }
    }

    public function down(): void
    {
        // Keep permissions — child accounts may already rely on them.
    }
};
