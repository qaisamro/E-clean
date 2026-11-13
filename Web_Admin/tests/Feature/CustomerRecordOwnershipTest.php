<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerRecordOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_read_or_update_another_customers_order(): void
    {
        $owner = $this->createCustomer('owner');
        $other = $this->createCustomer('other');
        $ownerOrder = $this->createOrder($owner['customer'], $owner['address']);
        $otherOrder = $this->createOrder($other['customer'], $other['address']);

        $this->actingAs($owner['user'], 'api')
            ->getJson('/api/orders')
            ->assertOk()
            ->assertJsonFragment(['id' => $ownerOrder->id])
            ->assertJsonMissing(['id' => $otherOrder->id]);

        $this->getJson("/api/orders/{$otherOrder->id}/details")->assertNotFound();
        $this->putJson("/api/orders/{$otherOrder->id}", ['products' => []])->assertNotFound();

        $this->assertDatabaseHas('orders', [
            'id' => $otherOrder->id,
            'customer_id' => $other['customer']->id,
            'order_status' => $otherOrder->order_status,
        ]);
    }

    public function test_customer_cannot_view_update_or_delete_another_customers_address(): void
    {
        $owner = $this->createCustomer('owner');
        $other = $this->createCustomer('other');

        $this->actingAs($owner['user'], 'api')
            ->getJson('/api/addresses')
            ->assertOk()
            ->assertJsonFragment(['id' => $owner['address']->id])
            ->assertJsonMissing(['id' => $other['address']->id]);

        $addressInput = [
            'address_name' => 'Changed by another user',
            'area' => 'Test area',
        ];

        $this->postJson("/api/addresses/{$other['address']->id}", $addressInput)
            ->assertNotFound();
        $this->deleteJson("/api/addresses/{$other['address']->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('addresses', [
            'id' => $other['address']->id,
            'customer_id' => $other['customer']->id,
            'address_name' => $other['address']->address_name,
        ]);
    }

    public function test_customer_cannot_read_or_change_another_customers_notification(): void
    {
        $owner = $this->createCustomer('owner');
        $other = $this->createCustomer('other');
        $ownerNotification = Notification::create([
            'customer_id' => $owner['customer']->id,
            'title' => 'Owner notification',
            'message' => 'Private message',
            'isRead' => false,
        ]);
        $otherNotification = Notification::create([
            'customer_id' => $other['customer']->id,
            'title' => 'Other notification',
            'message' => 'Other private message',
            'isRead' => false,
        ]);

        $this->actingAs($owner['user'], 'api')
            ->getJson('/api/notification')
            ->assertOk()
            ->assertJsonFragment(['id' => $ownerNotification->id])
            ->assertJsonMissing(['id' => $otherNotification->id]);

        $this->actingAs($other['user'], 'api')
            ->postJson("/api/notification/{$ownerNotification->id}")
            ->assertNotFound();
        $this->deleteJson("/api/notification/{$ownerNotification->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('notifications', [
            'id' => $ownerNotification->id,
            'customer_id' => $owner['customer']->id,
            'isRead' => false,
        ]);

        $this->actingAs($owner['user'], 'api')
            ->postJson("/api/notification/{$ownerNotification->id}")
            ->assertOk();
        $this->assertDatabaseHas('notifications', [
            'id' => $ownerNotification->id,
            'isRead' => true,
        ]);

        $this->deleteJson("/api/notification/{$ownerNotification->id}")->assertOk();
        $this->assertDatabaseMissing('notifications', ['id' => $ownerNotification->id]);
    }

    public function test_order_creation_rejects_an_address_owned_by_another_customer(): void
    {
        $customer = $this->createCustomer('buyer');
        $other = $this->createCustomer('other');

        $this->actingAs($customer['user'], 'api')
            ->postJson('/api/orders', [
                'products' => [],
                'pick_date' => '2026-12-01',
                'pick_hour' => '08:00:00',
                'delivery_date' => '2026-12-03',
                'delivery_hour' => '08:00:00',
                'address_id' => $other['address']->id,
                'payment_type' => config('enums.payment_types.cash_on_delivery'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['address_id']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_website_customer_cannot_view_or_delete_another_customers_records(): void
    {
        $owner = $this->createCustomer('owner');
        $other = $this->createCustomer('other');
        $ownerOrder = $this->createOrder($owner['customer'], $owner['address']);
        $otherOrder = $this->createOrder($other['customer'], $other['address']);

        $this->actingAs($owner['user'])
            ->get('/account/my-orders')
            ->assertOk()
            ->assertSee($ownerOrder->order_code);

        $this->actingAs($other['user'])
            ->get('/account/my-orders')
            ->assertOk()
            ->assertSee($otherOrder->order_code)
            ->assertDontSee($ownerOrder->order_code);

        $this->getJson('/account/addresses/list')
            ->assertOk()
            ->assertJsonFragment(['id' => $other['address']->id])
            ->assertJsonMissing(['id' => $owner['address']->id]);

        $this->post("/account/addresses/{$owner['address']->id}/default")
            ->assertRedirect();
        $this->assertDatabaseHas('addresses', [
            'id' => $owner['address']->id,
            'customer_id' => $owner['customer']->id,
            'is_default' => false,
        ]);

        $this->post("/account/addresses/{$owner['address']->id}/destroy")
            ->assertRedirect();
        $this->assertDatabaseHas('addresses', [
            'id' => $owner['address']->id,
            'customer_id' => $owner['customer']->id,
        ]);

        $this->actingAs($other['user'])
            ->get("/account/orders/{$ownerOrder->id}")
            ->assertNotFound();
    }

    public function test_only_customer_role_can_use_customer_api_routes(): void
    {
        $customer = $this->createCustomer('customer');
        $driver = $this->createUserWithRole('driver-user', 'driver');
        $admin = $this->createUserWithRole('admin-user', 'admin');

        $this->actingAs($customer['user'], 'api')
            ->getJson('/api/addresses')
            ->assertOk();

        $this->actingAs($driver, 'api')
            ->getJson('/api/addresses')
            ->assertForbidden();

        $this->actingAs($admin, 'api')
            ->getJson('/api/addresses')
            ->assertForbidden();
    }

    private function createCustomer(string $key): array
    {
        $user = $this->createUserWithRole($key, 'customer');
        $customer = Customer::create(['user_id' => $user->id]);
        $address = Address::create([
            'customer_id' => $customer->id,
            'address_name' => "{$key} address",
            'area' => "{$key} area",
        ]);

        return compact('user', 'customer', 'address');
    }

    private function createUserWithRole(string $key, string $roleName): User
    {
        $user = User::create([
            'first_name' => ucfirst($key),
            'last_name' => 'Test',
            'mobile' => "{$key}-mobile",
            'email' => "{$key}@example.test",
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $role = Role::findOrCreate($roleName, 'web');
        $user->assignRole($role);

        return $user;
    }

    private function createOrder(Customer $customer, Address $address): Order
    {
        return Order::create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'order_code' => 'ORD-' . $customer->id . '-' . $address->id,
            'prefix' => 'LM',
            'pick_date' => '2026-12-01',
            'delivery_date' => '2026-12-03',
            'pick_hour' => '08:00:00',
            'delivery_hour' => '08:00:00',
            'amount' => 20,
            'total_amount' => 20,
            'payment_status' => config('enums.payment_status.pending'),
            'order_status' => config('enums.order_status.pending'),
            'payment_type' => config('enums.payment_types.cash_on_delivery'),
        ]);
    }
}
