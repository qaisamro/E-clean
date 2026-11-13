<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class VendorStoreCustomerRefreshTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('root', 'web');
        Role::findOrCreate('vendor_admin', 'web');
        Role::findOrCreate('customer', 'web');
    }

    public function test_pending_order_customer_is_hidden_until_the_order_is_delivered(): void
    {
        $store = $this->createVendor('refresh-a');
        $customer = $this->createCustomer('Refresh Buyer');

        $order = $this->createOrder($customer, $store['vendor'], 'REFRESH-A', config('enums.order_status.pending'));

        $this->actingAs($store['user'])
            ->get(route('vendor.customers.index'))
            ->assertOk()
            ->assertDontSee('Refresh Buyer');

        $order->update(['order_status' => config('enums.order_status.delivered')]);

        $this->actingAs($store['user'])
            ->get(route('vendor.customers.index'))
            ->assertOk()
            ->assertSee('Refresh Buyer');
    }

    public function test_vendor_customers_page_is_not_cached(): void
    {
        $store = $this->createVendor('cache-a');
        $customer = $this->createCustomer('Cache Buyer');
        $this->createOrder($customer, $store['vendor'], 'CACHE-A', config('enums.order_status.delivered'));

        $response = $this->actingAs($store['user'])
            ->get(route('vendor.customers.index'))
            ->assertOk();

        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
    }

    public function test_vendor_customers_page_contains_quiet_auto_refresh_markup(): void
    {
        $store = $this->createVendor('markup-a');
        $customer = $this->createCustomer('Markup Buyer');
        $this->createOrder($customer, $store['vendor'], 'MARKUP-A', config('enums.order_status.delivered'));

        $response = $this->actingAs($store['user'])
            ->get(route('vendor.customers.index'))
            ->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('id="vc-summary-row"', $content);
        $this->assertStringContainsString('id="vc-table-body"', $content);
        $this->assertStringContainsString('id="vc-pagination"', $content);
        $this->assertStringContainsString('id="vc-refresh-now"', $content);
        $this->assertStringContainsString('REFRESH_MS = 30000', $content);
        $this->assertStringContainsString('visibilitychange', $content);
    }

    public function test_search_filter_is_preserved_on_refresh_endpoint(): void
    {
        $store = $this->createVendor('search-refresh-a');
        $alice = $this->createCustomer('Alice Refreshable');
        $bob = $this->createCustomer('Bob Refreshable');
        $this->createOrder($alice, $store['vendor'], 'SR-A-1', config('enums.order_status.delivered'));
        $this->createOrder($bob, $store['vendor'], 'SR-A-2', config('enums.order_status.delivered'));

        $this->actingAs($store['user'])
            ->get(route('vendor.customers.index', ['search' => 'Alice']))
            ->assertOk()
            ->assertSee('Alice Refreshable')
            ->assertDontSee('Bob Refreshable');
    }

    private function createVendor(string $key): array
    {
        $user = $this->createUser($key . ' owner', 'vendor_admin');
        $vendor = Vendor::create([
            'name' => 'Store ' . $key,
            'slug' => $key,
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        return compact('user', 'vendor');
    }

    private function createCustomer(string $name): array
    {
        $key = Str::slug($name);
        $user = $this->createUser($name, 'customer');
        $customer = Customer::create(['user_id' => $user->id]);
        Address::create([
            'customer_id' => $customer->id,
            'address_name' => $name . ' address',
            'area' => 'Test area',
        ]);

        return compact('user', 'customer', 'key');
    }

    private function createUser(string $name, string $role): User
    {
        $key = Str::slug($name) . '-' . Str::random(6);
        $user = User::create([
            'first_name' => $name,
            'last_name' => 'Test',
            'mobile' => $key . '-mobile',
            'email' => $key . '@example.test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate($role, 'web'));

        return $user;
    }

    private function createOrder(array $customer, ?Vendor $vendor, string $code, string $status): Order
    {
        return Order::create([
            'customer_id' => $customer['customer']->id,
            'address_id' => Address::where('customer_id', $customer['customer']->id)->value('id'),
            'vendor_id' => $vendor?->id,
            'order_code' => $code,
            'prefix' => 'LM',
            'pick_date' => today()->toDateString(),
            'delivery_date' => today()->addDay()->toDateString(),
            'pick_hour' => '08:00:00',
            'delivery_hour' => '08:00:00',
            'amount' => 20,
            'total_amount' => 20,
            'payment_status' => config('enums.payment_status.pending'),
            'order_status' => $status,
            'payment_type' => config('enums.payment_types.cash_on_delivery'),
        ]);
    }
}
