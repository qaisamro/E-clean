<?php

namespace Tests\Feature;

use App\Models\MessageLog;
use App\Services\IntegrationSettingsService;
use App\Services\IntegrationTester;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function settings(): IntegrationSettingsService
    {
        return app(IntegrationSettingsService::class);
    }

    private function configure(array $overrides = []): void
    {
        $values = array_merge([
            'enabled' => '1',
            'provider' => 'generic_http',
            'api_url' => 'https://sms.example.test/send',
            'user_name' => 'sms-user',
            'password' => 'sms-password',
            'sender_id' => 'ELITECLEAN',
            'route' => '1',
            'country_code' => '970',
        ], $overrides);

        foreach ($values as $key => $value) {
            $this->settings()->set('sms.' . $key, $value, [
                'group' => 'sms',
                'is_secret' => $key === 'password',
            ]);
        }
    }

    public function test_disabled_sms_sends_nothing(): void
    {
        $this->configure(['enabled' => '0']);
        Http::fake();

        $result = app(SmsService::class)->send('+970599123456', 'Hello');

        $this->assertSame('skipped', $result['status']);
        Http::assertNothingSent();
        $this->assertSame(0, MessageLog::where('channel', 'sms')->count());
    }

    public function test_unconfigured_sms_is_reported_and_logged(): void
    {
        $this->configure([
            'api_url' => '',
            'user_name' => '',
            'password' => '',
            'sender_id' => '',
        ]);
        Http::fake();

        $result = app(SmsService::class)->send('+970599123456', 'Hello');

        $this->assertSame('not_configured', $result['status']);
        Http::assertNothingSent();
        $this->assertDatabaseHas('message_logs', [
            'channel' => 'sms',
            'status' => 'not_configured',
        ]);
    }

    public function test_generic_provider_sends_normalized_recipient_and_logs_without_secrets(): void
    {
        $this->configure();
        Http::fake([
            'https://sms.example.test/*' => Http::response('MSGID:12345', 200),
        ]);

        $result = app(SmsService::class)->send('0599123456', 'Your verification code is 123456', 'otp_registration');

        $this->assertSame('sent', $result['status']);
        $this->assertSame('MSGID:12345', $result['provider_id']);

        Http::assertSent(function ($request) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);

            return str_contains($request->url(), 'sms.example.test/send')
                && $query['RECIPIENT'] === '970599123456'
                && $query['USER_NAME'] === 'sms-user'
                && $query['PASSWORD'] === 'sms-password'
                && $query['ORIGINATOR'] === 'ELITECLEAN'
                && $query['MESSAGE_TEXT'] === 'Your verification code is 123456';
        });

        $log = MessageLog::where('channel', 'sms')->latest('id')->firstOrFail();
        $this->assertSame('sent', $log->status);
        $this->assertSame('generic_http', $log->provider);
        $this->assertSame('otp_registration', $log->template_key);
        $this->assertStringNotContainsString('970599123456', $log->recipient_masked);
        $this->assertStringEndsWith('3456', $log->recipient_masked);
        $this->assertStringNotContainsString('123456', json_encode($log->meta));
        $this->assertStringNotContainsString('sms-password', (string) $log->error_message);
    }

    public function test_provider_failure_is_logged_without_credentials(): void
    {
        $this->configure();
        Http::fake([
            'https://sms.example.test/*' => Http::response('gateway error', 500),
        ]);

        $result = app(SmsService::class)->send('+970599123456', 'Hello');

        $this->assertSame('failed', $result['status']);

        $log = MessageLog::where('channel', 'sms')->latest('id')->firstOrFail();
        $this->assertSame('failed', $log->status);
        $this->assertSame('http_500', $log->error_code);
        $this->assertStringNotContainsString('sms-password', (string) $log->error_message);
    }

    public function test_log_provider_is_a_safe_test_mode(): void
    {
        $this->configure(['provider' => 'log']);
        Http::fake();

        $result = app(SmsService::class)->send('+970599123456', 'Hello', 'test');

        $this->assertSame('sent', $result['status']);
        $this->assertStringContainsString('log driver', $result['message']);
        Http::assertNothingSent();
        $this->assertDatabaseHas('message_logs', [
            'channel' => 'sms',
            'provider' => 'log',
            'status' => 'logged',
        ]);
    }

    public function test_legacy_env_configuration_remains_a_fallback(): void
    {
        config([
            'app.sms_base_url' => 'https://legacy-sms.example.test/send',
            'app.sms_user_name' => 'legacy-user',
            'app.sms_password' => 'legacy-password',
            'app.sms_originator' => 'LEGACY',
            'app.sms_route' => '2',
        ]);
        Http::fake(['https://legacy-sms.example.test/*' => Http::response('OK', 200)]);

        $result = app(SmsService::class)->send('0599123456', 'Hello');

        $this->assertSame('sent', $result['status']);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'legacy-sms.example.test'));
    }

    public function test_phone_normalization_handles_local_international_and_double_zero_formats(): void
    {
        $this->configure();
        $sms = app(SmsService::class);

        $this->assertSame('970599123456', $sms->normalizePhone('0599123456'));
        $this->assertSame('970599123456', $sms->normalizePhone('+970599123456'));
        $this->assertSame('970599123456', $sms->normalizePhone('00970599123456'));
        $this->assertSame('970599123456', $sms->normalizePhone('970 599 123 456'));
    }

    public function test_integration_tester_requires_a_recipient_and_records_success(): void
    {
        $this->configure();
        Http::fake(['https://sms.example.test/*' => Http::response('OK', 200)]);
        $tester = app(IntegrationTester::class);

        $missing = $tester->test('sms', []);
        $this->assertSame('not_configured', $missing['status']);

        $sent = $tester->test('sms', ['recipient' => '0599123456']);
        $this->assertSame('sent', $sent['status']);
        $this->assertSame('success', $this->settings()->get('sms.last_test_status'));
    }
}
