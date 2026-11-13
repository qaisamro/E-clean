<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\AuditLog;
use App\Models\IdempotencyKey;
use App\Services\ApiKeyService;
use App\Services\IntegrationSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiKeyIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    private function service(): ApiKeyService
    {
        return app(ApiKeyService::class);
    }

    private function settings(): IntegrationSettingsService
    {
        return app(IntegrationSettingsService::class);
    }

    public function test_api_requires_a_valid_active_key(): void
    {
        $this->getJson('/api/partner/ping')->assertUnauthorized();

        $this->withHeader('X-API-Key', 'ec_invalid_key')
            ->getJson('/api/partner/ping')
            ->assertUnauthorized();

        $generated = $this->service()->generate('Ping key', ['ping.read']);
        $this->withHeader('X-API-Key', $generated['plain'])
            ->getJson('/api/partner/ping')
            ->assertOk()
            ->assertJsonPath('data.ok', true);
    }

    public function test_api_can_be_disabled_entirely(): void
    {
        $this->settings()->set('api.enabled', '0', ['group' => 'api']);
        $generated = $this->service()->generate('Disabled', ['ping.read']);

        $this->withHeader('X-API-Key', $generated['plain'])
            ->getJson('/api/partner/ping')
            ->assertStatus(503);
    }

    public function test_scopes_are_enforced(): void
    {
        $generated = $this->service()->generate('Read only', ['ping.read']);

        $this->withHeader('X-API-Key', $generated['plain'])
            ->postJson('/api/partner/webhooks/test')
            ->assertForbidden();

        $webhookKey = $this->service()->generate('Webhooks', ['webhooks.read']);
        $this->withHeader('X-API-Key', $webhookKey['plain'])
            ->postJson('/api/partner/webhooks/test')
            ->assertOk();
    }

    public function test_expired_and_revoked_keys_are_rejected(): void
    {
        $expired = $this->service()->generate('Expired', ['ping.read'], now()->subMinute());
        $this->withHeader('X-API-Key', $expired['plain'])
            ->getJson('/api/partner/ping')
            ->assertUnauthorized();

        $generated = $this->service()->generate('Revoked', ['ping.read']);
        $this->service()->revoke($generated['key']);
        $this->withHeader('X-API-Key', $generated['plain'])
            ->getJson('/api/partner/ping')
            ->assertUnauthorized();
    }

    public function test_keys_are_hashed_scoped_and_the_plain_value_is_not_persisted(): void
    {
        $generated = $this->service()->generate('Hashed key', ['ping.read', 'orders.read']);

        $this->assertStringStartsWith('ec_', $generated['plain']);

        $row = ApiKey::findOrFail($generated['key']->id);
        $this->assertSame(hash('sha256', $generated['plain']), $row->key_hash);
        $this->assertNotSame($generated['plain'], $row->key_hash);
        $this->assertSame(['ping.read', 'orders.read'], $row->scopes);
        $this->assertArrayNotHasKey('key_hash', $row->toArray());

        $audit = AuditLog::where('action', 'api_key.created')->latest('id')->firstOrFail();
        $serialized = $audit->old_value . $audit->new_value . json_encode($audit->meta);
        $this->assertStringNotContainsString($generated['plain'], (string) $serialized);
    }

    public function test_key_rate_limit_is_enforced_per_key(): void
    {
        $this->settings()->set('api.rate_limit_per_minute', '1', ['group' => 'api']);
        $generated = $this->service()->generate('Limited', ['ping.read']);

        $this->withHeader('X-API-Key', $generated['plain'])
            ->getJson('/api/partner/ping')
            ->assertOk();

        $this->withHeader('X-API-Key', $generated['plain'])
            ->getJson('/api/partner/ping')
            ->assertStatus(429)
            ->assertHeader('Retry-After');
    }

    public function test_idempotent_request_replays_the_original_response(): void
    {
        $generated = $this->service()->generate('Idempotent', ['ping.read']);
        $headers = [
            'X-API-Key' => $generated['plain'],
            'Idempotency-Key' => 'idem-echo-1',
        ];

        $first = $this->withHeaders($headers)->postJson('/api/partner/echo', ['message' => 'hello']);
        $first->assertOk()->assertJsonPath('data.message', 'hello');

        $second = $this->withHeaders($headers)->postJson('/api/partner/echo', ['message' => 'hello']);
        $second->assertOk()->assertHeader('Idempotent-Replayed', 'true');
        $this->assertSame($first->getContent(), $second->getContent());

        $this->assertDatabaseCount('idempotency_keys', 1);
        $this->assertDatabaseHas('idempotency_keys', [
            'api_key_id' => $generated['key']->id,
            'idempotency_key' => 'idem-echo-1',
        ]);
    }

    public function test_same_idempotency_key_with_a_different_request_is_rejected(): void
    {
        $generated = $this->service()->generate('Idempotent conflict', ['ping.read']);
        $headers = [
            'X-API-Key' => $generated['plain'],
            'Idempotency-Key' => 'idem-conflict-1',
        ];

        $this->withHeaders($headers)->postJson('/api/partner/echo', ['message' => 'first'])->assertOk();

        $this->withHeaders($headers)
            ->postJson('/api/partner/echo', ['message' => 'second'])
            ->assertStatus(422);
    }

    public function test_expired_idempotency_key_can_be_used_again(): void
    {
        $generated = $this->service()->generate('Idempotent expired', ['ping.read']);
        $headers = [
            'X-API-Key' => $generated['plain'],
            'Idempotency-Key' => 'idem-expired-1',
        ];

        $first = $this->withHeaders($headers)->postJson('/api/partner/echo', ['message' => 'first']);

        IdempotencyKey::where('idempotency_key', 'idem-expired-1')->update([
            'expires_at' => now()->subMinute(),
        ]);

        $second = $this->withHeaders($headers)->postJson('/api/partner/echo', ['message' => 'first']);
        $second->assertOk();
        $second->assertHeaderMissing('Idempotent-Replayed');
        $this->assertSame($first->getContent(), $second->getContent());
    }

    public function test_idempotency_without_a_key_is_not_replayed(): void
    {
        $generated = $this->service()->generate('No idempotency', ['ping.read']);

        $first = $this->withHeader('X-API-Key', $generated['plain'])
            ->postJson('/api/partner/echo', ['message' => 'hello']);
        $first->assertOk();

        $second = $this->withHeader('X-API-Key', $generated['plain'])
            ->postJson('/api/partner/echo', ['message' => 'hello']);
        $second->assertOk()->assertHeaderMissing('Idempotent-Replayed');

        $this->assertDatabaseCount('idempotency_keys', 0);
    }
}
