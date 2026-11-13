<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\IntegrationSetting;
use App\Models\User;
use App\Services\IntegrationSettingsService;
use App\Services\NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class IntegrationCenterSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role, string $key): User
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

    private function settings(): IntegrationSettingsService
    {
        return app(IntegrationSettingsService::class);
    }

    public function test_guests_are_redirected_and_non_admin_roles_are_forbidden(): void
    {
        $this->get('/integrations')->assertRedirect('/login');

        foreach (['customer', 'vendor_admin', 'driver', 'visitor'] as $role) {
            $user = $this->userWithRole($role, $role);
            $this->actingAs($user)->get('/integrations')->assertForbidden();
            $this->actingAs($user)->get('/integrations/audit-logs')->assertForbidden();
            $this->actingAs($user)->get('/integrations/api-keys/list')->assertForbidden();
        }
    }

    public function test_root_and_admin_can_open_the_integration_center(): void
    {
        foreach (['root', 'admin'] as $role) {
            $user = $this->userWithRole($role, 'admin-' . $role);

            $response = $this->actingAs($user)->get('/integrations');
            $response->assertOk();
            $response->assertSee('channels_registration[]', false);
            $response->assertSee('إعداد خارجي مطلوب');
            $this->actingAs($user)->get('/integrations/templates/list')->assertOk();
            $this->actingAs($user)->get('/integrations/message-logs')->assertOk();
            $this->actingAs($user)->get('/integrations/webhooks/list')->assertOk();
            $this->actingAs($user)->get('/integrations/api-keys/list')->assertOk();
            $this->actingAs($user)->get('/integrations/audit-logs')->assertOk();
        }
    }

    public function test_secret_values_are_masked_and_never_rendered(): void
    {
        $root = $this->userWithRole('root', 'mask-root');
        $this->settings()->set('smtp.password', 'super-secret-value', [
            'group' => 'smtp',
            'is_secret' => true,
        ]);

        $response = $this->actingAs($root)->get('/integrations');
        $response->assertOk();
        $response->assertDontSee('super-secret-value');
        $response->assertSee('********');

        $group = $this->settings()->groupWithMeta('smtp');
        $this->assertTrue($group['secrets']['password']);
        $this->assertSame('', $group['values']['password']);

        $masked = $this->settings()->group('smtp');
        $this->assertSame('********', $masked['password']);
    }

    public function test_changing_a_secret_requires_the_current_password_and_is_encrypted(): void
    {
        $root = $this->userWithRole('root', 'reauth-root');
        $url = route('integrations.update', 'smtp');

        $this->actingAs($root)
            ->post($url, ['password' => 'super-secret-value'])
            ->assertStatus(423);

        $this->assertDatabaseMissing('integration_settings', ['key' => 'smtp.password']);

        $this->actingAs($root)
            ->post($url, [
                'password' => 'super-secret-value',
                'current_password' => 'password',
            ])
            ->assertRedirect();

        $row = IntegrationSetting::where('key', 'smtp.password')->firstOrFail();
        $this->assertTrue($row->is_secret);
        $this->assertNotSame('super-secret-value', $row->value);
        $this->assertSame('super-secret-value', decrypt($row->value));
    }

    public function test_non_secret_updates_are_saved_and_audited(): void
    {
        $root = $this->userWithRole('root', 'audit-root');

        $this->actingAs($root)
            ->post(route('integrations.update', 'smtp'), [
                'host' => 'smtp.example.test',
                'port' => '587',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('integration_settings', [
            'key' => 'smtp.host',
            'value' => 'smtp.example.test',
            'is_secret' => false,
        ]);

        $log = AuditLog::where('setting_key', 'smtp.host')->latest('id')->firstOrFail();
        $this->assertSame('integration.setting.updated', $log->action);
        $this->assertSame('smtp', $log->group_name);
        $this->assertSame('smtp.example.test', $log->new_value);
        $this->assertSame($root->id, $log->user_id);
    }

    public function test_secret_changes_are_audited_without_storing_the_value(): void
    {
        $root = $this->userWithRole('root', 'secret-audit-root');

        $this->actingAs($root)
            ->post(route('integrations.update', 'whatsapp'), [
                'access_token' => 'EAAG-super-secret-token',
                'current_password' => 'password',
            ])
            ->assertRedirect();

        $log = AuditLog::where('setting_key', 'whatsapp.access_token')->latest('id')->firstOrFail();
        $this->assertContains($log->old_value, ['[secret]', '[redacted]']);
        $this->assertContains($log->new_value, ['[secret changed]', '[redacted]']);

        $serialized = $log->old_value . $log->new_value . json_encode($log->meta);
        $this->assertStringNotContainsString('EAAG-super-secret-token', $serialized);
    }

    public function test_audit_log_page_requires_admin_and_shows_no_secret_values(): void
    {
        $this->settings()->set('sms.password', 'sms-secret-value', [
            'group' => 'sms',
            'is_secret' => true,
        ]);

        $customer = $this->userWithRole('customer', 'audit-customer');
        $this->actingAs($customer)->get('/integrations/audit-logs')->assertForbidden();

        $root = $this->userWithRole('root', 'audit-viewer');
        $response = $this->actingAs($root)->get('/integrations/audit-logs');
        $response->assertOk();
        $response->assertDontSee('sms-secret-value');
    }

    public function test_otp_channels_are_saved_as_a_multi_select(): void
    {
        $root = $this->userWithRole('root', 'channels-root');

        $this->actingAs($root)
            ->post(route('integrations.update', 'otp'), [
                'channels_registration' => ['email', 'sms'],
                'channels_login' => ['whatsapp'],
                'channels_password_reset' => [],
                'channels_phone_change' => ['sms', 'whatsapp'],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('integration_settings', [
            'key' => 'otp.channels_registration',
            'value' => 'email,sms',
        ]);
        $this->assertDatabaseHas('integration_settings', [
            'key' => 'otp.channels_password_reset',
            'value' => '',
        ]);

        $dispatcher = app(NotificationDispatcher::class);
        $this->assertSame(['email', 'sms'], $dispatcher->channelsFor('registration', 'user@example.test'));
        $this->assertSame(['sms', 'whatsapp'], $dispatcher->channelsFor('phone_change', '+970599000111'));
    }

    public function test_otp_purpose_switches_are_saved_from_the_center(): void
    {
        $root = $this->userWithRole('root', 'purposes-root');

        $this->actingAs($root)
            ->post(route('integrations.update', 'otp'), [
                'enabled_registration' => '0',
                'enabled_login' => '1',
                'enabled_password_reset' => '0',
                'enabled_phone_change' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('integration_settings', [
            'key' => 'otp.enabled_registration',
            'value' => '0',
        ]);
        $this->assertDatabaseHas('integration_settings', [
            'key' => 'otp.enabled_login',
            'value' => '1',
        ]);
    }

    public function test_unknown_setting_keys_are_ignored(): void
    {
        $root = $this->userWithRole('root', 'unknown-root');

        $this->actingAs($root)
            ->post(route('integrations.update', 'smtp'), [
                'host' => 'smtp.example.test',
                'arbitrary_key' => 'must-not-be-written',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('integration_settings', [
            'key' => 'smtp.arbitrary_key',
        ]);
    }

    public function test_unknown_group_returns_not_found(): void
    {
        $root = $this->userWithRole('root', 'group-root');

        $this->actingAs($root)
            ->post('/integrations/does-not-exist', ['enabled' => '1'])
            ->assertNotFound();

        $this->actingAs($root)
            ->post('/integrations/does-not-exist/test')
            ->assertNotFound();
    }

    public function test_connected_status_requires_a_recent_successful_test(): void
    {
        $settings = $this->settings();

        $settings->set('smtp.enabled', '1', ['group' => 'smtp']);
        $settings->set('smtp.host', 'smtp.example.test', ['group' => 'smtp']);
        $settings->set('smtp.from_address', 'noreply@example.test', ['group' => 'smtp']);

        $this->assertSame('configured', $settings->status('smtp')['status']);

        $settings->recordTest('smtp', true, 'connected successfully');
        $this->assertSame('connected', $settings->status('smtp')['status']);

        $settings->recordTest('smtp', false, 'connection refused');
        $this->assertSame('error', $settings->status('smtp')['status']);

        // Internal capabilities never claim to be externally "connected".
        $settings->set('otp.enabled', '1', ['group' => 'otp']);
        $settings->recordTest('otp', true, 'settings valid');
        $otpStatus = $settings->status('otp');
        $this->assertSame('configured', $otpStatus['status']);
        $this->assertSame('مهيأ ومختبر', $otpStatus['label']);
    }
}
