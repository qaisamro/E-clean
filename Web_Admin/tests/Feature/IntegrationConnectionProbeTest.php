<?php

namespace Tests\Feature;

use App\Services\IntegrationSettingsService;
use App\Services\IntegrationTester;
use App\Services\Smtp\SmtpConnector;
use App\Services\Smtp\SmtpProbe;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\Support\FakeSmtpConnection;
use Tests\Support\FakeSmtpConnector;
use Tests\TestCase;

class IntegrationConnectionProbeTest extends TestCase
{
    use RefreshDatabase;

    private function settings(): IntegrationSettingsService
    {
        return app(IntegrationSettingsService::class);
    }

    private function configureSmtp(): void
    {
        foreach ([
            'enabled' => '1',
            'host' => 'smtp.example.test',
            'port' => '587',
            'username' => 'smtp-user',
            'password' => 'smtp-password',
            'encryption' => 'tls',
            'from_address' => 'noreply@example.test',
            'timeout' => '10',
        ] as $key => $value) {
            $this->settings()->set('smtp.' . $key, $value, [
                'group' => 'smtp',
                'is_secret' => $key === 'password',
            ]);
        }
    }

    private function successScript(): array
    {
        return [
            '220 smtp.example.test ready',
            '250-smtp.example.test',
            '250 AUTH LOGIN',
            '220 go ahead',
            '250-smtp.example.test',
            '250 AUTH LOGIN',
            '334 VXNlcm5hbWU6',
            '334 UGFzc3dvcmQ6',
            '235 authenticated',
        ];
    }

    public function test_smtp_probe_authenticates_without_sending_a_message(): void
    {
        $connection = new FakeSmtpConnection($this->successScript());
        $this->app->instance(SmtpConnector::class, new FakeSmtpConnector($connection));

        $result = app(SmtpProbe::class)->test([
            'host' => 'smtp.example.test',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'smtp-user',
            'password' => 'smtp-password',
            'timeout' => 10,
        ]);

        $this->assertSame('ok', $result['status']);
        $this->assertTrue($connection->closed);
        $this->assertContains('STARTTLS', $connection->written);
        $this->assertContains('AUTH LOGIN', $connection->written);

        foreach ($connection->written as $line) {
            $this->assertStringStartsNotWith('MAIL FROM', strtoupper($line));
            $this->assertStringStartsNotWith('RCPT TO', strtoupper($line));
            $this->assertStringStartsNotWith('DATA', strtoupper($line));
        }
    }

    public function test_smtp_probe_reports_authentication_failure_without_exposing_the_password(): void
    {
        $script = $this->successScript();
        $script[8] = '535 5.7.8 authentication failed';

        $connection = new FakeSmtpConnection($script);
        $this->app->instance(SmtpConnector::class, new FakeSmtpConnector($connection));

        $result = app(SmtpProbe::class)->test([
            'host' => 'smtp.example.test',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'smtp-user',
            'password' => 'super-secret-smtp-password',
            'timeout' => 10,
        ]);

        $this->assertSame('failed', $result['status']);
        $this->assertStringNotContainsString('super-secret-smtp-password', $result['message']);
    }

    public function test_smtp_probe_reports_connection_failure(): void
    {
        $this->app->instance(SmtpConnector::class, new FakeSmtpConnector(null));

        $result = app(SmtpProbe::class)->test([
            'host' => 'smtp.example.test',
            'port' => 587,
            'encryption' => 'tls',
            'username' => '',
            'password' => '',
            'timeout' => 5,
        ]);

        $this->assertSame('failed', $result['status']);
    }

    public function test_smtp_tester_uses_the_probe_when_no_recipient_is_given(): void
    {
        $this->configureSmtp();
        $connection = new FakeSmtpConnection($this->successScript());
        $this->app->instance(SmtpConnector::class, new FakeSmtpConnector($connection));

        Mail::shouldReceive('raw')->never();

        $result = app(IntegrationTester::class)->test('smtp', []);

        $this->assertSame('ok', $result['status']);
        $this->assertSame('success', $this->settings()->get('smtp.last_test_status'));
    }

    public function test_whatsapp_probe_reads_the_business_profile_without_sending(): void
    {
        foreach ([
            'enabled' => '1',
            'access_token' => 'EAAG-test-token',
            'phone_number_id' => '1234567890',
        ] as $key => $value) {
            $this->settings()->set('whatsapp.' . $key, $value, [
                'group' => 'whatsapp',
                'is_secret' => $key === 'access_token',
            ]);
        }

        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'verified_name' => 'Elite Clean',
                'display_phone_number' => '+970 59 000 0000',
                'quality_rating' => 'GREEN',
            ], 200),
        ]);

        $result = app(WhatsAppService::class)->testConnection();

        $this->assertSame('ok', $result['status']);
        $this->assertSame('Elite Clean', $result['verified_name']);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && str_contains($request->url(), '/1234567890')
            && $request->hasHeader('Authorization', 'Bearer EAAG-test-token'));
    }

    public function test_whatsapp_tester_uses_the_probe_without_a_recipient(): void
    {
        foreach ([
            'enabled' => '1',
            'access_token' => 'EAAG-test-token',
            'phone_number_id' => '1234567890',
        ] as $key => $value) {
            $this->settings()->set('whatsapp.' . $key, $value, [
                'group' => 'whatsapp',
                'is_secret' => $key === 'access_token',
            ]);
        }

        Http::fake(function ($request) {
            if ($request->method() === 'GET') {
                return Http::response(['verified_name' => 'Elite Clean'], 200);
            }

            return Http::response(['messages' => [['id' => 'wamid.test-1']]], 200);
        });

        $tester = app(IntegrationTester::class);

        $probe = $tester->test('whatsapp', []);
        $this->assertSame('ok', $probe['status']);

        $sent = $tester->test('whatsapp', ['recipient' => '+970599123456']);
        $this->assertSame('sent', $sent['status']);
    }
}
