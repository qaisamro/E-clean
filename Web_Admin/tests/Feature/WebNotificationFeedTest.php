<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\CustomerJoinRequest;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\Support\RefreshCityDatabase;
use Tests\TestCase;

class WebNotificationFeedTest extends TestCase
{
    use RefreshCityDatabase;

    public function test_vendor_and_driver_feeds_are_strictly_scoped_to_their_store_and_assignment(): void
    {
        $storeA = $this->createVendor('notify-a');
        $storeB = $this->createVendor('notify-b');
        $customer = $this->createCustomer('notify-buyer');
        $orderA = $this->createOrder($customer, $storeA['vendor'], 'NOTIFY-A');
        $orderB = $this->createOrder($customer, $storeB['vendor'], 'NOTIFY-B');

        $this->actingAs($storeA['user'])
            ->getJson(route('web-notifications.feed'))
            ->assertOk()
            ->assertJsonFragment(['key' => 'order:' . $orderA->id])
            ->assertJsonMissing(['key' => 'order:' . $orderB->id]);

        $driverUser = $this->createUser('notify-driver', 'driver');
        $driver = Driver::create([
            'user_id' => $driverUser->id,
            'vendor_id' => $storeA['vendor']->id,
            'is_approve' => true,
        ]);
        $driver->orders()->attach($orderA->id, [
            'status' => 'delivery',
            'is_accept' => false,
        ]);

        $response = $this->actingAs($driverUser)
            ->getJson(route('web-notifications.feed'))
            ->assertOk()
            ->assertJsonFragment(['type' => 'driver_order']);

        $keys = collect($response->json('data.items'))->pluck('key');
        $this->assertTrue($keys->contains(fn ($key) => str_starts_with($key, 'driver-order:' . $orderA->id . ':')));
        $this->assertFalse($keys->contains(fn ($key) => str_contains($key, 'driver-order:' . $orderB->id . ':')));
    }

    public function test_super_admin_receives_mobile_join_requests_but_vendor_does_not(): void
    {
        $root = $this->createUser('notify-root', 'root');
        $store = $this->createVendor('notify-store');
        $customer = $this->createCustomer('notify-order-buyer');
        $order = $this->createOrder($customer, $store['vendor'], 'ROOT-MUST-NOT-SEE');
        $mobileApplicant = $this->createUser('mobile-applicant', 'customer');
        $webApplicant = $this->createUser('web-applicant', 'customer');

        $mobileRequest = CustomerJoinRequest::create([
            'user_id' => $mobileApplicant->id,
            'status' => CustomerJoinRequest::STATUS_PENDING,
            'request_metadata' => ['source' => 'mobile_app'],
        ]);
        CustomerJoinRequest::create([
            'user_id' => $webApplicant->id,
            'status' => CustomerJoinRequest::STATUS_PENDING,
            'request_metadata' => ['source' => 'website'],
        ]);

        $this->actingAs($root)
            ->getJson(route('web-notifications.feed'))
            ->assertOk()
            ->assertJsonFragment([
                'key' => 'customer-join-request:' . $mobileRequest->id,
                'type' => 'customer_join_request',
            ])
            ->assertJsonMissing(['key' => 'order:' . $order->id])
            ->assertJsonMissing(['type' => 'order'])
            ->assertJsonMissing(['message' => 'طلب انضمام جديد من Web-applicant Test عبر التطبيق']);

        $this->actingAs($store['user'])
            ->getJson(route('web-notifications.feed'))
            ->assertOk()
            ->assertJsonMissing(['type' => 'customer_join_request']);
    }

    public function test_unsupported_accounts_cannot_read_the_web_notification_feed(): void
    {
        $customer = $this->createUser('feed-customer', 'customer');

        $this->actingAs($customer)
            ->getJson(route('web-notifications.feed'))
            ->assertForbidden();
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
            'is_show' => false,
        ]);
    }
}
