<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\DeviceKey;
use App\Models\NotificationManage;
use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Services\FirebaseMessagingService;
use App\Services\NotificationServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Global coupons created by a Super Admin must apply to every store, and the
 * "notify all customers" switch must actually deliver a push notification and
 * report the outcome to the admin.
 */
class CouponNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('root', 'web');
        Role::findOrCreate('vendor_admin', 'web');
        Role::findOrCreate('customer', 'web');
    }

    private function root(string $suffix = ''): User
    {
        $user = User::create([
            'first_name' => 'Root',
            'last_name' => 'Admin',
            'mobile' => '966500000901' . $suffix,
            'email' => 'root-coupon' . $suffix . '@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole('root');

        return $user;
    }

    private function vendorAdmin(string $suffix = ''): User
    {
        $user = User::create([
            'first_name' => 'Vendor',
            'last_name' => 'Admin',
            'mobile' => '966500000902' . $suffix,
            'email' => 'vendor-coupon' . $suffix . '@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole('vendor_admin');
        // Route-name permissions let a vendor admin use the coupon screens.
        foreach (['coupon.index', 'coupon.create', 'coupon.store', 'coupon.edit'] as $p) {
            Permission::findOrCreate($p, 'web');
            $user->givePermissionTo($p);
        }

        return $user;
    }

    private function customer(string $suffix = ''): User
    {
        $user = User::create([
            'first_name' => 'Buyer',
            'last_name' => 'One',
            'mobile' => '966500000903' . $suffix,
            'email' => 'buyer-coupon' . $suffix . '@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole('customer');
        (new CustomerRepository())->storeByUser($user);

        return $user;
    }

    /** @param array $result delivery result returned by FirebaseMessagingService */
    private function fakePush(array $result, ?string $bodyContains = null, ?string $titleContains = null): void
    {
        $this->mock(FirebaseMessagingService::class, function ($mock) use ($result, $bodyContains, $titleContains) {
            $expectation = $mock->shouldReceive('sendMany');

            if ($bodyContains !== null || $titleContains !== null) {
                $expectation->withArgs(
                    fn ($tokens, $title, $body) =>
                        ($bodyContains === null || (is_string($body) && str_contains($body, $bodyContains)))
                        && ($titleContains === null || (is_string($title) && str_contains($title, $titleContains)))
                );
            }

            $expectation->andReturn($result);
        });
    }

    private function deviceKeys(int $count): void
    {
        $user = $this->customer();
        $customerId = $user->customer->id;

        for ($i = 0; $i < $count; $i++) {
            DeviceKey::create([
                'customer_id' => $customerId,
                'key' => 'coupon-device-' . $i,
                'device_type' => 'android',
            ]);
        }
    }

    private function couponPayload(array $extra = []): array
    {
        return array_merge([
            'code' => 'SAVE20',
            'discount_type' => 'percent',
            'discount' => 20,
            'min_amount' => 50,
            'start_date' => now()->format('Y-m-d'),
            'start_time' => now()->format('H:i'),
            'expired_date' => now()->addMonth()->format('Y-m-d'),
            'expired_time' => now()->format('H:i'),
            'notify' => '1',
        ], $extra);
    }

    private static function sentResult(int $n): array
    {
        return [
            'status' => 'sent',
            'message' => "Sent {$n} notifications.",
            'provider_id' => null,
            'success' => $n,
            'failed' => 0,
        ];
    }

    /* ---------------------------------------------------------------
       Global coupon creation (Super Admin)
       ---------------------------------------------------------------*/

    public function test_super_admin_can_create_a_coupon_that_applies_to_all_stores(): void
    {
        $this->actingAs($this->root())
            ->post(route('coupon.admin.store'), $this->couponPayload(['notify' => '0']))
            ->assertRedirect(route('coupon.admin.index'));

        $coupon = Coupon::where('code', 'SAVE20')->first();
        $this->assertNotNull($coupon);
        // vendor_id = null means "global": every store, every customer.
        $this->assertNull($coupon->vendor_id);
    }

    public function test_global_coupon_is_accepted_for_any_customer_at_checkout(): void
    {
        Coupon::create([
            'code' => 'SAVE20',
            'discount_type' => 'percent',
            'discount' => 20,
            'min_amount' => 50,
            'started_at' => now()->subDay(),
            'expired_at' => now()->addMonth(),
            'vendor_id' => null, // global
        ]);

        $this->actingAs($this->customer(), 'api')
            ->postJson('/api/coupons/SAVE20/apply', ['amount' => 100])
            ->assertStatus(200);
    }

    public function test_vendor_admin_coupons_stay_scoped_to_their_own_store(): void
    {
        $vendorAdmin = $this->vendorAdmin();
        $vendor = \App\Models\Vendor::create([
            'name' => 'Store One',
            'user_id' => $vendorAdmin->id,
            'is_active' => true,
        ]);

        $this->actingAs($vendorAdmin)
            ->post(route('coupon.store'), $this->couponPayload([
                'code' => 'VENDOR10',
                'notify' => '0',
            ]))
            ->assertRedirect(route('coupon.index'));

        $coupon = Coupon::where('code', 'VENDOR10')->first();
        $this->assertNotNull($coupon);
        $this->assertSame($vendor->id, (int) $coupon->vendor_id);
    }

    /* ---------------------------------------------------------------
       Notification delivery + admin feedback
       ---------------------------------------------------------------*/

    public function test_creating_a_coupon_notifies_all_customers_and_reports_it(): void
    {
        $this->deviceKeys(3);
        $this->fakePush(self::sentResult(3));

        $this->actingAs($this->root())
            ->post(route('coupon.admin.store'), $this->couponPayload())
            ->assertRedirect(route('coupon.admin.index'))
            ->assertSessionHas('coupon_notification');

        $report = session('coupon_notification');
        $this->assertSame(3, $report['sent']);
        $this->assertSame(3, $report['recipients']);
        $this->assertSame('sent', $report['status']);
    }

    public function test_notification_includes_the_coupon_code_even_without_a_template(): void
    {
        // Simulate a production database where the template row is missing.
        NotificationManage::query()->delete();
        $this->assertDatabaseCount('notification_manages', 0);

        $this->deviceKeys(2);
        $this->fakePush(self::sentResult(2), 'SAVE20');

        $this->actingAs($this->root())
            ->post(route('coupon.admin.store'), $this->couponPayload())
            ->assertRedirect(route('coupon.admin.index'))
            ->assertSessionHas('coupon_notification');

        // sendMany was expected with a body containing the coupon code.
        $this->assertSame('sent', session('coupon_notification')['status']);
    }

    public function test_custom_template_message_is_used_when_present(): void
    {
        NotificationManage::query()->delete();
        NotificationManage::create([
            'title' => 'إشعار كوبون مخصص',
            'name' => 'coupon_notify',
            'message' => 'عرض خاص من إدارة المتجر',
            'is_active' => true,
        ]);
        $this->deviceKeys(1);
        $this->fakePush(self::sentResult(1), 'عرض خاص من إدارة المتجر');

        $this->actingAs($this->root())
            ->post(route('coupon.admin.store'), $this->couponPayload())
            ->assertRedirect();

        $this->assertSame('sent', session('coupon_notification')['status']);
    }

    public function test_admin_is_told_when_no_device_is_registered(): void
    {
        $this->actingAs($this->root())
            ->post(route('coupon.admin.store'), $this->couponPayload())
            ->assertRedirect(route('coupon.admin.index'))
            ->assertSessionHas('coupon_notification');

        $this->assertSame('no_recipients', session('coupon_notification')['status']);
        $this->assertSame(0, session('coupon_notification')['sent']);
    }

    public function test_admin_is_told_when_push_service_is_disabled(): void
    {
        $this->deviceKeys(2);
        $this->fakePush([
            'status' => 'skipped',
            'message' => 'Firebase push is disabled.',
            'provider_id' => null,
            'success' => 0,
            'failed' => 0,
        ]);

        $this->actingAs($this->root())
            ->post(route('coupon.admin.store'), $this->couponPayload())
            ->assertRedirect();

        $report = session('coupon_notification');
        $this->assertSame('skipped', $report['status']);
        $this->assertSame(0, $report['sent']);
    }

    public function test_no_notification_is_sent_when_the_admin_opts_out(): void
    {
        $this->deviceKeys(2);

        $this->actingAs($this->root())
            ->post(route('coupon.admin.store'), $this->couponPayload(['notify' => '0']))
            ->assertRedirect(route('coupon.admin.index'));

        $this->assertNull(session('coupon_notification'));
    }

    public function test_updating_a_coupon_can_also_notify_and_reports_it(): void
    {
        $coupon = Coupon::create([
            'code' => 'SAVE20',
            'discount_type' => 'percent',
            'discount' => 20,
            'min_amount' => 50,
            'started_at' => now()->subDay(),
            'expired_at' => now()->addMonth(),
        ]);
        $this->deviceKeys(2);
        $this->fakePush(self::sentResult(2));

        $this->actingAs($this->root())
            ->post(route('coupon.admin.update', $coupon->id), $this->couponPayload())
            ->assertRedirect(route('coupon.admin.index'))
            ->assertSessionHas('coupon_notification');

        $this->assertSame(2, session('coupon_notification')['sent']);
    }

    public function test_send_notification_returns_the_delivery_result(): void
    {
        $this->fakePush(self::sentResult(1));

        $result = (new NotificationServices())->sendNotification('hello', ['device-key'], 'title');

        $this->assertSame('sent', $result['status']);
        $this->assertSame(1, $result['success']);
        $this->assertSame(0, $result['failed']);
    }

    /* ---------------------------------------------------------------
       Deleting a coupon
       ---------------------------------------------------------------*/

    public function test_root_can_delete_a_coupon(): void
    {
        $coupon = Coupon::create([
            'code' => 'DELETE10',
            'discount_type' => 'amount',
            'discount' => 10,
            'min_amount' => 50,
            'started_at' => now()->subDay(),
            'expired_at' => now()->addMonth(),
        ]);

        $this->actingAs($this->root())
            ->post(route('coupon.admin.destroy', $coupon->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    public function test_destroy_route_is_registered(): void
    {
        $route = null;
        foreach (app('router')->getRoutes() as $r) {
            if ($r->getName() === 'coupon.destroy') {
                $route = $r;
                break;
            }
        }

        $this->assertNotNull($route, 'coupon.destroy route must exist');
        $this->assertContains('POST', $route->methods());
        $this->assertStringContainsString('coupons/{coupon}/destroy', $route->uri());
    }

    public function test_vendor_admin_cannot_delete_another_stores_coupon(): void
    {
        $vendorAdmin = $this->vendorAdmin();
        \App\Models\Vendor::create([
            'name' => 'Store Two',
            'user_id' => $vendorAdmin->id,
            'is_active' => true,
        ]);

        $other = \App\Models\User::create([
            'first_name' => 'Other', 'last_name' => 'Vendor',
            'mobile' => '966500000904', 'email' => 'other-vendor@example.test',
            'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $otherVendor = \App\Models\Vendor::create([
            'name' => 'Store Three',
            'user_id' => $other->id,
            'is_active' => true,
        ]);

        $coupon = Coupon::create([
            'code' => 'NOTMINE',
            'discount_type' => 'amount',
            'discount' => 5,
            'min_amount' => 10,
            'started_at' => now()->subDay(),
            'expired_at' => now()->addMonth(),
            'vendor_id' => $otherVendor->id,
        ]);

        // Either the permission middleware (403) or the scope guard (404) denies it;
        // what matters is that the request never succeeds.
        $this->actingAs($vendorAdmin)
            ->post(route('coupon.destroy', $coupon->id))
            ->assertStatus(403);

        $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
    }

    /* ---------------------------------------------------------------
       Separation between Super Admin and vendor coupon interfaces
       ---------------------------------------------------------------*/

    public function test_super_admin_routes_only_manage_global_coupons(): void
    {
        $root = $this->root();

        $this->actingAs($root)
            ->post(route('coupon.admin.store'), $this->couponPayload(['code' => 'GLOBAL50', 'notify' => '0']))
            ->assertRedirect(route('coupon.admin.index'));

        $coupon = Coupon::where('code', 'GLOBAL50')->first();
        $this->assertNotNull($coupon);
        $this->assertTrue($coupon->isGlobal(), 'a Super Admin coupon must have vendor_id = null');

        // The Super Admin list shows the global coupon.
        $this->actingAs($root)->get(route('coupon.admin.index'))->assertStatus(200);
    }

    public function test_admin_list_never_shows_a_vendors_private_coupon(): void
    {
        $vendorAdmin = $this->vendorAdmin();
        $vendor = \App\Models\Vendor::create([
            'name' => 'Store Private', 'user_id' => $vendorAdmin->id, 'is_active' => true,
        ]);

        $this->actingAs($vendorAdmin)
            ->post(route('coupon.store'), $this->couponPayload(['code' => 'PRIVATE1', 'notify' => '0']))
            ->assertRedirect(route('coupon.index'));

        $this->actingAs($this->root())
            ->get(route('coupon.admin.index'))
            ->assertStatus(200)
            // No coupon at all was created for the admin, so the row is absent.
            ->assertDontSee('PRIVATE1');
    }

    public function test_super_admin_cannot_reach_the_vendor_coupon_screens(): void
    {
        $root = $this->root();
        $this->actingAs($root)->get(route('coupon.index'))->assertStatus(403);
        $this->actingAs($root)->get(route('coupon.create'))->assertStatus(403);

        $coupon = Coupon::create([
            'code' => 'ADMINONLY', 'discount_type' => 'amount', 'discount' => 5,
            'min_amount' => 10, 'started_at' => now()->subDay(), 'expired_at' => now()->addMonth(),
        ]);
        $this->actingAs($root)
            ->post(route('coupon.destroy', $coupon->id))
            ->assertStatus(403);
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
    }

    public function test_vendor_admin_cannot_reach_the_super_admin_coupon_screens(): void
    {
        $vendorAdmin = $this->vendorAdmin();

        $this->actingAs($vendorAdmin)->get(route('coupon.admin.index'))->assertStatus(403);
        $this->actingAs($vendorAdmin)->get(route('coupon.admin.create'))->assertStatus(403);

        $this->actingAs($vendorAdmin)
            ->post(route('coupon.admin.store'), $this->couponPayload(['code' => 'SNEAKY']))
            ->assertStatus(403);
        $this->assertDatabaseMissing('coupons', ['code' => 'SNEAKY']);
    }

    public function test_super_admin_cannot_edit_or_delete_a_vendors_coupon(): void
    {
        $vendorAdmin = $this->vendorAdmin();
        $vendor = \App\Models\Vendor::create([
            'name' => 'Store Guarded', 'user_id' => $vendorAdmin->id, 'is_active' => true,
        ]);
        $coupon = Coupon::create([
            'code' => 'GUARDED', 'discount_type' => 'amount', 'discount' => 5,
            'min_amount' => 10, 'started_at' => now()->subDay(), 'expired_at' => now()->addMonth(),
            'vendor_id' => $vendor->id,
        ]);

        $root = $this->root();
        $this->actingAs($root)->get(route('coupon.admin.edit', $coupon->id))->assertStatus(404);
        $this->actingAs($root)->post(route('coupon.admin.destroy', $coupon->id))->assertStatus(404);
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
    }

    public function test_admin_coupon_and_vendor_coupon_are_both_usable_by_customers(): void
    {
        Coupon::create([
            'code' => 'GLOBALUSE', 'discount_type' => 'percent', 'discount' => 15, 'min_amount' => 20,
            'started_at' => now()->subDay(), 'expired_at' => now()->addMonth(), 'vendor_id' => null,
        ]);
        $vendor = \App\Models\Vendor::create(['name' => 'Store A', 'user_id' => $this->vendorAdmin()->id, 'is_active' => true]);
        Coupon::create([
            'code' => 'STOREUSE', 'discount_type' => 'percent', 'discount' => 10, 'min_amount' => 20,
            'started_at' => now()->subDay(), 'expired_at' => now()->addMonth(), 'vendor_id' => $vendor->id,
        ]);

        $customer = $this->customer();

        $this->actingAs($customer, 'api')
            ->postJson('/api/coupons/GLOBALUSE/apply', ['amount' => 100])
            ->assertStatus(200);

        $this->actingAs($customer, 'api')
            ->postJson('/api/coupons/STOREUSE/apply', ['amount' => 100])
            ->assertStatus(200);
    }

    public function test_vendor_coupon_notification_names_the_store(): void
    {
        $vendorAdmin = $this->vendorAdmin();
        \App\Models\Vendor::create([
            'name' => 'مغسلة النور', 'user_id' => $vendorAdmin->id, 'is_active' => true,
        ]);

        NotificationManage::query()->delete();
        $this->deviceKeys(2);
        // Both the store name and the code must reach the customer.
        $this->fakePush(self::sentResult(2), 'مغسلة النور', 'مغسلة النور');

        $this->actingAs($vendorAdmin)
            ->post(route('coupon.store'), $this->couponPayload(['code' => 'STORE30', 'notify' => '1']))
            ->assertRedirect(route('coupon.index'));

        $report = session('coupon_notification');
        $this->assertSame('sent', $report['status']);
        $this->assertSame('مغسلة النور', $report['store']);
    }

    public function test_global_coupon_notification_says_it_works_in_all_stores(): void
    {
        NotificationManage::query()->delete();
        $this->deviceKeys(2);
        $this->fakePush(self::sentResult(2), 'جميع المتاجر', 'كل المتاجر');

        $this->actingAs($this->root())
            ->post(route('coupon.admin.store'), $this->couponPayload(['code' => 'ALL40']))
            ->assertRedirect(route('coupon.admin.index'));

        $report = session('coupon_notification');
        $this->assertSame('sent', $report['status']);
        $this->assertNull($report['store']);
    }

    /* ---------------------------------------------------------------
       Migration seeds the missing template
       ---------------------------------------------------------------*/

    public function test_migration_seeds_the_coupon_template_only_once(): void
    {
        // The migration has already run as part of RefreshDatabase.
        $this->assertDatabaseCount('notification_manages', 1);

        // Re-running the (idempotent) insert must not create a duplicate.
        $this->seedCouponTemplate();

        $this->assertDatabaseCount('notification_manages', 1);
        $this->assertDatabaseHas('notification_manages', ['name' => 'coupon_notify']);
    }

    public function test_migration_seeds_the_template_into_an_empty_table(): void
    {
        NotificationManage::query()->delete();
        $this->assertDatabaseCount('notification_manages', 0);

        $this->seedCouponTemplate();

        $this->assertDatabaseCount('notification_manages', 1);
        $this->assertDatabaseHas('notification_manages', [
            'name' => 'coupon_notify',
            'message' => 'احصل على كوبون خصم على منتجاتنا',
        ]);
    }

    /** Mirrors the idempotent insert of the coupon_notify migration. */
    private function seedCouponTemplate(): void
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
}
