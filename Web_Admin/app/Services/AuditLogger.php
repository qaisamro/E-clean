<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    private const SENSITIVE = [
        'password',
        'password_confirmation',
        'current_password',
        'secret',
        'token',
        'access_token',
        'refresh_token',
        'api_key',
        'api_secret',
        'key',
        'otp',
        'code',
        'authorization',
        'credentials',
        'credentials_json',
        'server_key',
        'private_key',
    ];

    public function log(
        string $action,
        ?string $group = null,
        ?string $settingKey = null,
        mixed $oldValue = null,
        mixed $newValue = null,
        array $meta = [],
        ?Request $request = null
    ): ?AuditLog {
        try {
            $request ??= request();
            $user = Auth::user();

            return AuditLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'group_name' => $group,
                'setting_key' => $settingKey,
                'old_value' => $this->encodeValue($oldValue, $settingKey),
                'new_value' => $this->encodeValue($newValue, $settingKey),
                'ip_address' => $request?->ip(),
                'user_agent' => substr((string) $request?->userAgent(), 0, 500),
                'meta' => $this->redact($meta),
            ]);
        } catch (\Throwable) {
            // Audit logging must never break the business operation.
            return null;
        }
    }

    public function changedSetting(string $group, string $key, mixed $oldValue, mixed $newValue, bool $secret = false): void
    {
        $this->log(
            'integration.setting.updated',
            $group,
            $key,
            $secret ? '[secret]' : $oldValue,
            $secret ? '[secret changed]' : $newValue,
            ['secret' => $secret]
        );
    }

    public function redact(array $data): array
    {
        return collect($data)->map(function ($value, $key) {
            if (is_string($key) && $this->isSensitiveKey($key)) {
                return '[redacted]';
            }

            if (is_array($value)) {
                return $this->redact($value);
            }

            return $value;
        })->all();
    }

    public function isSensitiveKey(string $key): bool
    {
        $key = strtolower($key);
        foreach (self::SENSITIVE as $needle) {
            if (str_contains($key, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function encodeValue(mixed $value, ?string $key): ?string
    {
        if ($key && $this->isSensitiveKey($key)) {
            return '[redacted]';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($this->redact(Arr::wrap($value)), JSON_UNESCAPED_UNICODE);
        }

        return $value === null ? null : (string) $value;
    }
}
