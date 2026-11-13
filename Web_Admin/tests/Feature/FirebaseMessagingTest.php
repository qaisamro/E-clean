<?php

namespace Tests\Feature;

use App\Models\MessageLog;
use App\Services\Firebase\ServiceAccountTokenProvider;
use App\Services\FirebaseMessagingService;
use App\Services\IntegrationSettingsService;
use App\Services\IntegrationTester;
use App\Support\FirebaseCredentials;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Mockery;
use Tests\TestCase;

class FirebaseMessagingTest extends TestCase
{
    use RefreshDatabase;

    private function settings(): IntegrationSettingsService
    {
        return app(IntegrationSettingsService::class);
    }

    private function credentialsJson(): string
    {
        return json_encode([
            'project_id' => 'elite-clean-test',
            'client_email' => 'firebase@elite-clean-test.iam.gserviceaccount.com',
            'private_key' => "-----BEGIN PRIVATE KEY-----\ntest-private-key\n-----END PRIVATE KEY-----\n",
        ]);
    }

    private function enableLegacy(string $key = 'legacy-server-key-test'): void
    {
        $this->settings()->set('firebase.enabled', '1', ['group' => 'firebase']);
        $this->settings()->set('firebase.legacy_server_key', $key, [
            'group' => 'firebase',
            'is_secret' => true,
        ]);
    }

    private function enableServiceAccount(): void
    {
        $this->settings()->set('firebase.enabled', '1', ['group' => 'firebase']);
        $this->settings()->set('firebase.project_id', 'elite-clean-test', ['group' => 'firebase']);
        $this->settings()->set('firebase.credentials_json', $this->credentialsJson(), [
            'group' => 'firebase',
            'is_secret' => true,
        ]);
    }

    private function injectMessaging(FirebaseMessagingService $service, Messaging $messaging): void
    {
        $property = new \ReflectionProperty(FirebaseMessagingService::class, 'messaging');
        $property->setAccessible(true);
        $property->setValue($service, $messaging);
    }

    public function test_disabled_firebase_sends_nothing(): void
    {
        $this->settings()->set('firebase.enabled', '0', ['group' => 'firebase']);
        Http::fake();

        $result = app(FirebaseMessagingService::class)->send('device-token-1', 'Title', 'Body');

        $this->assertSame('skipped', $result['status']);
        Http::assertNothingSent();
        $this->assertSame(0, MessageLog::where('channel', 'push')->count());
    }

    public function test_missing_credentials_are_reported_and_logged(): void
    {
        $this->settings()->set('firebase.enabled', '1', ['group' => 'firebase']);
        Http::fake();

        $service = app(FirebaseMessagingService::class);
        $this->assertFalse($service->isConfigured());

        $result = $service->send('device-token-1', 'Title', 'Body');

        $this->assertSame('not_configured', $result['status']);
        Http::assertNothingSent();
        $this->assertDatabaseHas('message_logs', [
            'channel' => 'push',
            'status' => 'not_configured',
            'provider' => 'fcm',
        ]);
    }

