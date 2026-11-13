<?php

namespace App\Services;

use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookService
{
    public const EVENTS = [
        'order.created' => 'طلب جديد',
        'order.status_changed' => 'تغير حالة الطلب',
        'order.paid' => 'تم دفع الطلب',
        'customer.created' => 'عميل جديد',
        'integration.test' => 'اختبار تكامل',
        'webhook.test' => 'اختبار Webhook',
    ];

    public function __construct(
        private IntegrationSettingsService $settings,
        private MessageLogger $logger,
        private AuditLogger $audit
    ) {
    }

    public function dispatchEvent(string $event, array $payload, ?string $dedupeKey = null): array
    {
        if (!$this->settings->isEnabled('webhooks')) {
            return ['status' => 'skipped', 'deliveries' => 0];
        }

        $endpoints = WebhookEndpoint::where('active', true)->get()
            ->filter(fn (WebhookEndpoint $endpoint) => $this->subscribed($endpoint, $event));

        $count = 0;
        foreach ($endpoints as $endpoint) {
            $delivery = $this->createDelivery($endpoint, $event, $payload, $dedupeKey);
            if (!$delivery) {
                continue;
            }
            if (!$delivery->wasRecentlyCreated) {
                continue;
            }
            $this->deliver($delivery);
            $count++;
        }

        return ['status' => 'dispatched', 'deliveries' => $count];
    }

    public function deliverTest(WebhookEndpoint $endpoint): array
    {
        $delivery = $this->createDelivery($endpoint, 'webhook.test', [
            'message' => 'Elite Clean webhook test',
            'timestamp' => now()->toIso8601String(),
        ], 'test:' . Str::uuid());

        if (!$delivery) {
            return ['status' => 'skipped', 'message' => 'Endpoint is not active.'];
        }

        $this->deliver($delivery);

        return [
            'status' => $delivery->fresh()->status,
            'http_status' => $delivery->fresh()->response_code,
            'message' => $delivery->fresh()->error ?: 'Test delivery completed.',
        ];
    }

    public function deliver(WebhookDelivery $delivery): void
    {
        $endpoint = $delivery->endpoint;
        if (!$endpoint || !$endpoint->active) {
            $delivery->forceFill(['status' => 'skipped', 'error' => 'Endpoint is inactive.'])->save();

            return;
        }

        $body = json_encode($delivery->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $timestamp = time();
        $signature = $this->signature($body, $endpoint->secret, $timestamp);
        $timeout = max(1, (int) $this->settings->get('webhooks.timeout', 10));

        $delivery->increment('attempts');
        $delivery->forceFill(['last_attempt_at' => now(), 'signature' => $signature])->save();

        try {
            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-EC-Event' => $delivery->event,
                    'X-EC-Delivery' => $delivery->delivery_uuid,
                    'X-EC-Timestamp' => (string) $timestamp,
                    'X-EC-Signature' => $signature,
                    'Idempotency-Key' => $delivery->delivery_uuid,
                    'User-Agent' => 'EliteClean-Webhooks/1.0',
                ])
                ->withBody($body, 'application/json')
                ->post($endpoint->url);

            $success = $response->successful();
            $delivery->forceFill([
                'status' => $success ? 'delivered' : 'failed',
                'response_code' => $response->status(),
                'response_body' => Str::limit((string) $response->body(), 2000),
                'delivered_at' => $success ? now() : null,
                'next_retry_at' => $success ? null : $this->nextRetryAt($delivery->attempts),
                'error' => $success ? null : 'Receiver returned HTTP ' . $response->status(),
            ])->save();
        } catch (\Throwable $e) {
            $delivery->forceFill([
                'status' => 'failed',
                'error' => 'Webhook connection failed.',
                'next_retry_at' => $this->nextRetryAt($delivery->attempts),
            ])->save();
            $this->logger->safeLog('warning', 'Webhook delivery failed', [
                'endpoint_id' => $endpoint->id,
                'delivery' => $delivery->delivery_uuid,
                'exception' => get_class($e),
            ]);
        }
    }

    public function retry(WebhookDelivery $delivery): void
    {
        $maxAttempts = max(1, (int) $this->settings->get('webhooks.max_attempts', 5));

        if ($delivery->attempts >= $maxAttempts) {
            $delivery->forceFill(['status' => 'failed', 'next_retry_at' => null])->save();

            return;
        }

        $this->deliver($delivery);
    }

    public function retryPending(int $limit = 50): int
    {
        $deliveries = WebhookDelivery::where('status', 'failed')
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', now())
            ->limit($limit)
            ->get();

        foreach ($deliveries as $delivery) {
            $this->retry($delivery);
        }

        return $deliveries->count();
    }

    public function signature(string $body, string $secret, ?int $timestamp = null): string
    {
        $timestamp ??= time();

        return 't=' . $timestamp . ',v1=' . hash_hmac('sha256', $timestamp . '.' . $body, $secret);
    }

    public function verifySignature(string $body, string $header, string $secret, ?int $ttl = null): bool
    {
        $ttl ??= max(30, (int) $this->settings->get('webhooks.signature_ttl', 300));

        if (!preg_match('/t=(\d+),v1=([a-f0-9]{64})/i', $header, $matches)) {
            return false;
        }

        $timestamp = (int) $matches[1];
        if (abs(time() - $timestamp) > $ttl) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $body, $secret);

        return hash_equals($expected, strtolower($matches[2]));
    }

    private function createDelivery(WebhookEndpoint $endpoint, string $event, array $payload, ?string $dedupeKey): ?WebhookDelivery
    {
        if (!$endpoint->active) {
            return null;
        }

        if ($dedupeKey) {
            return WebhookDelivery::firstOrCreate(
                [
                    'webhook_endpoint_id' => $endpoint->id,
                    'dedupe_key' => $dedupeKey,
                ],
                [
                    'event' => $event,
                    'delivery_uuid' => (string) Str::uuid(),
                    'payload' => $payload,
                    'status' => 'pending',
                    'attempts' => 0,
                ]
            );
        }

        return WebhookDelivery::create([
            'webhook_endpoint_id' => $endpoint->id,
            'event' => $event,
            'delivery_uuid' => (string) Str::uuid(),
            'payload' => $payload,
            'status' => 'pending',
            'attempts' => 0,
        ]);
    }

    private function subscribed(WebhookEndpoint $endpoint, string $event): bool
    {
        $events = $endpoint->events ?? [];

        return in_array('*', $events, true) || in_array($event, $events, true);
    }

    private function nextRetryAt(int $attempts): ?\Illuminate\Support\Carbon
    {
        $delays = [60, 300, 1800, 7200, 21600];
        $delay = $delays[min(max(0, $attempts - 1), count($delays) - 1)];
        $maxAttempts = max(1, (int) $this->settings->get('webhooks.max_attempts', 5));

        if ($attempts >= $maxAttempts) {
            return null;
        }

        return now()->addSeconds($delay);
    }
}
