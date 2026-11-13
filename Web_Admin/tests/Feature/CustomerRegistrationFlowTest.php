<?php

namespace Tests\Feature;

use App\Models\CustomerJoinRequest;
use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Services\IntegrationSettingsService;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The four registration states, driven entirely by Super Admin settings:
 *
 *   1. OTP off  + approval off  -> immediate full access
 *   2. OTP on   + approval off  -> OTP first, then full access
 *   3. OTP off  + approval on   -> no OTP, pending approval, no token
 *   4. OTP on   + approval on   -> OTP first, then pending approval
 */
class CustomerRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createPassportTables();
        config(['auth.guards.passport' => ['driver' => 'passport', 'provider' => 'users']]);

        Role::findOrCreate('customer', 'web');
        Role::findOrCreate('root', 'web');
        Role::findOrCreate('admin', 'web');
    }

    private function createPassportTables(): void
    {
        \Illuminate\Support\Facades\Schema::create('oauth_clients', function ($table) {
            $table->uuid('id')->primary();
            $table->nullableMorphs('owner');
            $table->string('name');
            $table->string('secret')->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect_uris');
            $table->text('grant_types');
            $table->boolean('revoked');
            $table->timestamps();
        });

        DB::table('oauth_clients')->insert([
            'id' => 'test-personal-access-client-id',
            'name' => 'Test Personal Access Client',
            'secret' => bcrypt('test-secret'),
            'provider' => 'users',
            'redirect_uris' => '[]',
            'grant_types' => '["personal_access"]',
            'revoked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\Schema::create('oauth_access_tokens', function ($table) {
            $table->string('id', 100)->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->uuid('client_id');
            $table->string('name')->nullable();
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
        });

        \Illuminate\Support\Facades\Schema::create('oauth_refresh_tokens', function ($table) {
            $table->string('id', 100)->primary();
            $table->string('access_token_id', 100)->index();
            $table->boolean('revoked');
            $table->timestamp('expires_at')->nullable();
        });
    }

    private function settings(): IntegrationSettingsService
    {
        return app(IntegrationSettingsService::class);
    }

    public function test_turning_otp_off_removes_old_phone_gate_without_verifying_phone(): void
    {
        $this->otpEnabledForRegistration();
        $this->approvalOff();
        $this->registerCustomer('switch-off@example.test', '966500000131')
            ->assertJsonPath('data.next_step', 'verify_otp');
        $user = User::where('email', 'switch-off@example.test')->firstOrFail();
        $this->otpGloballyDisabled();
        $this->postJson('/api/login', ['contact' => $user->email, 'password' => 'wrong-password'])
            ->assertStatus(400);
        $this->postJson('/api/login', ['contact' => $user->email, 'password' => 'password123'])
            ->assertOk()->assertJsonPath('data.next_step', 'ready');
        $this->actingAs($user->fresh(), 'api')->getJson('/api/customer')->assertOk();
        $this->assertNull($user->fresh()->mobile_verified_at);
        $this->assertTrue($user->fresh()->phone_verification_required);
        $this->otpEnabledForRegistration();
        $this->postJson('/api/login', ['contact' => $user->email, 'password' => 'password123'])
            ->assertForbidden()->assertJsonPath('data.next_step', 'verify_otp');
    }

    public function test_turning_otp_off_keeps_admin_approval_gate(): void
    {
        $this->otpEnabledForRegistration();
        $this->approvalOn();
        $this->registerCustomer('still-pending@example.test', '966500000132');
        $this->otpGloballyDisabled();
        $this->postJson('/api/login', ['contact' => 'still-pending@example.test', 'password' => 'password123'])
            ->assertForbidden()->assertJsonPath('data.next_step', 'await_approval');
    }

    /* ---------------------------------------------------------------
       Settings helpers (mirror the Integration Center exactly)
       ---------------------------------------------------------------*/

    private function otpEnabledForRegistration(): void
    {
        $this->settings()->set('otp.enabled', '1', ['group' => 'otp']);
        $this->settings()->set('otp.enabled_registration', '1', ['group' => 'otp']);
    }

    private function otpDisabledForRegistration(): void
    {
        // Registration OTP off while the global switch stays on: this is what
        // proves the two OTP settings are independent.
        $this->settings()->set('otp.enabled', '1', ['group' => 'otp']);
        $this->settings()->set('otp.enabled_registration', '0', ['group' => 'otp']);
    }

    private function otpGloballyDisabled(): void
    {
        $this->settings()->set('otp.enabled', '0', ['group' => 'otp']);
    }

    private function approvalOn(): void
    {
        $this->settings()->set('customer_registration.enabled', '1', ['group' => 'customer_registration']);
        $this->settings()->set('customer_registration.approval_required', '1', ['group' => 'customer_registration']);
    }

    private function approvalOff(): void
    {
        $this->settings()->set('customer_registration.enabled', '0', ['group' => 'customer_registration']);
        $this->settings()->set('customer_registration.approval_required', '0', ['group' => 'customer_registration']);
    }

    /* ---------------------------------------------------------------
       Users
       ---------------------------------------------------------------*/

    private function registerCustomer(string $email, string $mobile, array $extra = [])
    {
        return $this->postJson('/api/register', array_merge([
            'first_name' => 'Flow',
            'last_name' => 'User',
            'email' => $email,
            'mobile' => $mobile,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $extra));
    }

    private function legacyCustomer(string $email = 'legacy@example.test', string $mobile = '966500000100'): User
    {
        $user = User::create([
            'first_name' => 'Legacy',
            'last_name' => 'Customer',
            'mobile' => $mobile,
            'email' => $email,
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('customer', 'web'));
        (new CustomerRepository())->storeByUser($user);

        return $user;
    }

    private function rootUser(): User
    {
        $user = User::create([
            'first_name' => 'Root',
            'last_name' => 'Admin',
            'mobile' => '966500000101',
            'email' => 'root-flow@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('root', 'web'));

        return $user;
    }

    private function issueOtpFor(User $user): string
    {
        $request = Request::create('/', 'POST', [], [], [], ['REMOTE_ADDR' => '10.9.9.9']);

        $result = app(OtpService::class)->issue($user, (string) $user->email, 'registration', $request);

        $this->assertSame('sent', $result['status']);

        return (string) $result['code'];
    }

    /* ===============================================================
       STATE 1 — OTP disabled + approval disabled
       =============================================================== */

    public function test_state1_otp_off_approval_off_gives_immediate_access(): void
    {
        $this->otpDisabledForRegistration();
        $this->approvalOff();

        $response = $this->registerCustomer('state1@example.test', '966500000111');

        $response->assertStatus(200);
        $response->assertJsonPath('data.otp_required', false);
        $response->assertJsonPath('data.approval_required', false);
        $response->assertJsonPath('data.approval_status', 'approved');
        $response->assertJsonPath('data.next_step', 'ready');
        // Immediate access token, no OTP, no tracking token.
        $this->assertNotEmpty($response->json('data.access.token'));
        $response->assertJsonMissing(['otp' => true]);
        $response->assertJsonMissing(['tracking_token' => true]);

        $user = User::where('email', 'state1@example.test')->first();
        $this->assertFalse((bool) $user->phone_verification_required);
        $this->assertNull($user->mobile_verified_at);

        // No join request row is created.
        $this->assertDatabaseCount('customer_join_requests', 0);

        // Existing authentication rules still apply.
        $this->postJson('/api/login', ['contact' => 'state1@example.test', 'password' => 'password123'])
            ->assertStatus(200)
            ->assertJsonPath('data.next_step', 'ready');

        $this->actingAs($user, 'api')->getJson('/api/customer')->assertStatus(200);
    }

    public function test_state1_no_otp_code_is_ever_created(): void
    {
        $this->otpDisabledForRegistration();
        $this->approvalOff();

        $this->registerCustomer('state1b@example.test', '966500000112')->assertStatus(200);

        $this->assertDatabaseCount('verification_codes', 0);
    }

    /* ===============================================================
       STATE 2 — OTP enabled + approval disabled
       =============================================================== */

    public function test_state2_otp_on_approval_off_requires_otp_before_access(): void
    {
        $this->otpEnabledForRegistration();
        $this->approvalOff();

        $response = $this->registerCustomer('state2@example.test', '966500000113');

        $response->assertStatus(200);
        $response->assertJsonPath('data.otp_required', true);
        $response->assertJsonPath('data.approval_required', false);
        $response->assertJsonPath('data.approval_status', 'approved');
        $response->assertJsonPath('data.next_step', 'verify_otp');
        // No full access token before verification.
        $response->assertJsonMissing(['access' => true]);
        $response->assertJsonMissing(['tracking_token' => true]);

        $user = User::where('email', 'state2@example.test')->first();
        $this->assertTrue((bool) $user->phone_verification_required);
        $this->assertNull($user->mobile_verified_at);
        $this->assertTrue((bool) $user->is_active);

        // Login is refused until the phone is verified.
        $this->postJson('/api/login', ['contact' => 'state2@example.test', 'password' => 'password123'])
            ->assertStatus(403)
            ->assertJsonPath('data.next_step', 'verify_otp')
            ->assertJsonPath('data.otp_required', true);

        // Verify OTP through the public endpoint.
        $this->postJson('/api/contact/verify', [
            'contact' => 'state2@example.test',
            'otp' => $this->issueOtpFor($user),
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.otp_verified', true)
            ->assertJsonPath('data.next_step', 'ready')
            ->assertJsonPath('data.approval_status', 'approved');

        $this->assertNotNull($user->fresh()->mobile_verified_at);

        // Now login and the protected API work.
        $login = $this->postJson('/api/login', ['contact' => 'state2@example.test', 'password' => 'password123']);
        $login->assertStatus(200);
        $this->assertNotEmpty($login->json('data.access.token'));

        $this->actingAs($user->fresh(), 'api')->getJson('/api/customer')->assertStatus(200);
    }

    public function test_state2_existing_token_cannot_bypass_otp(): void
    {
        $this->otpEnabledForRegistration();
        $this->approvalOff();

        $user = User::create([
            'first_name' => 'Bypass',
            'last_name' => 'Attempt',
            'mobile' => '966500000114',
            'email' => 'bypass@example.test',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'phone_verification_required' => true,
        ]);
        $user->assignRole(Role::findOrCreate('customer', 'web'));
        (new CustomerRepository())->storeByUser($user);

        // A token that exists despite the missing verification, attached to the
        // authenticated user exactly as Passport's TokenGuard does in
        // production (AccessToken::fromPsrRequest).
        $token = $user->createToken('smuggled token');
        $user->withAccessToken(new \Laravel\Passport\AccessToken([
            'oauth_access_token_id' => $token->token->getKey(),
        ]));

        $response = $this->actingAs($user, 'api')->getJson('/api/customer');

        $response->assertStatus(403);
        // The middleware answers with a flat body (no "data" wrapper).
        $response->assertJsonPath('next_step', 'verify_otp');
        $response->assertJsonPath('otp_required', true);

        // The token is revoked, so it cannot be reused.
        $this->assertSame(1, (int) DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->where('revoked', 1)
            ->count());
    }

    public function test_state2_login_otp_on_mobile_completes_verification(): void
    {
        $this->otpEnabledForRegistration();
        $this->approvalOff();

        $user = User::create([
            'first_name' => 'Login',
            'last_name' => 'Otp',
            'mobile' => '966500000115',
            'email' => 'loginotp@example.test',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'phone_verification_required' => true,
        ]);
        $user->assignRole(Role::findOrCreate('customer', 'web'));
        (new CustomerRepository())->storeByUser($user);

        $request = Request::create('/', 'POST', [], [], [], ['REMOTE_ADDR' => '10.9.9.8']);
        $issued = app(OtpService::class)->issue($user, (string) $user->mobile, 'login', $request);
        // "delivery_failed" is expected in tests (no SMS provider configured);
        // the generated code is always returned to the caller.
        $this->assertContains($issued['status'], ['sent', 'delivery_failed']);

        $this->postJson('/api/login/otp/verify', [
            'contact' => '966500000115',
            'otp' => $issued['code'],
        ])->assertStatus(200);

        $this->assertNotNull($user->fresh()->mobile_verified_at);

        $this->postJson('/api/login', ['contact' => 'loginotp@example.test', 'password' => 'password123'])
            ->assertStatus(200);
    }

    /* ===============================================================
       STATE 3 — OTP disabled + approval enabled
       =============================================================== */

    public function test_state3_otp_off_approval_on_goes_straight_to_pending(): void
    {
        $this->otpDisabledForRegistration();
        $this->approvalOn();

        $response = $this->registerCustomer('state3@example.test', '966500000116');

        $response->assertStatus(200);
        $response->assertJsonPath('data.otp_required', false);
        $response->assertJsonPath('data.approval_required', true);
        $response->assertJsonPath('data.approval_status', 'pending');
        $response->assertJsonPath('data.next_step', 'await_approval');
        // Tracking token is returned exactly once, no access token, no OTP.
        $this->assertNotEmpty($response->json('data.tracking_token'));
        $response->assertJsonMissing(['access' => true]);
        $response->assertJsonMissing(['otp' => true]);

        $user = User::where('email', 'state3@example.test')->first();
        $this->assertFalse((bool) $user->phone_verification_required);
        $this->assertNull($user->mobile_verified_at);
        $this->assertFalse((bool) $user->is_active);

        // The status endpoint works with the tracking token.
        $this->postJson('/api/customer/join-request-status', [
            'tracking_token' => $response->json('data.tracking_token'),
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.approval_status', 'pending');

        // Login is blocked while pending.
        $this->postJson('/api/login', ['contact' => 'state3@example.test', 'password' => 'password123'])
            ->assertStatus(403)
            ->assertJsonPath('data.next_step', 'await_approval');

        // Approving activates the account and grants access.
        $joinRequest = CustomerJoinRequest::where('user_id', $user->id)->first();
        $this->actingAs($this->rootUser())
            ->post(route('customer-join-requests.approve', $joinRequest->id))
            ->assertRedirect();

        $this->assertTrue((bool) $user->fresh()->is_active);
        // Phone is still unverified — approval must never verify it.
        $this->assertNull($user->fresh()->mobile_verified_at);

        $this->postJson('/api/login', ['contact' => 'state3@example.test', 'password' => 'password123'])
            ->assertStatus(200)
            ->assertJsonPath('data.next_step', 'ready');
    }

    public function test_state3_phone_is_never_marked_verified_when_otp_disabled(): void
    {
        $this->otpDisabledForRegistration();
        $this->approvalOff();

        $this->registerCustomer('state3b@example.test', '966500000117')->assertStatus(200);

        $user = User::where('email', 'state3b@example.test')->first();
        $this->assertNull($user->mobile_verified_at);

        // Even a verification attempt is rejected, because OTP is disabled.
        $this->postJson('/api/contact/verify', [
            'contact' => 'state3b@example.test',
            'otp' => '123456',
        ])->assertStatus(422);
    }

    /* ===============================================================
       STATE 4 — OTP enabled + approval enabled
       =============================================================== */

    public function test_state4_otp_on_approval_on_requires_both(): void
    {
        $this->otpEnabledForRegistration();
        $this->approvalOn();

        $response = $this->registerCustomer('state4@example.test', '966500000118');

        $response->assertStatus(200);
        $response->assertJsonPath('data.otp_required', true);
        $response->assertJsonPath('data.approval_required', true);
        $response->assertJsonPath('data.approval_status', 'pending');
        $response->assertJsonPath('data.next_step', 'verify_otp');
        $this->assertNotEmpty($response->json('data.tracking_token'));
        $response->assertJsonMissing(['access' => true]);

        $user = User::where('email', 'state4@example.test')->first();
        $this->assertTrue((bool) $user->phone_verification_required);
        $this->assertNull($user->mobile_verified_at);
        $this->assertFalse((bool) $user->is_active);

        // Login is blocked: OTP comes first.
        $this->postJson('/api/login', ['contact' => 'state4@example.test', 'password' => 'password123'])
            ->assertStatus(403)
            ->assertJsonPath('data.next_step', 'verify_otp');

        // Complete OTP: the join request stays pending.
        $this->postJson('/api/contact/verify', [
            'contact' => 'state4@example.test',
            'otp' => $this->issueOtpFor($user),
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.next_step', 'await_approval')
            ->assertJsonPath('data.approval_status', 'pending')
            ->assertJsonMissing(['access' => true]);

        $this->assertNotNull($user->fresh()->mobile_verified_at);

        // Still blocked until the admin approves.
        $this->postJson('/api/login', ['contact' => 'state4@example.test', 'password' => 'password123'])
            ->assertStatus(403)
            ->assertJsonPath('data.next_step', 'await_approval');

        $joinRequest = CustomerJoinRequest::where('user_id', $user->id)->first();
        $this->actingAs($this->rootUser())
            ->post(route('customer-join-requests.approve', $joinRequest->id))
            ->assertRedirect();

        // Full access only after OTP *and* approval.
        $login = $this->postJson('/api/login', ['contact' => 'state4@example.test', 'password' => 'password123']);
        $login->assertStatus(200);
        $this->assertNotEmpty($login->json('data.access.token'));

        $this->actingAs($user->fresh(), 'api')->getJson('/api/customer')->assertStatus(200);
    }

    /* ===============================================================
       Backward compatibility
       =============================================================== */

    public function test_existing_customers_keep_full_access_when_otp_enabled_later(): void
    {
        $this->otpEnabledForRegistration();
        $this->approvalOn();

        // Account created before the OTP policy: marker stays false.
        $legacy = $this->legacyCustomer();
        $this->assertFalse((bool) $legacy->phone_verification_required);

        $this->postJson('/api/login', ['contact' => 'legacy@example.test', 'password' => 'password'])
            ->assertStatus(200)
            ->assertJsonPath('data.approval_status', 'approved');

        $this->actingAs($legacy, 'api')->getJson('/api/customer')->assertStatus(200);
    }

    public function test_staff_accounts_are_not_gated(): void
    {
        $this->otpEnabledForRegistration();

        $root = $this->rootUser();
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Staff',
            'mobile' => '966500000102',
            'email' => 'admin-flow@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        // Staff accounts never carry the customer verification gate.
        $this->assertFalse((bool) $root->phone_verification_required);
        $this->assertFalse((bool) $admin->phone_verification_required);
        $this->assertFalse($root->hasRole('customer'));
        $this->assertFalse($admin->hasRole('customer'));

        // Staff-created customers are not gated either.
        $created = User::create([
            'first_name' => 'Panel',
            'last_name' => 'Customer',
            'mobile' => '966500000103',
            'email' => 'panel@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $created->assignRole(Role::findOrCreate('customer', 'web'));
        (new CustomerRepository())->storeByUser($created);

        $this->assertFalse((bool) $created->phone_verification_required);
        $this->actingAs($created, 'api')->getJson('/api/customer')->assertStatus(200);
    }

    /* ===============================================================
       Tracking-token recovery (independent of the registration OTP)
       =============================================================== */

    public function test_recovery_works_even_when_registration_otp_is_disabled(): void
    {
        // Registration OTP OFF, global OTP ON, approval ON.
        $this->otpDisabledForRegistration();
        $this->approvalOn();

        $response = $this->registerCustomer('recovery@example.test', '966500000119');
        $token = $response->json('data.tracking_token');
        $this->assertNotEmpty($token);

        $user = User::where('email', 'recovery@example.test')->first();

        // Step 1: generic response, OTP still sent (generic purpose).
        $this->postJson('/api/customer/join-request-recovery', ['contact' => 'recovery@example.test'])
            ->assertStatus(200);

        $request = Request::create('/', 'POST', [], [], [], ['REMOTE_ADDR' => '10.9.9.7']);
        $issued = app(OtpService::class)->issue($user, 'recovery@example.test', 'generic', $request);
        $this->assertSame('sent', $issued['status']);

        // Step 2: a new token is issued and the old one stops working.
        $verify = $this->postJson('/api/customer/join-request-recovery/verify', [
            'contact' => 'recovery@example.test',
            'otp' => $issued['code'],
        ]);
        $verify->assertStatus(200);
        $newToken = $verify->json('data.tracking_token');
        $this->assertNotEmpty($newToken);
        $this->assertNotSame($token, $newToken);

        $this->postJson('/api/customer/join-request-status', ['tracking_token' => $token])
            ->assertStatus(404);
        $this->postJson('/api/customer/join-request-status', ['tracking_token' => $newToken])
            ->assertStatus(200)
            ->assertJsonPath('data.approval_status', 'pending');
    }

    public function test_recovery_is_unavailable_when_otp_is_globally_disabled(): void
    {
        $this->otpGloballyDisabled();
        $this->approvalOn();

        $this->postJson('/api/customer/join-request-recovery', ['contact' => 'someone@example.test'])
            ->assertStatus(422);
    }

    public function test_recovery_does_not_reveal_whether_the_account_exists(): void
    {
        $this->otpDisabledForRegistration();

        $known = $this->postJson('/api/customer/join-request-recovery', ['contact' => 'known@example.test'])
            ->assertStatus(200)
            ->json('message');
        $unknown = $this->postJson('/api/customer/join-request-recovery', ['contact' => 'unknown@example.test'])
            ->assertStatus(200)
            ->json('message');

        $this->assertSame($known, $unknown);
    }

    public function test_authenticated_status_reports_approved_for_existing_customer(): void
    {
        $this->approvalOn();
        $legacy = $this->legacyCustomer();

        $this->actingAs($legacy, 'api')
            ->getJson('/api/customer/join-request-status')
            ->assertStatus(200)
            ->assertJsonPath('data.approval_status', 'approved')
            ->assertJsonPath('data.next_step', 'ready');
    }

    public function test_authenticated_status_is_blocked_for_pending_account(): void
    {
        $this->otpDisabledForRegistration();
        $this->approvalOn();

        $user = User::create([
            'first_name' => 'Approved',
            'last_name' => 'Customer',
            'mobile' => '966500000120',
            'email' => 'approved-pending@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('customer', 'web'));
        (new CustomerRepository())->storeByUser($user);
        CustomerJoinRequest::create(['user_id' => $user->id, 'status' => CustomerJoinRequest::STATUS_PENDING]);

        // The middleware blocks a pending account before the controller runs.
        $this->actingAs($user, 'api')
            ->getJson('/api/customer/join-request-status')
            ->assertStatus(403)
            ->assertJsonPath('approval_status', 'pending');
    }

    /* ===============================================================
       FAIL-CLOSED behaviour when the approval store is unavailable
       (approval feature ON but the table is missing)
       =============================================================== */

    /**
     * Drop the table to simulate "approval enabled, migration not run".
     */
    private function dropJoinRequestTable(): void
    {
        \Illuminate\Support\Facades\Schema::drop('customer_join_requests');
    }

    public function test_approval_enabled_but_table_missing_blocks_protected_access(): void
    {
        $this->otpDisabledForRegistration();
        $this->approvalOn();

        $user = User::create([
            'first_name' => 'Stranded',
            'last_name' => 'Customer',
            'mobile' => '966500000121',
            'email' => 'stranded@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('customer', 'web'));
        (new CustomerRepository())->storeByUser($user);
        $this->dropJoinRequestTable();

        // FAIL CLOSED: no access for anyone while approval is unverifiable.
        $response = $this->actingAs($user, 'api')->getJson('/api/customer');
        $response->assertStatus(503);
        $response->assertJsonPath('next_step', 'retry_later');

        // ... and login is refused too.
        $this->postJson('/api/login', ['contact' => 'stranded@example.test', 'password' => 'password'])
            ->assertStatus(503);
    }

    public function test_approval_enabled_but_table_missing_does_not_create_unapproved_account(): void
    {
        $this->otpDisabledForRegistration();
        $this->approvalOn();
        $this->dropJoinRequestTable();

        // FAIL CLOSED: an approval-protected registration must never silently
        // fall back to normal registration.
        $response = $this->registerCustomer('norollback@example.test', '966500000122');

        $response->assertStatus(503);
        $response->assertJsonMissing(['access' => true]);
        $response->assertJsonMissing(['tracking_token' => true]);

        // The transaction rolled back: no account, no customer row, no token.
        $this->assertDatabaseMissing('users', ['email' => 'norollback@example.test']);
        $this->assertDatabaseCount('customers', 0);
        $this->assertDatabaseCount('oauth_access_tokens', 0);

        // The contact is free again, proving nothing persisted.
        $this->postJson('/api/register', [
            'first_name' => 'Retry',
            'last_name' => 'User',
            'email' => 'norollback@example.test',
            'mobile' => '966500000122',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(503);
    }

    public function test_approval_enabled_but_table_missing_never_issues_token_after_otp(): void
    {
        $this->otpEnabledForRegistration();
        $this->approvalOn();

        // Create the account while the table still exists, then remove the store.
        $this->registerCustomer('loststore@example.test', '966500000123');
        $user = User::where('email', 'loststore@example.test')->first();
        $this->dropJoinRequestTable();

        // OTP verification itself still succeeds (it does not need the table)...
        $request = Request::create('/', 'POST', [], [], [], ['REMOTE_ADDR' => '10.9.9.6']);
        $issued = app(OtpService::class)->issue($user, 'loststore@example.test', 'registration', $request);

        $response = $this->postJson('/api/contact/verify', [
            'contact' => 'loststore@example.test',
            'otp' => $issued['code'],
        ]);

        // ...but no token is handed out, because approval cannot be verified.
        $response->assertStatus(503);
        $response->assertJsonMissing(['access' => true]);
        $this->assertNotNull($user->fresh()->mobile_verified_at);
    }

    public function test_status_endpoint_returns_404_when_store_is_unavailable(): void
    {
        $this->approvalOn();
        $this->dropJoinRequestTable();

        // Never confirms a status that cannot be verified, and leaks nothing.
        $this->postJson('/api/customer/join-request-status', ['tracking_token' => str_repeat('a', 64)])
            ->assertStatus(404);
    }

    public function test_approval_off_preserves_fail_open_for_existing_customers(): void
    {
        // Feature OFF: the table is not needed, existing customers keep working.
        $this->otpDisabledForRegistration();
        $this->approvalOff();

        $legacy = $this->legacyCustomer('legacy-open@example.test', '966500000124');
        $this->dropJoinRequestTable();

        $this->actingAs($legacy, 'api')->getJson('/api/customer')->assertStatus(200);

        $this->postJson('/api/login', ['contact' => 'legacy-open@example.test', 'password' => 'password'])
            ->assertStatus(200);
    }

    public function test_registration_works_normally_when_approval_off_and_table_missing(): void
    {
        $this->otpDisabledForRegistration();
        $this->approvalOff();
        $this->dropJoinRequestTable();

        // No approval required => no store needed => normal registration.
        $this->registerCustomer('normal-nostore@example.test', '966500000125')
            ->assertStatus(200)
            ->assertJsonPath('data.next_step', 'ready');
    }
}
