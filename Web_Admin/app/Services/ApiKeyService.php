<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApiKeyService
{
    public const SCOPES = [
        'ping.read' => 'Ping / health check',
        'orders.read' => 'قراءة الطلبات',
        'orders.write' => 'إنشاء وتعديل الطلبات',
        'customers.read' => 'قراءة العملاء',
        'webhooks.read' => 'قراءة Webhooks',
    ];

    public function __construct(private AuditLogger $audit)
    {
    }

    public function generate(string $name, array $scopes, ?Carbon $expiresAt = null, ?int $userId = null): array
    {
        $scopes = $this->normalizeScopes($scopes);
        $plain = 'ec_' . Str::random(48);

        $key = ApiKey::create([
            'name' => $name,
            'key_prefix' => substr($plain, 0, 12),
            'key_hash' => hash('sha256', $plain),
            'scopes' => $scopes,
            'expires_at' => $expiresAt,
            'created_by' => $userId,
        ]);

        $this->audit->log('api_key.created', 'api', 'api_key:' . $key->id, null, [
            'name' => $name,
            'scopes' => $scopes,
            'expires_at' => $expiresAt?->toIso8601String(),
        ]);

        return ['key' => $key, 'plain' => $plain];
    }

    public function findByToken(string $plain): ?ApiKey
    {
        if ($plain === '') {
            return null;
        }

        $key = ApiKey::where('key_hash', hash('sha256', $plain))->first();

        if (!$key || !$key->isActive()) {
            return null;
        }

        return $key;
    }

    public function revoke(ApiKey $key): void
    {
        $key->forceFill(['revoked_at' => now()])->save();
        $this->audit->log('api_key.revoked', 'api', 'api_key:' . $key->id);
    }

    public function normalizeScopes(array $scopes): array
    {
        $allowed = array_keys(self::SCOPES);
        $scopes = array_values(array_unique(array_filter($scopes, fn ($scope) => in_array($scope, $allowed, true))));

        return empty($scopes) ? ['ping.read'] : $scopes;
    }
}