    public function test_legacy_server_key_uses_the_legacy_endpoint(): void
    {
        $this->enableLegacy();
        Http::fake([
            'https://fcm.googleapis.com/fcm/send' => Http::response([
                'success' => 1,
                'failure' => 0,
                'results' => [['message_id' => '0:test']],
            ], 200),
        ]);

        $result = app(FirebaseMessagingService::class)->sendMany(
            ['device-token-1', 'device-token-1'],
            'Order update',
            'Your order is on the way',
            ['order_id' => 10, 'nested' => ['x' => 1]]
        );

        $this->assertSame('sent', $result['status']);
        $this->assertSame(1, $result['success']);
        $this->assertSame(0, $result['failed']);

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $request->url() === 'https://fcm.googleapis.com/fcm/send'
                && $request->hasHeader('Authorization', 'key=legacy-server-key-test')
                && $payload['registration_ids'] === ['device-token-1']
                && $payload['notification']['title'] === 'Order update'
                && $payload['notification']['body'] === 'Your order is on the way'
                && $payload['data'] === ['order_id' => '10'];
        });

        $log = MessageLog::where('channel', 'push')->latest('id')->firstOrFail();
        $this->assertSame('sent', $log->status);
        $this->assertSame('fcm_legacy', $log->provider);
        $this->assertNotSame('device-token-1', $log->recipient_masked);
    }

    public function test_legacy_failure_is_logged_without_the_server_key(): void
    {
        $this->enableLegacy('super-secret-legacy-key');
        Http::fake([
            'https://fcm.googleapis.com/fcm/send' => Http::response('Unauthorized key=super-secret-legacy-key', 401),
        ]);

        $result = app(FirebaseMessagingService::class)->send('device-token-1', 'Title', 'Body');

        $this->assertSame('failed', $result['status']);

        $log = MessageLog::where('channel', 'push')->latest('id')->firstOrFail();
        $this->assertSame('failed', $log->status);
        $this->assertSame('http_401', $log->error_code);
        $this->assertStringNotContainsString('super-secret-legacy-key', (string) $log->error_message);
    }

    public function test_service_account_credentials_use_the_v1_messaging_client(): void
    {
        $this->enableServiceAccount();

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('send')
            ->once()
            ->with(Mockery::type(CloudMessage::class))
            ->andReturn(['name' => 'projects/elite-clean-test/messages/1']);

        $service = app(FirebaseMessagingService::class);
        $this->injectMessaging($service, $messaging);

        $result = $service->send('device-token-1', 'Title', 'Body', ['order_id' => 10]);

        $this->assertSame('sent', $result['status']);
        $this->assertSame(1, $result['success']);

        $log = MessageLog::where('channel', 'push')->latest('id')->firstOrFail();
        $this->assertSame('sent', $log->status);
        $this->assertSame('fcm_v1', $log->provider);
    }

    public function test_service_account_failure_is_logged_without_secrets(): void
    {
        $this->enableServiceAccount();

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('send')
            ->once()
            ->andThrow(new \RuntimeException('Invalid private key test-private-key'));

        $service = app(FirebaseMessagingService::class);
        $this->injectMessaging($service, $messaging);

        $result = $service->send('device-token-1', 'Title', 'Body');

        $this->assertSame('failed', $result['status']);

        $log = MessageLog::where('channel', 'push')->latest('id')->firstOrFail();
        $this->assertSame('fcm_error', $log->error_code);
        $this->assertSame('FCM delivery failed.', $log->error_message);
        $this->assertStringNotContainsString('test-private-key', (string) $log->error_message);
    }

    public function test_invalid_service_account_json_does_not_send(): void
    {
        $this->settings()->set('firebase.enabled', '1', ['group' => 'firebase']);
        $this->settings()->set('firebase.project_id', 'elite-clean-test', ['group' => 'firebase']);
        $this->settings()->set('firebase.credentials_json', '{not-valid-json', [
            'group' => 'firebase',
            'is_secret' => true,
        ]);
        Http::fake();

        $result = app(FirebaseMessagingService::class)->send('device-token-1', 'Title', 'Body');

        $this->assertSame('not_configured', $result['status']);
        Http::assertNothingSent();
    }

    public function test_credentials_source_reports_settings_and_legacy_paths(): void
    {
        $service = app(FirebaseMessagingService::class);
        $this->assertSame('none', $service->credentialsSource());

        $this->enableLegacy();
        $this->assertSame('legacy_server_key', $service->credentialsSource());

        $this->settings()->set('firebase.project_id', 'elite-clean-test', ['group' => 'firebase']);
        $this->settings()->set('firebase.credentials_json', $this->credentialsJson(), [
            'group' => 'firebase',
            'is_secret' => true,
        ]);
        $this->assertSame('integration_settings', $service->credentialsSource());

        $status = $service->status();
        $this->assertTrue($status['credentials_present']);
        $this->assertSame('integration_settings', $status['credentials_source']);
        $this->assertStringNotContainsString('test-private-key', json_encode($status));
    }

    public function test_integration_tester_uses_a_non_destructive_probe_and_can_send_explicitly(): void
    {
        $this->enableLegacy();
        Http::fake([
            'https://fcm.googleapis.com/fcm/send' => Http::response(['success' => 1], 200),
        ]);

        $tester = app(IntegrationTester::class);

        $probe = $tester->test('firebase', []);
        $this->assertSame('ok', $probe['status']);
        $this->assertSame('success', $this->settings()->get('firebase.last_test_status'));

        $sent = $tester->test('firebase', [
            'device_token' => 'device-token-1',
            'send_test' => '1',
        ]);
        $this->assertSame('sent', $sent['status']);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return ($data['dry_run'] ?? false) === true
                || ($data['notification']['title'] ?? null) === 'Elite Clean test';
        });
    }

    public function test_service_account_connection_test_uses_oauth_and_validate_only(): void
    {
        $this->enableServiceAccount();
        $this->app->instance(ServiceAccountTokenProvider::class, new class extends ServiceAccountTokenProvider {
            public function fetch(array $credentials): ?string
            {
                return 'oauth-test-token';
            }
        });

        Http::fake([
            'https://fcm.googleapis.com/v1/projects/elite-clean-test/messages:send' => Http::response([
                'error' => [
                    'status' => 'NOT_FOUND',
                    'details' => [['errorCode' => 'UNREGISTERED']],
                ],
            ], 404),
        ]);

        $result = app(FirebaseMessagingService::class)->testConnection();

        $this->assertSame('ok', $result['status']);
        $this->assertSame(0, MessageLog::where('channel', 'push')->count());

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request['validate_only'] === true
            && $request['message']['token'] === 'ec-probe-invalid-token'
            && $request->hasHeader('Authorization', 'Bearer oauth-test-token'));
    }

    public function test_service_account_connection_test_reports_oauth_failure(): void
    {
        $this->enableServiceAccount();
        $this->app->instance(ServiceAccountTokenProvider::class, new class extends ServiceAccountTokenProvider {
            public function fetch(array $credentials): ?string
            {
                return null;
            }
        });

        Http::fake();

        $result = app(FirebaseMessagingService::class)->testConnection();

        $this->assertSame('failed', $result['status']);
        Http::assertNothingSent();
    }

    public function test_legacy_connection_test_uses_dry_run(): void
    {
        $this->enableLegacy('legacy-server-key-test');
        Http::fake([
            'https://fcm.googleapis.com/fcm/send' => Http::response(['success' => 1], 200),
        ]);

        $result = app(FirebaseMessagingService::class)->testConnection();

        $this->assertSame('ok', $result['status']);
        Http::assertSent(fn ($request) => $request['dry_run'] === true
            && $request['registration_ids'] === ['ec-probe-invalid-token']
            && $request->hasHeader('Authorization', 'key=legacy-server-key-test'));
        $this->assertSame(0, MessageLog::where('channel', 'push')->count());
    }

    public function test_storage_credentials_are_preferred_and_public_location_is_reported(): void
    {
        $path = FirebaseCredentials::storagePath();
        $existed = is_file($path);
        $original = $existed ? (string) file_get_contents($path) : null;

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }

        try {
            file_put_contents($path, $this->credentialsJson());

            $service = app(FirebaseMessagingService::class);
            $this->assertSame('server_file', $service->credentialsSource());
            $this->assertFalse($service->status()['credentials_in_public']);
        } finally {
            if ($existed && $original !== null) {
                file_put_contents($path, $original);
            } else {
                @unlink($path);
            }
        }
    }
}
