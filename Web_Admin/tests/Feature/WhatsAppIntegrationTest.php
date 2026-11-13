<?php

namespace Tests\Feature;

use App\Models\IntegrationWebhookEvent;
use App\Models\MessageLog;
use App\Services\IntegrationSettingsService;
use App\Services\MessageLogger;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function configure(array $overrides = []): void
    {
        $settings = app(IntegrationSettingsService::class);
        $values = array_merge([
            'enabled' => '1',
            'access_token' => 'EAAG_test_access_token',
            'phone_number_id' => '1234567890',
            'app_secret' => 'app-secret-test',
            'webhook_verify_token' => 'verify-token-test',
            'webhook_enabled' => '1',
        ], $overrides);

        foreach ($values as $key => $value) {
            if ($value === null) {
                continue;
            }

            $settings->set('whatsapp.' . $key, $value, [
                'group' => 'whatsapp',
                'is_secret' => in_array($key, ['access_token', 'app_secret', 'webhook_verify_token'], true),
            ]);
        }
    }

    private function signedPost(string $raw, string $secret): \Illuminate\Testing\TestResponse
    {
        return $this->call('POST', '/api/webhooks/whatsapp', [], [], [], [
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256=' . hash_hmac('sha256', $raw, $secret),
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $raw);
    }

    public function test_webhook_verification_challenge_requires_the_configured_token(): void
    {
        $this->configure();

        $this->get('/api/webhooks/whatsapp?hub.mode=subscribe&hub.verify_token=verify-token-test&hub.challenge=challenge-123')
            ->assertOk()
            ->assertSee('challenge-123');

        $this->get('/api/webhooks/whatsapp?hub.mode=subscribe&hub.verify_token=wrong-token&hub.challenge=challenge-123')
            ->assertForbidden();

        $this->get('/api/webhooks/whatsapp?hub.mode=unsubscribe&hub.verify_token=verify-token-test&hub.challenge=challenge-123')
            ->assertForbidden();
    }

    public function test_webhook_rejects_invalid_signatures_without_storing_events(): void
    {
        $this->configure();
        $raw = json_encode(['entry' => []]);

        $this->call('POST', '/api/webhooks/whatsapp', [], [], [], [
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256=' . str_repeat('0', 64),
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $raw)->assertForbidden();

        $this->assertSame(0, IntegrationWebhookEvent::count());
    }

    public function test_valid_webhook_updates_message_status_and_stores_the_event(): void
    {
        $this->configure();

        $logger = app(MessageLogger::class);
        $logger->record('whatsapp', '+970599123456', 'sent', 'whatsapp_cloud', null, 'wamid.test-1');

        $raw = json_encode([
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'statuses' => [[
                            'id' => 'wamid.test-1',
                            'status' => 'read',
                        ]],
                    ],
                ]],
            ]],
        ]);

        $this->signedPost($raw, 'app-secret-test')->assertOk();

        $event = IntegrationWebhookEvent::firstOrFail();
        $this->assertSame('whatsapp', $event->provider);
        $this->assertSame('message_status', $event->event_type);
        $this->assertSame('wamid.test-1', $event->external_id);
        $this->assertTrue($event->signature_valid);

        $this->assertSame('read', MessageLog::where('provider_message_id', 'wamid.test-1')->value('status'));
    }

    public function test_webhook_is_unavailable_when_disabled_or_unconfigured(): void
    {
        $this->configure(['webhook_enabled' => '0']);
        $raw = json_encode(['entry' => []]);
        $this->signedPost($raw, 'app-secret-test')->assertStatus(503);

        $this->configure(['webhook_enabled' => '1', 'app_secret' => null]);
        app(IntegrationSettingsService::class)->set('whatsapp.app_secret', '', [
            'group' => 'whatsapp',
            'is_secret' => true,
        ]);
        $this->signedPost($raw, 'app-secret-test')->assertStatus(503);
    }

    public function test_send_text_uses_the_cloud_api_and_logs_the_provider_id(): void
    {
        $this->configure();
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messages' => [['id' => 'wamid.out-1']],
            ], 200),
        ]);

        $result = app(WhatsAppService::class)->sendText('+970599123456', 'Hello from Elite Clean');

        $this->assertSame('sent', $result['status']);
        $this->assertSame('wamid.out-1', $result['provider_id']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v20.0/1234567890/messages')
                && $request->hasHeader('Authorization', 'Bearer EAAG_test_access_token')
                && $request['messaging_product'] === 'whatsapp'
                && $request['to'] === '970599123456'
                && $request['type'] === 'text'
                && $request['text']['body'] === 'Hello from Elite Clean';
        });

        $log = MessageLog::where('channel', 'whatsapp')->latest('id')->firstOrFail();
        $this->assertSame('sent', $log->status);
        $this->assertSame('wamid.out-1', $log->provider_message_id);
        $this->assertStringNotContainsString('970599123456', $log->recipient_masked);
    }

    public function test_provider_errors_are_logged_without_the_access_token(): void
    {
        $this->configure();
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'error' => [
                    'message' => 'Invalid credential EAAG_test_access_token',
                    'code' => 190,
                ],
            ], 400),
        ]);

        $result = app(WhatsAppService::class)->sendText('+970599123456', 'Hello');

        $this->assertSame('failed', $result['status']);

        $log = MessageLog::where('channel', 'whatsapp')->latest('id')->firstOrFail();
        $this->assertSame('failed', $log->status);
        $this->assertSame('190', $log->error_code);
        $this->assertStringNotContainsString('EAAG_test_access_token', (string) $log->error_message);
        $this->assertStringContainsString('[redacted]', (string) $log->error_message);
    }

    public function test_send_states_when_disabled_or_not_configured(): void
    {
        $this->configure(['enabled' => '0']);
        $this->assertSame('skipped', app(WhatsAppService::class)->sendText('+970599', 'x')['status']);

        $this->configure(['enabled' => '1', 'access_token' => '']);
        $this->assertSame('not_configured', app(WhatsAppService::class)->sendText('+970599', 'x')['status']);

        Http::assertNothingSent();
    }

    public function test_webhook_url_is_reported_with_the_api_prefix(): void
    {
        $this->configure();
        $status = app(WhatsAppService::class)->status();

        $this->assertTrue($status['webhook_configured']);
        $this->assertStringEndsWith('/api/webhooks/whatsapp', $status['webhook_url']);
    }
}
