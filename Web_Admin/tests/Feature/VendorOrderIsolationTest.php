<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Models\Variant;
use App\Models\Vendor;
use App\Repositories\OrderRepository;
use App\Support\OrderVendorOwnership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorOrderIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_cannot_search_read_mutate_or_print_another_vendors_order(): void
    {
        $storeA = $this->createVendor('store-a');
        $storeB = $this->createVendor('store-b');
        $customer = $this->createCustomer('buyer');
        $orderA = $this->createOrder($customer, $storeA['vendor'], 'ORDER-A-PRIVATE');
        $orderB = $this->createOrder($customer, $storeB['vendor'], 'ORDER-B-PRIVATE');
        $foreignProduct = $this->createProduct($storeB['vendor'], 'Foreign order product');
        $inconsistentOrder = $this->createOrder($customer, $storeA['vendor'], 'ORDER-A-MIXED-PRIVATE');
        $inconsistentOrder->products()->attach($foreignProduct->id, ['quantity' => 1]);
        $this->grant($storeA['user'], [
            'order.index',
            'order.show',
            'order.status.change',
            'orderIncomplete.paid',
            'order.print.invioce',
            'order.print.labels',
        ]);

        $this->actingAs($storeA['user'])
            ->get('/orders?search=ORDER-B-PRIVATE')
            ->assertOk()
            ->assertDontSee('ORDER-B-PRIVATE')
            ->assertDontSee('ORDER-A-MIXED-PRIVATE');

        $this->get("/orders/{$orderB->id}")
            ->assertNotFound()
            ->assertDontSee('ORDER-B-PRIVATE');
        $this->get("/orders/{$inconsistentOrder->id}")
            ->assertNotFound()
            ->assertDontSee('Foreign order product');
        $this->postJson("/orders/{$orderB->id}/status", ['status' => 'delivered'])
            ->assertNotFound()
            ->assertDontSee('ORDER-B-PRIVATE');
        $this->postJson("/orders/{$orderB->id}/paid")
            ->assertNotFound()
            ->assertDontSee('ORDER-B-PRIVATE');
        $this->get("/orders/{$orderB->id}/print-labels")
            ->assertNotFound()
            ->assertDontSee('ORDER-B-PRIVATE');
        $this->get("/orders/{$orderB->id}/print-invoice")
            ->assertNotFound()
            ->assertDontSee('ORDER-B-PRIVATE');

        $this->assertDatabaseHas('orders', [
            'id' => $orderB->id,
            'vendor_id' => $storeB['vendor']->id,
            'order_status' => config('enums.order_status.pending'),
            'payment_status' => config('enums.payment_status.pending'),
        ]);
        $this->get("/orders/{$orderA->id}")
            ->assertOk()
            ->assertSee('ORDER-A-PRIVATE');

        auth()->logout();
        $root = $this->createUser('order-view-root', 'root');
        $this->actingAs($root)
            ->get('/orders')
            ->assertOk()
            ->assertSee('ORDER-A-PRIVATE')
            ->assertSee('ORDER-B-PRIVATE');
    }

    public function test_pos_and_reports_include_only_the_authenticated_vendors_orders(): void
    {
        $storeA = $this->createVendor('pos-a');
        $storeB = $this->createVendor('pos-b');
        $customer = $this->createCustomer('pos-buyer');
        $orderA = $this->createOrder($customer, $storeA['vendor'], 'POS-A-ORDER');
        $orderB = $this->createOrder($customer, $storeB['vendor'], 'POS-B-ORDER');
        $foreignProduct = $this->createProduct($storeB['vendor'], 'POS foreign product');
        $inconsistentOrder = $this->createOrder($customer, $storeA['vendor'], 'POS-A-MIXED');
        $inconsistentOrder->products()->attach($foreignProduct->id, ['quantity' => 1]);
        $this->seed(\Database\Seeders\VendorPosPermissionSeeder::class);
        $this->seed(\Database\Seeders\VendorPosPermissionSeeder::class);
        $this->grant($storeA['user'], ['order.edit', 'order.payment-status']);

        foreach (\Database\Seeders\VendorPosPermissionSeeder::PERMISSIONS as $permission) {
            $this->assertTrue($storeA['user']->fresh()->can($permission));
        }
        $this->assertFalse($storeA['user']->fresh()->can('order.edit.price'));
        $this->assertFalse($customer['user']->can('pos.index'));

        $this->actingAs($storeA['user'])->get('/pos')->assertOk();

        $this->actingAs($storeA['user'])
            ->get('/pos/sales')
            ->assertOk()
            ->assertSee('POS-A-ORDER')
            ->assertDontSee('POS-B-ORDER')
            ->assertDontSee('POS-A-MIXED');

        $this->get("/orders/{$orderB->id}/edit")->assertForbidden();
        $this->postJson("/orders/{$orderB->id}/payment-status", ['payment_status' => 'paid'])
            ->assertForbidden();
        $this->assertDatabaseHas('orders', [
            'id' => $orderB->id,
            'payment_status' => config('enums.payment_status.pending'),
        ]);

        auth()->logout();
        $this->actingAs($storeA['user']);
        $report = (new OrderRepository())->getReportStats(null, null);
        $this->assertSame(1, $report['total_orders']);

        auth()->logout();
        $root = $this->createUser('root-user', 'root');
        $this->actingAs($root);
        $rootReport = (new OrderRepository())->getReportStats(null, null);
        $this->assertSame(3, $rootReport['total_orders']);
    }

    public function test_vendor_cannot_change_other_vendors_catalog_or_payment_state(): void
    {
        $storeA = $this->createVendor('catalog-a');
        $storeB = $this->createVendor('catalog-b');
        $customer = $this->createCustomer('catalog-buyer');
        $productB = $this->createProduct($storeB['vendor'], 'Private product');
        $serviceB = $productB->service;
        $orderB = $this->createOrder($customer, $storeB['vendor'], 'PAYMENT-B-PRIVATE');
        $this->grant($storeA['user'], [
            'product.edit',
            'product.status.toggle',
            'product.delete',
            'service.edit',
            'service.status.toggle',
            'service.delete',
            'payment.process',
            'payment.process.order',
            'payment.update',
        ]);

        $this->actingAs($storeA['user'])
            ->get("/products/{$productB->id}/edit")
            ->assertNotFound()
            ->assertDontSee('Private product');
        $this->post("/products/{$productB->id}/status")->assertNotFound();
        $this->delete("/products/{$productB->id}")->assertNotFound();
        $this->get("/services/{$serviceB->id}/edit")->assertForbidden();
        $this->post("/services/{$serviceB->id}/status")->assertForbidden();
        $this->delete("/services/{$serviceB->id}")->assertForbidden();
        $this->postJson("/payment/process/{$orderB->id}", ['payment_method' => 'stripe'])
            ->assertNotFound()
            ->assertDontSee('PAYMENT-B-PRIVATE');
        $this->postJson("/payment/process-order/{$orderB->id}", ['payment_method' => 'stripe'])
            ->assertNotFound()
            ->assertDontSee('PAYMENT-B-PRIVATE');
        $this->postJson("/payment/{$orderB->id}/update")->assertNotFound();

        $this->assertDatabaseHas('products', [
            'id' => $productB->id,
            'vendor_id' => $storeB['vendor']->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('services', [
            'id' => $serviceB->id,
            'vendor_id' => $storeB['vendor']->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $orderB->id,
            'payment_status' => config('enums.payment_status.pending'),
        ]);
    }

    public function test_driver_account_and_status_changes_require_a_current_same_store_assignment(): void
    {
        $storeA = $this->createVendor('driver-store-a');
        $storeB = $this->createVendor('driver-store-b');
        $customer = $this->createCustomer('driver-buyer');
        $assignedOrder = $this->createOrder($customer, $storeA['vendor'], 'DRIVER-ASSIGNED');
        $unassignedOrder = $this->createOrder($customer, $storeA['vendor'], 'DRIVER-UNASSIGNED');
        $otherStoreOrder = $this->createOrder($customer, $storeB['vendor'], 'DRIVER-OTHER-STORE');
        $foreignProduct = $this->createProduct($storeB['vendor'], 'Driver foreign product');
        $inconsistentOrder = $this->createOrder($customer, $storeA['vendor'], 'DRIVER-MIXED-PRIVATE');
        $inconsistentOrder->products()->attach($foreignProduct->id, ['quantity' => 1]);
        $driverUser = $this->createUser('driver-account', 'driver');
        $driver = Driver::create([
            'user_id' => $driverUser->id,
            'vendor_id' => $storeA['vendor']->id,
            'is_approve' => true,
        ]);
        $driver->orders()->attach($assignedOrder->id, [
            'is_accept' => true,
            'status' => 'delivery',
        ]);
        $driver->orders()->attach($inconsistentOrder->id, [
            'is_accept' => true,
            'status' => 'delivery',
        ]);

        $this->actingAs($driverUser)
            ->get('/driver/account')
            ->assertOk()
            ->assertSee('DRIVER-ASSIGNED')
            ->assertDontSee('DRIVER-UNASSIGNED')
            ->assertDontSee('DRIVER-OTHER-STORE')
            ->assertDontSee('DRIVER-MIXED-PRIVATE');

        $this->postJson("/driver/orders/{$assignedOrder->id}/status", ['status' => 'delivered'])
            ->assertOk()
            ->assertJsonPath('order_status', config('enums.order_status.delivered'));
        $this->get('/driver/account')
            ->assertOk()
            ->assertSee('DRIVER-ASSIGNED')
            ->assertDontSee('DRIVER-OTHER-STORE');
        $this->postJson("/driver/orders/{$unassignedOrder->id}/status", ['status' => 'delivered'])
            ->assertNotFound()
            ->assertDontSee('DRIVER-UNASSIGNED');
        $this->postJson("/driver/orders/{$otherStoreOrder->id}/status", ['status' => 'delivered'])
            ->assertNotFound()
            ->assertDontSee('DRIVER-OTHER-STORE');

        $this->assertDatabaseHas('orders', [
            'id' => $assignedOrder->id,
            'order_status' => config('enums.order_status.delivered'),
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $unassignedOrder->id,
            'order_status' => config('enums.order_status.pending'),
        ]);
    }

    public function test_customer_order_ownership_is_derived_and_mixed_store_products_are_rejected(): void
    {
        $storeA = $this->createVendor('customer-store-a');
        $storeB = $this->createVendor('customer-store-b');
        $productA = $this->createProduct($storeA['vendor'], 'Product A');
        $productB = $this->createProduct($storeB['vendor'], 'Product B');
        $customer = $this->createCustomer('api-buyer');

        $this->getJson("/api/vendors/{$storeA['vendor']->id}/products?search=Product%20B")
            ->assertOk()
            ->assertJsonMissing(['name' => 'Product B']);

        try {
            OrderVendorOwnership::resolveProductVendor([$productA->id, $productB->id]);
            $this->fail('Products from different vendors must not be combined.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('products', $exception->errors());
        }

        $unownedService = Service::create([
            'name' => 'Unowned service',
            'is_active' => true,
        ]);
        $unownedVariant = Variant::create(['name' => 'Unowned variant']);
        $unownedProduct = Product::create([
            'name' => 'Unowned product',
            'service_id' => $unownedService->id,
            'variant_id' => $unownedVariant->id,
            'price' => 8,
            'is_active' => true,
        ]);
        $this->assertNull(OrderVendorOwnership::resolveProductVendor([$unownedProduct->id]));

        try {
            OrderVendorOwnership::resolveProductVendor(
                [$unownedProduct->id],
                (int) $storeA['vendor']->id
            );
            $this->fail('Unknown ownership must not be assigned by inference.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('products', $exception->errors());
        }

        $this->actingAs($customer['user'], 'api')
            ->postJson('/api/orders', [
                'products' => [['id' => $productA->id, 'quantity' => 1]],
                'vendor_id' => $storeB['vendor']->id,
                'pick_date' => today()->addDays(2)->toDateString(),
                'pick_hour' => '08-09',
                'delivery_date' => today()->addDays(3)->toDateString(),
                'delivery_hour' => '08-09',
                'address_id' => $customer['address']->id,
                'payment_type' => \App\Enum\PaymentGateway::CASH->value,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('products');
        $this->assertDatabaseMissing('orders', [
            'customer_id' => $customer['customer']->id,
        ]);

        $request = Request::create('/pos/store', 'POST', [
            'customer_id' => $customer['customer']->id,
            'products' => [[
                'id' => $productA->id,
                'quantity' => 1,
                'price' => 10,
            ]],
            'vendor_id' => $storeB['vendor']->id,
            'address_id' => $customer['address']->id,
            'delivery_charge' => 0,
            'discount' => 0,
            'payment_id' => config('enums.payment_types.cash_on_delivery'),
        ]);
        $root = $this->createUser('order-root', 'root');
        $this->actingAs($root);
        $createdOrder = (new OrderRepository())->PosStoreByRequest($request);

        $this->assertSame((int) $storeA['vendor']->id, (int) $createdOrder->vendor_id);
        $this->assertDatabaseHas('order_products', [
            'order_id' => $createdOrder->id,
            'product_id' => $productA->id,
        ]);
    }

    private function createVendor(string $key): array
    {
        $user = $this->createUser($key . '-owner', 'vendor_admin');
        $vendor = Vendor::create([
            'name' => ucfirst($key),
            'slug' => $key,
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        return compact('user', 'vendor');
    }

    private function createCustomer(string $key): array
    {
        $user = $this->createUser($key, 'customer');
        $customer = Customer::create(['user_id' => $user->id]);
        $address = Address::create([
            'customer_id' => $customer->id,
            'address_name' => $key . ' address',
            'area' => 'Test area',
        ]);

        return compact('user', 'customer', 'address');
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

    private function createOrder(array $customer, Vendor $vendor, string $code): Order
    {
        return Order::create([
            'customer_id' => $customer['customer']->id,
            'address_id' => $customer['address']->id,
            'vendor_id' => $vendor->id,
            'order_code' => $code,
            'prefix' => 'LM',
            'pick_date' => today()->toDateString(),
            'delivery_date' => today()->addDay()->toDateString(),
            'pick_hour' => '08:00:00',
            'delivery_hour' => '08:00:00',
            'amount' => 20,
            'total_amount' => 20,
            'payment_status' => config('enums.payment_status.pending'),
            'order_status' => config('enums.order_status.pending'),
            'payment_type' => config('enums.payment_types.cash_on_delivery'),
        ]);
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

    private function grant(User $user, array $permissions): void
    {
        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }
    }
}
