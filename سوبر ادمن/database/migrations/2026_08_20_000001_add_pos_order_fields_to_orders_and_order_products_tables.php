<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AddPosOrderFieldsToOrdersAndOrderProductsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('orders', 'payment_method')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('payment_method')->nullable()->after('payment_type');
                $table->timestamp('confirmed_at')->nullable()->after('payment_method');
            });
        }

        if (!Schema::hasColumn('order_products', 'price')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->float('price')->nullable()->after('quantity');
                $table->string('title')->nullable()->after('price');
            });
        }

        foreach (['order.edit', 'order.edit.price', 'order.delete', 'order.payment-status'] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Grant the newly added permissions to the configured roles and their users,
        // mirroring RolePermissionSeeder so @can('order.edit') works immediately.
        foreach (['order.edit', 'order.update', 'order.edit.price', 'order.delete', 'order.payment-status'] as $permission) {
            $roles = config('acl.permissions.' . $permission, []);

            foreach ($roles as $roleName) {
                $role = Role::where('name', $roleName)->first();

                if ($role) {
                    $role->givePermissionTo($permission);

                    foreach (User::role($roleName)->get() as $user) {
                        $user->givePermissionTo($permission);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('orders', 'payment_method')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['payment_method', 'confirmed_at']);
            });
        }

        if (Schema::hasColumn('order_products', 'price')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->dropColumn(['price', 'title']);
            });
        }
    }
}