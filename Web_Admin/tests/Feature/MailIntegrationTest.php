<?php

namespace Tests\Feature;

use App\Models\MessageLog;
use App\Services\IntegrationSettingsService;
use App\Services\IntegrationTester;
use App\Services\MailIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class MailIntegrationTest extends TestCase
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
            'host' => 'smtp.example.test',
            'port' => '587',
            'username' => 'smtp-user',
            'password' => 'smtp-password',
            'encryption' => 'tls',
            'from_address' => 'noreply@example.test',
            'from_name' => 'Elite Clean',
            'timeout' => '15',
        ], $overrides);

        foreach ($values as $key => $value) {
            $this->settings()->set('smtp.' . $key, $value, [
                'group' => 'smtp',
                'is_secret' => $key === 'password',
            ]);
        }
    }

    private function messageExpectation(string $to, string $subject = 'Test subject')
    {
        $message = Mockery::mock();
        $message->shouldReceive('to')->once()->with($to)->andReturnSelf();
        $message->shouldReceive('subject')->once()->with($subject)->andReturnSelf();

        return $message;
    }

    public function test_disabled_smtp_sends_nothing(): void
    {
        $this->configure(['enabled' => '0']);
        Mail::shouldReceive('raw')->never();

        $result = app(MailIntegrationService::class)->send('customer@example.test', 'Hi', 'Body');

        $this->assertSame('skipped', $result['status']);
        $this->assertSame(0, MessageLog::where('channel', 'email')->count());
    }

    public function test_unconfigured_smtp_is_reported_and_logged(): void
    {
        $this->configure(['host' => '', 'from_address' => '']);
        Mail::shouldReceive('raw')->never();

        $result = app(MailIntegrationService::class)->send('customer@example.test', 'Hi', 'Body');

        $this->assertSame('not_configured', $result['status']);
        $this->assertDatabaseHas('message_logs', [
            'channel' => 'email',
            'status' => 'not_configured',
            'provider' => 'smtp',
        ]);
    }

    public function test_configuration_is_applied_and_the_email_is_sent(): void
    {
        $this->configure();
        $message = $this->messageExpectation('customer@example.test', 'Test subject');

        Mail::shouldReceive('purge')->once()->with('smtp');
        Mail::shouldReceive('raw')
            ->once()
            ->withArgs(function ($body, $callback) use ($message) {
                $callback($message);

                return $body === 'Plain body';
            });

        $result = app(MailIntegrationService::class)->send('customer@example.test', 'Test subject', 'Plain body');

        $this->assertSame('sent', $result['status']);
        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.example.test', config('mail.mailers.smtp.host'));
        $this->assertSame(587, config('mail.mailers.smtp.port'));
        $this->assertSame('tls', config('mail.mailers.smtp.encryption'));
        $this->assertSame('smtp-user', config('mail.mailers.smtp.username'));
        $this->assertSame('smtp-password', config('mail.mailers.smtp.password'));
        $this->assertSame('noreply@example.test', config('mail.from.address'));
        $this->assertSame('Elite Clean', config('mail.from.name'));

        $log = MessageLog::where('channel', 'email')->latest('id')->firstOrFail();
        $this->assertSame('sent', $log->status);
        $this->assertSame('smtp', $log->provider);
        $this->assertStringNotContainsString('customer@example.test', $log->recipient_masked);
    }

    public function test_encryption_none_is_applied_as_null(): void
    {
        $this->configure(['encryption' => 'none']);
        $message = $this->messageExpectation('customer@example.test');
        Mail::shouldReceive('purge')->once();
        Mail::shouldReceive('raw')->once()->withArgs(function ($body, $callback) use ($message) {
            $callback($message);

            return true;
        });

        app(MailIntegrationService::class)->send('customer@example.test', 'Test subject', 'Body');

        $this->assertNull(config('mail.mailers.smtp.encryption'));
    }

    public function test_smtp_failure_is_logged_without_credentials(): void
    {
        $this->configure();
        Mail::shouldReceive('purge')->once();
        Mail::shouldReceive('raw')->once()->andThrow(new \RuntimeException('Connection refused for smtp-password'));

        $result = app(MailIntegrationService::class)->send('customer@example.test', 'Hi', 'Body');

        $this->assertSame('failed', $result['status']);

        $log = MessageLog::where('channel', 'email')->latest('id')->firstOrFail();
        $this->assertSame('failed', $log->status);
        $this->assertSame('smtp_error', $log->error_code);
        $this->assertStringNotContainsString('smtp-password', (string) $log->error_message);
    }

    public function test_test_email_uses_the_configured_sender(): void
    {
        $this->configure();
        $message = Mockery::mock();
        $message->shouldReceive('to')->once()->with('admin@example.test')->andReturnSelf();
        $message->shouldReceive('subject')->once()->andReturnSelf();

        Mail::shouldReceive('purge')->once();
        Mail::shouldReceive('raw')->once()->withArgs(function ($body, $callback) use ($message) {
            $callback($message);

            return is_string($body) && $body !== '';
        });

        $result = app(MailIntegrationService::class)->test('admin@example.test');

        $this->assertSame('sent', $result['status']);
    }

    public function test_integration_tester_sends_only_when_a_recipient_is_given(): void
    {
        $this->configure();
        $message = Mockery::mock();
        $message->shouldReceive('to')->once()->with('admin@example.test')->andReturnSelf();
        $message->shouldReceive('subject')->once()->andReturnSelf();
        Mail::shouldReceive('purge')->zeroOrMoreTimes();
        Mail::shouldReceive('raw')->once()->withArgs(function ($body, $callback) use ($message) {
            $callback($message);

            return true;
        });

        $tester = app(IntegrationTester::class);

        $sent = $tester->test('smtp', ['recipient' => 'admin@example.test']);
        $this->assertSame('sent', $sent['status']);
        $this->assertSame('success', $this->settings()->get('smtp.last_test_status'));
    }
}
