<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VerificationCode;
use App\Services\IntegrationSettingsService;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OtpSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $email = 'otp@example.test', string $mobile = '+970599000111'): User
    {
        $user = User::create([
            'first_name' => 'Otp',
            'last_name' => 'User',
            'email' => $email,
            'mobile' => $mobile,
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('customer', 'web'));

        return $user;
    }

    private function request(string $ip = '10.0.0.1'): Request
    {
        $request = Request::create('/', 'POST');
        $request->server->set('REMOTE_ADDR', $ip);

        return $request;
    }

    private function settings(): IntegrationSettingsService
    {
        return app(IntegrationSettingsService::class);
    }

    public function test_disabled_recovery_never_requests_a_code_or_exposes_account_existence(): void
    {
        $user = $this->user();
        $this->settings()->set('otp.enabled', '0', ['group' => 'otp']);
        $known = $this->postJson('/api/forgot-password', ['contact' => $user->email])
            ->assertOk()->assertJsonPath('data.otp_required', false)
            ->assertJsonPath('data.next_step', 'contact_support');
        $unknown = $this->postJson('/api/forgot-password', ['contact' => 'missing@example.test'])->assertOk();
        $this->assertSame($known->json(), $unknown->json());
        $this->assertDatabaseCount('verification_codes', 0);
        $this->postJson('/api/reset-password', ['contact' => $user->email,
            'password' => 'changed-password', 'password_confirmation' => 'changed-password'])
            ->assertUnprocessable();
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_disabled_password_reset_purpose_does_not_request_otp(): void
    {
        $this->settings()->set('otp.enabled', '1', ['group' => 'otp']);
        $this->settings()->set('otp.enabled_password_reset', '0', ['group' => 'otp']);
        $this->postJson('/api/forgot-password', ['contact' => 'any@example.test'])
            ->assertOk()->assertJsonPath('data.otp_required', false);
    }

    public function test_otp_is_hashed_and_plaintext_is_not_persisted(): void
    {
        $user = $this->user();
        $result = app(OtpService::class)->issue($user, $user->email, 'registration', $this->request());

        $this->assertSame('sent', $result['status']);
        $this->assertNotEmpty($result['code']);

        $row = VerificationCode::where('contact', $user->email)->firstOrFail();
        $this->assertNotSame($result['code'], $row->otp);
        $this->assertTrue(Hash::check($result['code'], $row->otp));
        $this->assertNull($row->fresh()->plainOtp);
        $this->assertNotNull($row->expires_at);
        $this->assertSame('registration', $row->purpose);
    }

    public function test_otp_is_not_returned_in_forgot_password_or_resend_responses(): void
    {
        $user = $this->user();

        $forgot = $this->postJson('/api/forgot-password', ['contact' => $user->email]);
        $forgot->assertOk()->assertJsonMissing(['otp' => null]);
        $this->assertStringNotContainsString('"otp"', $forgot->getContent());

        $resend = $this->postJson('/api/resend/otp', ['contact' => $user->email, 'purpose' => 'registration']);
        $resend->assertOk();
        $this->assertStringNotContainsString('"otp"', $resend->getContent());

        $this->assertStringNotContainsString(
            (string) VerificationCode::where('contact', $user->email)->value('otp'),
            $forgot->getContent()
        );
    }

    public function test_expired_otp_cannot_be_verified(): void
    {
        $user = $this->user();
        $otp = app(OtpService::class);
        $result = $otp->issue($user, $user->email, 'registration', $this->request());

        VerificationCode::where('contact', $user->email)->update(['expires_at' => now()->subMinute()]);

        $verify = $otp->verifyAndConsume($user->email, $result['code'], 'registration', $this->request());

        $this->assertSame('invalid', $verify['status']);
    }

    public function test_otp_is_single_use(): void
    {
        $user = $this->user();
        $otp = app(OtpService::class);
        $result = $otp->issue($user, $user->email, 'registration', $this->request());

        $first = $otp->verifyAndConsume($user->email, $result['code'], 'registration', $this->request());
        $second = $otp->verifyAndConsume($user->email, $result['code'], 'registration', $this->request());

        $this->assertSame('verified', $first['status']);
        $this->assertSame('invalid', $second['status']);
        $this->assertDatabaseMissing('verification_codes', ['contact' => $user->email]);
    }

    public function test_wrong_attempts_trigger_a_temporary_lockout(): void
    {
        $user = $this->user();
        $settings = $this->settings();
        $settings->set('otp.max_attempts', '2', ['group' => 'otp']);
        $settings->set('otp.lockout_seconds', '300', ['group' => 'otp']);
        $settings->set('otp.verify_limit', '20', ['group' => 'otp']);

        $otp = app(OtpService::class);
        $result = $otp->issue($user, $user->email, 'registration', $this->request());

        $first = $otp->verify($user->email, '000000', 'registration', $this->request());
        $second = $otp->verify($user->email, '000000', 'registration', $this->request());
        $third = $otp->verify($user->email, $result['code'], 'registration', $this->request());

        $this->assertSame('invalid', $first['status']);
        $this->assertSame('invalid', $second['status']);
        $this->assertSame('rate_limited', $third['status']);
        $this->assertGreaterThan(0, $third['retry_after']);
    }

    public function test_phone_number_formatting_cannot_bypass_rate_limits(): void
    {
        $user = $this->user('format@example.test', '+970599123456');
        $settings = $this->settings();
        $settings->set('otp.send_limit', '1', ['group' => 'otp']);
        $settings->set('otp.rate_limit_window', '3600', ['group' => 'otp']);

        $otp = app(OtpService::class);
        $first = $otp->issue($user, '+970599123456', 'registration', $this->request());
        $second = $otp->issue($user, '0599123456', 'registration', $this->request('10.0.0.2'));

        $this->assertContains($first['status'], ['sent', 'delivery_failed']);
        $this->assertSame('rate_limited', $second['status']);
    }

    public function test_resend_cooldown_is_enforced(): void
    {
        $user = $this->user();
        $this->settings()->set('otp.resend_after', '60', ['group' => 'otp']);

        $otp = app(OtpService::class);
        $otp->issue($user, $user->email, 'registration', $this->request());
        $resend = $otp->issue($user, $user->email, 'registration', $this->request(), true);

        $this->assertSame('cooldown', $resend['status']);
        $this->assertGreaterThan(0, $resend['retry_after']);
    }

    public function test_in_app_otp_is_only_for_allowlisted_test_contacts(): void
    {
        $user = $this->user('allowed@example.test');
        $other = $this->user('other@example.test', '+970599000222');
        $settings = $this->settings();
        $settings->set('otp.show_in_app', '1', ['group' => 'otp']);
        $settings->set('otp.test_contacts', 'allowed@example.test', ['group' => 'otp']);

        $otp = app(OtpService::class);
        $allowed = $otp->issue($user, $user->email, 'registration', $this->request());
        $blocked = $otp->issue($other, $other->email, 'registration', $this->request('10.0.0.3'));

        $this->assertTrue($allowed['show_in_app']);
        $this->assertFalse($blocked['show_in_app']);
    }

    public function test_mobile_verified_at_is_not_set_at_registration_and_set_after_otp(): void
    {
        $this->settings()->set('customer_registration.enabled', '1', ['group' => 'customer_registration']);
        $this->settings()->set('customer_registration.approval_required', '1', ['group' => 'customer_registration']);
        \Spatie\Permission\Models\Role::findOrCreate('customer', 'web');

        $response = $this->postJson('/api/register', [
            'first_name' => 'Otp',
            'last_name' => 'User',
            'email' => 'otp-flow@example.test',
            'mobile' => '966500000031',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertStatus(200);

        $user = User::where('email', 'otp-flow@example.test')->first();
        $this->assertNull($user->mobile_verified_at);

        // Issue OTP and verify via the public endpoint.
        $result = app(OtpService::class)
            ->issue($user, $user->email, 'registration', $this->request());
        $this->assertSame('sent', $result['status']);

        $verify = $this->postJson('/api/contact/verify', [
            'contact' => $user->email,
            'otp' => $result['code'],
        ]);
        $verify->assertStatus(200);

        $this->assertNotNull($user->fresh()->mobile_verified_at);
    }

    public function test_otp_can_be_disabled_for_one_purpose_without_disabling_others(): void
    {
        $user = $this->user();
        $this->settings()->set('otp.enabled_registration', '0', ['group' => 'otp']);

        $otp = app(OtpService::class);
        $registration = $otp->issue($user, $user->email, 'registration', $this->request());
        $passwordReset = $otp->issue($user, $user->email, 'password_reset', $this->request());

        $this->assertSame('disabled', $registration['status']);
        $this->assertContains($passwordReset['status'], ['sent', 'delivery_failed']);

        $verify = $otp->verify($user->email, '000000', 'registration', $this->request());
        $this->assertSame('disabled', $verify['status']);
    }

    public function test_global_otp_switch_still_disables_every_purpose(): void
    {
        $user = $this->user();
        $this->settings()->set('otp.enabled', '0', ['group' => 'otp']);

        $otp = app(OtpService::class);

        foreach (['registration', 'login', 'password_reset', 'phone_change'] as $purpose) {
            $this->assertSame('disabled', $otp->issue($user, $user->email, $purpose, $this->request())['status']);
        }
    }

    public function test_disabled_purpose_returns_support_flow_instead_of_claiming_a_code_was_sent(): void
    {
        $user = $this->user();
        $this->settings()->set('otp.enabled_password_reset', '0', ['group' => 'otp']);

        $this->postJson('/api/forgot-password', ['contact' => $user->email])
            ->assertOk()
            ->assertJsonPath('data.otp_required', false)
            ->assertJsonPath('data.next_step', 'contact_support');
    }

    public function test_message_logs_do_not_contain_otp_or_message_body(): void
    {
        $user = $this->user();
        $result = app(OtpService::class)->issue($user, $user->email, 'registration', $this->request());

        $this->assertDatabaseMissing('message_logs', [
            'error_message' => $result['code'],
        ]);

        foreach (\App\Models\MessageLog::all() as $log) {
            $this->assertStringNotContainsString($result['code'], (string) $log->error_message);
            $this->assertStringNotContainsString($result['code'], json_encode($log->meta));
        }
    }
}
