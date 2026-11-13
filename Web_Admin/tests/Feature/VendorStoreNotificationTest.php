<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\DeviceKey;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Models\Variant;
use App\Models\Vendor;
use App\Models\VendorNotificationLog;
use App\Services\FirebaseMessagingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorStoreNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('root', 'web');
        Role::findOrCreate('vendor_admin', 'web');
        Role::findOrCreate('customer', 'web');
    }

    protected function tearDown(): void
    {
        \Carbon\Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_store_customers_are_derived_only_from_delivered_orders(): void
    {
        $storeA = $this->createVendor('alpha');
        $storeB = $this->createVendor('bravo');
        $customerA = $this->createCustomer('Alpha Buyer');
        $shared = $this->createCustomer('Shared Buyer');
        $customerB = $this->createCustomer('Bravo Buyer');

        // Pending / incomplete orders do not create membership.
        $this->createOrder($customerA, $storeA['vendor'], 'PENDING-A', config('enums.order_status.pending'));
        // First successful order creates the relation.
        $this->createOrder($customerA, $storeA['vendor'], 'DELIVERED-A-1', config('enums.order_status.delivered'));
        // Repeated successful orders never duplicate the customer.
        $this->createOrder($customerA, $storeA['vendor'], 'DELIVERED-A-2', config('enums.order_status.delivered'));
        // A customer can belong to more than one store.
        $this->createOrder($shared, $storeA['vendor'], 'SHARED-A', config('enums.order_status.delivered'));
        $this->createOrder($shared, $storeB['vendor'], 'SHARED-B', config('enums.order_status.delivered'));
        $this->createOrder($customerB, $storeB['vendor'], 'DELIVERED-B', config('enums.order_status.delivered'));

        $response = $this->actingAs($storeA['user'])
            ->get(route('vendor.customers.index'))
            ->assertOk()
            ->assertSee('Alpha Buyer')
            ->assertSee('Shared Buyer')
            ->assertDontSee('Bravo Buyer');

        $listed = collect($response->viewData('customers')->items());
        $this->assertCount(2, $listed);
        $this->assertSame(2, $response->viewData('summary')['customers']);
        $this->assertSame(3, $response->viewData('summary')['orders']);

        $this->actingAs($storeB['user'])
            ->get(route('vendor.customers.index'))
            ->assertOk()
            ->assertSee('Bravo Buyer')
            ->assertSee('Shared Buyer')
            ->assertDontSee('Alpha Buyer');
    }

    public function test_legacy_and_inconsistent_orders_are_classified_by_product_ownership(): void
    {
        $storeA = $this->createVendor('legacy-a');
        $storeB = $this->createVendor('legacy-b');
        $productA = $this->createProduct($storeA['vendor'], 'Legacy A product');
        $productB = $this->createProduct($storeB['vendor'], 'Legacy B product');

        $legacy = $this->createCustomer('Legacy Buyer');
        $legacyOrder = $this->createOrder($legacy, null, 'LEGACY-NULL', config('enums.order_status.delivered'));
        $legacyOrder->products()->attach($productA->id, ['quantity' => 1]);

        $mixed = $this->createCustomer('Mixed Buyer');
        $mixedOrder = $this->createOrder($mixed, null, 'LEGACY-MIXED', config('enums.order_status.delivered'));
        $mixedOrder->products()->attach($productA->id, ['quantity' => 1]);
        $mixedOrder->products()->attach($productB->id, ['quantity' => 1]);

        $explicitMixed = $this->createCustomer('Explicit Mixed Buyer');
        $explicitOrder = $this->createOrder($explicitMixed, $storeA['vendor'], 'EXPLICIT-MIXED', config('enums.order_status.delivered'));
        $explicitOrder->products()->attach($productB->id, ['quantity' => 1]);

        $noProducts = $this->createCustomer('No Product Buyer');
        $this->createOrder($noProducts, $storeA['vendor'], 'NO-PRODUCTS', config('enums.order_status.delivered'));

        $response = $this->actingAs($storeA['user'])
            ->get(route('vendor.customers.index'))
            ->assertOk()
            ->assertSee('Legacy Buyer')
            ->assertSee('No Product Buyer')
            ->assertDontSee('Mixed Buyer')
            ->assertDontSee('Explicit Mixed Buyer');

        $this->assertSame(2, $response->viewData('summary')['customers']);
    }

    public function test_notification_page_renders_only_the_stores_customers(): void
    {
        $storeA = $this->createVendor('page-a');
        $storeB = $this->createVendor('page-b');
        $own = $this->createCustomer('Own Page Customer');
        $foreign = $this->createCustomer('Foreign Page Customer');
        $this->createOrder($own, $storeA['vendor'], 'PAGE-A', config('enums.order_status.delivered'));
        $this->createOrder($foreign, $storeB['vendor'], 'PAGE-B', config('enums.order_status.delivered'));

        $this->actingAs($storeA['user'])
            ->get(route('vendor.notifications.index'))
            ->assertOk()
            ->assertSee('Own Page Customer')
            ->assertDontSee('Foreign Page Customer')
            ->assertSee('سجل الإشعارات المرسلة');

        // Preselecting a customer through the URL also stays inside the store.
        $this->actingAs($storeA['user'])
            ->get(route('vendor.notifications.index', ['customer_id' => $own['customer']->id]))
            ->assertOk()
            ->assertSee('Own Page Customer');

        $this->actingAs($storeA['user'])
            ->get(route('vendor.notifications.index', ['customer_id' => $foreign['customer']->id]))
            ->assertOk()
            ->assertDontSee('Foreign Page Customer');
    }

    public function test_customer_search_is_scoped_to_the_authenticated_store(): void
    {
        $storeA = $this->createVendor('search-a');
        $storeB = $this->createVendor('search-b');
        $alice = $this->createCustomer('Alice Searchable');
        $bob = $this->createCustomer('Bob Searchable');
        $charlie = $this->createCustomer('Charlie Foreign');

        $this->createOrder($alice, $storeA['vendor'], 'SEARCH-A-1', config('enums.order_status.delivered'));
        $this->createOrder($bob, $storeA['vendor'], 'SEARCH-A-2', config('enums.order_status.delivered'));
        $this->createOrder($charlie, $storeB['vendor'], 'SEARCH-B-1', config('enums.order_status.delivered'));

        $this->actingAs($storeA['user'])
            ->get(route('vendor.customers.index', ['search' => 'Alice']))
            ->assertOk()
            ->assertSee('Alice Searchable')
            ->assertDontSee('Bob Searchable')
            ->assertDontSee('Charlie Foreign');

        $this->actingAs($storeA['user'])
            ->get(route('vendor.customers.index', ['search' => 'Searchable']))
            ->assertOk()
            ->assertSee('Alice Searchable')
            ->assertSee('Bob Searchable')
            ->assertDontSee('Charlie Foreign');
    }

    public function test_vendor_sends_a_single_in_app_and_push_notification_to_an_own_customer(): void
    {
        $storeA = $this->createVendor('send-a');
        $customer = $this->createCustomer('Single Recipient');
        $this->createOrder($customer, $storeA['vendor'], 'SEND-A', config('enums.order_status.delivered'));
        DeviceKey::create(['customer_id' => $customer['customer']->id, 'key' => 'token-single']);

        $this->mock(FirebaseMessagingService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendMany')
                ->once()
                ->withArgs(function (array $tokens) {
                    return $tokens === ['token-single'];
                })
                ->andReturnUsing(fn (array $tokens) => [
                    'status' => 'sent',
                    'message' => 'ok',
                    'success' => count($tokens),
                    'failed' => 0,
                ]);
        });

        $this->actingAs($storeA['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'single',
                'customer_id' => $customer['customer']->id,
                'message' => 'رسالة خاصة لعميل المتجر',
                'request_token' => (string) Str::uuid(),
            ])
            ->assertRedirect(route('vendor.notifications.index'))
            ->assertSessionHas('success');

        $log = VendorNotificationLog::firstOrFail();
        $this->assertSame('sent', $log->status);
        $this->assertSame('single', $log->audience);
        $this->assertSame(1, $log->recipients_count);
        $this->assertSame(0, $log->skipped_count);
        $this->assertSame('sent', $log->push_status);
        $this->assertSame(1, $log->push_success_count);

        $this->assertDatabaseHas('notifications', [
            'customer_id' => $customer['customer']->id,
            'vendor_id' => $storeA['vendor']->id,
            'vendor_notification_log_id' => $log->id,
            'title' => $storeA['vendor']->name,
            'message' => 'رسالة خاصة لعميل المتجر',
            'isRead' => 0,
        ]);
        $this->assertSame(1, Notification::count());
    }

    public function test_vendor_cannot_send_to_another_stores_customer(): void
    {
        $storeA = $this->createVendor('block-a');
        $storeB = $this->createVendor('block-b');
        $foreign = $this->createCustomer('Foreign Recipient');
        $this->createOrder($foreign, $storeB['vendor'], 'BLOCK-B', config('enums.order_status.delivered'));

        $this->mock(FirebaseMessagingService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('sendMany');
        });

        $this->actingAs($storeA['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'single',
                'customer_id' => $foreign['customer']->id,
                'message' => 'محاولة إرسال لعميل متجر آخر',
                'request_token' => (string) Str::uuid(),
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('customer_id');

        $this->assertDatabaseCount('vendor_notification_logs', 0);
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_bulk_send_reaches_all_own_customers_and_respects_preferences(): void
    {
        $storeA = $this->createVendor('bulk-a');
        $storeB = $this->createVendor('bulk-b');
        $enabled = $this->createCustomer('Enabled Customer');
        $disabled = $this->createCustomer('Disabled Customer');
        $foreign = $this->createCustomer('Foreign Customer');

        $this->createOrder($enabled, $storeA['vendor'], 'BULK-A-1', config('enums.order_status.delivered'));
        $this->createOrder($disabled, $storeA['vendor'], 'BULK-A-2', config('enums.order_status.delivered'));
        $this->createOrder($foreign, $storeB['vendor'], 'BULK-B-1', config('enums.order_status.delivered'));

        $disabled['user']->update(['vendor_notifications_enabled' => false]);

        DeviceKey::create(['customer_id' => $enabled['customer']->id, 'key' => 'token-enabled']);
        DeviceKey::create(['customer_id' => $disabled['customer']->id, 'key' => 'token-disabled']);
        DeviceKey::create(['customer_id' => $foreign['customer']->id, 'key' => 'token-foreign']);

        $this->mock(FirebaseMessagingService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendMany')
                ->once()
                ->withArgs(function (array $tokens) {
                    return $tokens === ['token-enabled'];
                })
                ->andReturnUsing(fn (array $tokens) => [
                    'status' => 'sent',
                    'message' => 'ok',
                    'success' => count($tokens),
                    'failed' => 0,
                ]);
        });

        $this->actingAs($storeA['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'all',
                'message' => 'عرض جديد لكل عملاء المتجر',
                'request_token' => (string) Str::uuid(),
            ])
            ->assertRedirect(route('vendor.notifications.index'));

        $log = VendorNotificationLog::firstOrFail();
        $this->assertSame('sent', $log->status);
        $this->assertSame(1, $log->recipients_count);
        $this->assertSame(1, $log->skipped_count);

        $this->assertDatabaseHas('notifications', [
            'customer_id' => $enabled['customer']->id,
            'vendor_id' => $storeA['vendor']->id,
        ]);
        $this->assertDatabaseMissing('notifications', [
            'customer_id' => $disabled['customer']->id,
        ]);
        $this->assertDatabaseMissing('notifications', [
            'customer_id' => $foreign['customer']->id,
        ]);
        $this->assertSame(1, Notification::count());
    }

    public function test_single_send_to_a_customer_who_disabled_vendor_notifications_is_skipped(): void
    {
        $store = $this->createVendor('disabled-a');
        $customer = $this->createCustomer('Opted Out Customer');
        $this->createOrder($customer, $store['vendor'], 'DISABLED-A', config('enums.order_status.delivered'));
        $customer['user']->update(['vendor_notifications_enabled' => false]);

        $this->mock(FirebaseMessagingService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('sendMany');
        });

        $this->actingAs($store['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'single',
                'customer_id' => $customer['customer']->id,
                'message' => 'لن يستلمها العميل',
                'request_token' => (string) Str::uuid(),
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $log = VendorNotificationLog::firstOrFail();
        $this->assertSame('skipped', $log->status);
        $this->assertSame(0, $log->recipients_count);
        $this->assertSame(1, $log->skipped_count);
        $this->assertSame(0, Notification::count());
    }

    public function test_duplicate_messages_cooldown_and_request_tokens_are_blocked(): void
    {
        $store = $this->createVendor('limits-a');
        $customer = $this->createCustomer('Rate Limited Customer');
        $this->createOrder($customer, $store['vendor'], 'LIMITS-A', config('enums.order_status.delivered'));

        $this->mock(FirebaseMessagingService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendMany')
                ->twice()
                ->andReturnUsing(fn (array $tokens) => [
                    'status' => 'skipped',
                    'message' => 'No device tokens.',
                    'success' => 0,
                    'failed' => 0,
                ]);
        });

        $firstToken = (string) Str::uuid();
        $payload = [
            'audience' => 'single',
            'customer_id' => $customer['customer']->id,
            'message' => 'رسالة متكررة للاختبار',
            'request_token' => $firstToken,
        ];

        $this->actingAs($store['user'])
            ->post(route('vendor.notifications.send'), $payload)
            ->assertRedirect(route('vendor.notifications.index'));
        $this->assertSame(1, VendorNotificationLog::count());

        // The same form token cannot be submitted twice.
        $this->actingAs($store['user'])
            ->post(route('vendor.notifications.send'), $payload)
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertSame(1, VendorNotificationLog::count());

        // A different campaign is blocked during the short cooldown.
        $this->actingAs($store['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'single',
                'customer_id' => $customer['customer']->id,
                'message' => 'رسالة مختلفة أثناء فترة الانتظار',
                'request_token' => (string) Str::uuid(),
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertSame(1, VendorNotificationLog::count());

        // Cooldown passed, but an identical message is still inside the duplicate window.
        $this->travel(21)->seconds();
        $this->actingAs($store['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'single',
                'customer_id' => $customer['customer']->id,
                'message' => 'رسالة متكررة للاختبار',
                'request_token' => (string) Str::uuid(),
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertSame(1, VendorNotificationLog::count());

        // After the duplicate window, the same message may be sent again intentionally.
        $this->travel(10)->minutes();
        $this->actingAs($store['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'single',
                'customer_id' => $customer['customer']->id,
                'message' => 'رسالة متكررة للاختبار',
                'request_token' => (string) Str::uuid(),
            ])
            ->assertRedirect(route('vendor.notifications.index'));
        $this->assertSame(2, VendorNotificationLog::count());
    }

    public function test_message_length_and_audience_are_validated(): void
    {
        $store = $this->createVendor('valid-a');
        $customer = $this->createCustomer('Validation Customer');
        $this->createOrder($customer, $store['vendor'], 'VALID-A', config('enums.order_status.delivered'));

        $this->actingAs($store['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'all',
                'message' => str_repeat('x', 256),
                'request_token' => (string) Str::uuid(),
            ])
            ->assertSessionHasErrors('message');

        $this->actingAs($store['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'all',
                'message' => '',
                'request_token' => (string) Str::uuid(),
            ])
            ->assertSessionHasErrors('message');

        $this->actingAs($store['user'])
            ->post(route('vendor.notifications.send'), [
                'audience' => 'everyone',
                'message' => 'رسالة صحيحة',
                'request_token' => (string) Str::uuid(),
            ])
            ->assertSessionHasErrors('audience');

        $this->assertDatabaseCount('vendor_notification_logs', 0);
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_customer_can_update_the_vendor_notification_preference_through_the_api(): void
    {
        $customer = $this->createCustomer('Preference Customer');

        $this->actingAs($customer['user'], 'api')
            ->postJson('/api/users/update', [
                'first_name' => 'Preference',
                'mobile' => '0555000123',
                'vendor_notifications_enabled' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.user.vendor_notifications_enabled', false);

        $this->assertDatabaseHas('users', [
            'id' => $customer['user']->id,
            'vendor_notifications_enabled' => false,
        ]);

        $this->actingAs($customer['user'], 'api')
            ->postJson('/api/users/update', [
                'first_name' => 'Preference',
                'mobile' => '0555000123',
                'vendor_notifications_enabled' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.user.vendor_notifications_enabled', true);

        $this->assertDatabaseHas('users', [
            'id' => $customer['user']->id,
            'vendor_notifications_enabled' => true,
        ]);
    }

    public function test_non_vendor_roles_cannot_access_the_vendor_notification_area(): void
    {
        $customer = $this->createCustomer('No Access Customer');
        $root = $this->createUser('Root User', 'root');

        $this->actingAs($customer['user'])
            ->get(route('vendor.customers.index'))
            ->assertForbidden();

        $this->actingAs($root)
            ->get(route('vendor.customers.index'))
            ->assertForbidden();

        $vendorAdminWithoutStore = $this->createUser('Storeless Owner', 'vendor_admin');
        $this->actingAs($vendorAdminWithoutStore)
            ->get(route('vendor.notifications.index'))
            ->assertNotFound();
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
        $address = Address::create([
            'customer_id' => $customer->id,
            'address_name' => $name . ' address',
            'area' => 'Test area',
        ]);

        return compact('user', 'customer', 'address', 'key');
    }

    private function createUser(string $name, string $role): User
    {
        $key = Str::slug($name) . '-' . Str::random(6);
        $user = User::create([
            'first_name' => $name,
            'last_name' => 'Test',
            'mobile' => $key . '-mobile',
            'email' => $key . '@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate($role, 'web'));

        return $user;
    }

    private function createOrder(
        array $customer,
        ?Vendor $vendor,
        string $code,
        string $status
    ): Order {
        return Order::create([
            'customer_id' => $customer['customer']->id,
            'address_id' => $customer['address']->id,
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
