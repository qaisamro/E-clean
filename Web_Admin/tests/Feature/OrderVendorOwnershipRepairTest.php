<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Models\Variant;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderVendorOwnershipRepairTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_command_repairs_only_orders_with_consistent_product_ownership(): void
    {
        $vendorA = Vendor::create(['name' => 'Store A', 'slug' => 'store-a', 'is_active' => true]);
        $vendorB = Vendor::create(['name' => 'Store B', 'slug' => 'store-b', 'is_active' => true]);
        $customerUser = $this->createUser('buyer', 'customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $address = Address::create(['customer_id' => $customer->id, 'address_name' => 'addr', 'area' => 'area']);

        $productA = $this->createProduct($vendorA, 'Product A');
        $productB = $this->createProduct($vendorB, 'Product B');

        $repairable = Order::create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'vendor_id' => null,
            'order_code' => 'REPAIR-ME',
            'prefix' => 'LM',
            'pick_date' => now()->toDateString(),
            'delivery_date' => now()->addDay()->toDateString(),
            'pick_hour' => '08:00:00',
            'delivery_hour' => '08:00:00',
            'amount' => 10,
            'total_amount' => 10,
            'payment_status' => 'Pending',
            'order_status' => 'Pending',
            'payment_type' => 'cash',
        ]);
        $repairable->products()->attach($productA->id, ['quantity' => 1]);

        $mixed = Order::create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'vendor_id' => null,
            'order_code' => 'MIXED',
            'prefix' => 'LM',
            'pick_date' => now()->toDateString(),
            'delivery_date' => now()->addDay()->toDateString(),
            'pick_hour' => '08:00:00',
            'delivery_hour' => '08:00:00',
            'amount' => 20,
            'total_amount' => 20,
            'payment_status' => 'Pending',
            'order_status' => 'Pending',
            'payment_type' => 'cash',
        ]);
        $mixed->products()->attach([$productA->id => ['quantity' => 1], $productB->id => ['quantity' => 1]]);

        Artisan::call('orders:audit-vendor-ownership', ['--fix' => true]);

        $this->assertDatabaseHas('orders', [
            'id' => $repairable->id,
            'vendor_id' => $vendorA->id,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $mixed->id,
            'vendor_id' => null,
        ]);
    }

    public function test_soft_deleted_product_does_not_hide_an_owned_order(): void
    {
        $vendor = Vendor::create(['name' => 'Store A', 'slug' => 'store-a', 'is_active' => true]);
        $owner = $this->createUser('owner', 'vendor_admin');
        $vendor->update(['user_id' => $owner->id]);
        $owner->assignRole(Role::findOrCreate('vendor_admin', 'web'));
        \Spatie\Permission\Models\Permission::findOrCreate('order.show', 'web');
        $owner->givePermissionTo('order.show');

        $customerUser = $this->createUser('buyer', 'customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $address = Address::create(['customer_id' => $customer->id, 'address_name' => 'addr', 'area' => 'area']);

        $product = $this->createProduct($vendor, 'Deleted product');
        $product->delete();

        $order = Order::create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'vendor_id' => $vendor->id,
            'order_code' => 'SOFT-DELETED-PRODUCT',
            'prefix' => 'LM',
            'pick_date' => now()->toDateString(),
            'delivery_date' => now()->addDay()->toDateString(),
            'pick_hour' => '08:00:00',
            'delivery_hour' => '08:00:00',
            'amount' => 10,
            'total_amount' => 10,
            'payment_status' => 'Pending',
            'order_status' => 'Pending',
            'payment_type' => 'cash',
        ]);
        $order->products()->attach($product->id, ['quantity' => 1]);

        $this->actingAs($owner)
            ->get("/orders/{$order->id}")
            ->assertOk()
            ->assertSee('SOFT-DELETED-PRODUCT');
    }

    private function createUser(string $key, string $role): User
    {
        $user = User::create([
            'first_name' => ucfirst($key),
            'last_name' => 'Test',
            'mobile' => $key . '-mobile',
            'email' => $key . '@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate($role, 'web'));
        return $user;
    }

    private function createProduct(Vendor $vendor, string $name): Product
    {
        $service = Service::create([
            'name' => $name . ' service',
            'vendor_id' => $vendor->id,
            'is_active' => true,
        ]);
        $variant = Variant::create(['name' => $name . ' variant']);
        return Product::create([
            'name' => $name,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'vendor_id' => $vendor->id,
            'price' => 10,
            'is_active' => true,
        ]);
    }
}