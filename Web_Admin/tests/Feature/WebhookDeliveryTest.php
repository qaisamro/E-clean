<?php

namespace Tests\Feature;

use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Services\IntegrationSettingsService;
use App\Services\IntegrationTester;
use App\Services\WebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebhookDeliveryTest extends TestCase
{
    use RefreshDatabase;

    private function endpoint(array $overrides = []): WebhookEndpoint
    {
        return WebhookEndpoint::create(array_merge([
            'name' => 'Test receiver',
            'url' => 'https://receiver.test/hooks/orders',
            'secret' => 'whsec_test_secret',
            'events' => ['order.created'],
            'active' => true,
        ], $overrides));
    }

    private function service(): WebhookService
    {
        return app(WebhookService::class);
    }

    public function test_signature_verification_rejects_tampering_wrong_secret_and_stale_timestamps(): void
    {
        $service = $this->service();
        $body = '{"order_id":10}';
        $timestamp = time();
        $signature = $service->signature($body, 'whsec_test_secret', $timestamp);

        $this->assertTrue($service->verifySignature($body, $signature, 'whsec_test_secret', 300));
        $this->assertFalse($service->verifySignature($body . 'x', $signature, 'whsec_test_secret', 300));
        $this->assertFalse($service->verifySignature($body, $signature, 'wrong-secret', 300));

        $stale = $service->signature($body, 'whsec_test_secret', time() - 1000);
        $this->assertFalse($service->verifySignature($body, $stale, 'whsec_test_secret', 300));
    }

    public function test_dispatch_delivers_a_signed_request_with_idempotency(): void
    {
        Http::fake(['https://receiver.test/*' => Http::response('ok', 200)]);
        $endpoint = $this->endpoint();

        $result = $this->service()->dispatchEvent('order.created', ['order_id' => 10], 'order-created:10');
        $this->assertSame(1, $result['deliveries']);

        $delivery = WebhookDelivery::firstOrFail();
        $this->assertSame('delivered', $delivery->status);
        $this->assertSame(1, $delivery->attempts);
        $this->assertNotNull($delivery->delivered_at);
        $this->assertNull($delivery->next_retry_at);

        Http::assertSent(function ($request) use ($endpoint, $delivery) {
            $signature = $request->header('X-EC-Signature')[0] ?? '';

            return $request->url() === $endpoint->url
                && $request->method() === 'POST'
                && $request->header('X-EC-Event')[0] === 'order.created'
                && $request->header('Idempotency-Key')[0] === $delivery->delivery_uuid
                && $this->service()->verifySignature($request->body(), $signature, 'whsec_test_secret');
        });
    }

    public function test_failed_delivery_is_retried_and_then_delivered(): void
    {
        $this->endpoint();
        $calls = 0;
        Http::fake([
            'https://receiver.test/*' => function () use (&$calls) {
                $calls++;

                return $calls === 1
                    ? Http::response('server error', 500)
                    : Http::response('ok', 200);
            },
        ]);

        $this->service()->dispatchEvent('order.created', ['order_id' => 11]);
        $delivery = WebhookDelivery::firstOrFail();

        $this->assertSame('failed', $delivery->status);
        $this->assertSame(1, $delivery->attempts);
        $this->assertNotNull($delivery->next_retry_at);
        $this->assertSame(500, $delivery->response_code);

        $this->service()->retry($delivery);

        $delivery->refresh();
        $this->assertSame('delivered', $delivery->status);
        $this->assertSame(2, $delivery->attempts);
        $this->assertNull($delivery->next_retry_at);
        $this->assertNotNull($delivery->delivered_at);
    }

    public function test_retry_stops_at_the_configured_maximum(): void
    {
        $this->endpoint();
        app(IntegrationSettingsService::class)->set('webhooks.max_attempts', '1', ['group' => 'webhooks']);
        Http::fake(['https://receiver.test/*' => Http::response('server error', 500)]);

        $this->service()->dispatchEvent('order.created', ['order_id' => 12]);
        $delivery = WebhookDelivery::firstOrFail();

        $this->service()->retry($delivery);

        $delivery->refresh();
        $this->assertSame('failed', $delivery->status);
        $this->assertSame(1, $delivery->attempts);
        $this->assertNull($delivery->next_retry_at);
    }

    public function test_deduplication_prevents_duplicate_deliveries(): void
    {
        $this->endpoint();
        Http::fake(['https://receiver.test/*' => Http::response('ok', 200)]);

        $first = $this->service()->dispatchEvent('order.created', ['order_id' => 13], 'order-created:13');
        $second = $this->service()->dispatchEvent('order.created', ['order_id' => 13], 'order-created:13');

        $this->assertSame(1, $first['deliveries']);
        $this->assertSame(0, $second['deliveries']);
        $this->assertSame(1, WebhookDelivery::count());
        Http::assertSentCount(1);
    }

    public function test_inactive_endpoints_and_disabled_webhooks_send_nothing(): void
    {
        Http::fake();
        $this->endpoint(['active' => false]);

        $result = $this->service()->dispatchEvent('order.created', ['order_id' => 14]);
        $this->assertSame('dispatched', $result['status']);
        $this->assertSame(0, $result['deliveries']);

        app(IntegrationSettingsService::class)->set('webhooks.enabled', '0', ['group' => 'webhooks']);
        $result = $this->service()->dispatchEvent('order.created', ['order_id' => 15]);
        $this->assertSame('skipped', $result['status']);

        Http::assertNothingSent();
        $this->assertSame(0, WebhookDelivery::count());
    }

    public function test_endpoint_secret_is_encrypted_and_hidden(): void
    {
        $endpoint = $this->endpoint();

        $raw = DB::table('webhook_endpoints')->where('id', $endpoint->id)->value('secret');
        $this->assertNotSame('whsec_test_secret', $raw);
        $this->assertSame('whsec_test_secret', \Illuminate\Support\Facades\Crypt::decryptString($raw));
        $this->assertArrayNotHasKey('secret', $endpoint->toArray());
    }

    public function test_integration_tester_records_a_successful_webhook_test(): void
    {
        Http::fake(['https://receiver.test/*' => Http::response('ok', 200)]);
        $endpoint = $this->endpoint(['events' => ['webhook.test']]);

        $result = app(IntegrationTester::class)->test('webhooks', ['endpoint_id' => $endpoint->id]);

        $this->assertSame('delivered', $result['status']);
        $this->assertSame(1, WebhookDelivery::where('event', 'webhook.test')->count());
        $this->assertSame(
            'success',
            app(IntegrationSettingsService::class)->get('webhooks.last_test_status')
        );
    }
}
