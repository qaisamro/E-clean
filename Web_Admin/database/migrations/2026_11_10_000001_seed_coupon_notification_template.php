<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Some environments have an empty notification_manages table (the table was
 * recreated by an earlier migration and never re-seeded). Without a
 * "coupon_notify" row the coupon notification was silently skipped, so admins
 * believed customers had been notified when they had not.
 *
 * This inserts the default template only when it is missing, and never
 * overwrites an admin-customised row.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('notification_manages')->where('name', 'coupon_notify')->exists()) {
            DB::table('notification_manages')->insert([
                'title' => 'إشعار كوبون',
                'name' => 'coupon_notify',
                'message' => 'احصل على كوبون خصم على منتجاتنا',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Non-destructive: leave any admin-customised row untouched.
    }
};
