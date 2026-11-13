<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use App\Support\FirebaseCredentials;
use App\Support\IntegrationSchema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class IntegrationSettingsService
{
    public const MASK = '********';

    public function get(string $key, $default = null)
    {
        return Cache::remember("integration:{$key}", 300, fn () => IntegrationSetting::getValue($key, $default));
    }

    public function set(string $key, $value, array $meta = []): void
    {
        IntegrationSetting::setValue($key, $value, $meta);
        Cache::forget("integration:{$key}");
        Cache::forget('integration:group:' . Str::before($key, '.'));
    }

    public function forget(string $key): void
    {
        Cache::forget("integration:{$key}");
    }

    /**
     * Return a group as short keys. Secret values are always masked.
     */
    public function group(string $group): array
    {
        $defaults = IntegrationSchema::defaults($group);
        $values = $defaults;

        $rows = IntegrationSetting::where('group', $group)->get();
        foreach ($rows as $row) {
            $short = Str::after($row->key, $group . '.');
            $values[$short] = $row->is_secret
                ? ($row->value !== null && $row->value !== '' ? self::MASK : '')
                : $row->value;
        }

        return $values;
    }

    public function groupWithMeta(string $group): array
    {
        $defaults = IntegrationSchema::defaults($group);
        $values = $defaults;
        $secrets = [];

        $rows = IntegrationSetting::where('group', $group)->get();
        foreach ($rows as $row) {
            $short = Str::after($row->key, $group . '.');
            if ($row->is_secret) {
                $secrets[$short] = $row->value !== null && $row->value !== '';
                $values[$short] = '';
                continue;
            }
            $values[$short] = $row->value;
        }

        return ['values' => $values, 'secrets' => $secrets];
    }

    public function isEnabled(string $group): bool
    {
        $default = IntegrationSchema::defaults($group)['enabled'] ?? '1';

        return (string) $this->get("{$group}.enabled", $default) === '1';
    }

    public function isConfigured(string $group): bool
    {
        return match ($group) {
            'sms' => $this->get('sms.provider', 'generic_http') === 'log'
                ? true
                : ($this->hasAll(['sms.api_url', 'sms.user_name', 'sms.password', 'sms.sender_id'])
                    || $this->legacySmsConfigured()),
            'otp' => $this->isEnabled('otp'),
            'firebase' => ($this->has('firebase.project_id')
                    && ($this->has('firebase.credentials_json') || $this->has('firebase.legacy_server_key')))
                || $this->has('firebase.legacy_server_key')
                || $this->legacyFirebaseConfigured(),
            'google_maps' => $this->hasAny([
                'google_maps.server_api_key',
                'google_maps.web_api_key',
                'google_maps.android_api_key',
                'google_maps.ios_api_key',
            ]),
            'smtp' => $this->hasAll(['smtp.host', 'smtp.from_address']),
            'whatsapp' => $this->hasAll(['whatsapp.access_token', 'whatsapp.phone_number_id']),
            'api' => $this->isEnabled('api'),
            'webhooks' => $this->isEnabled('webhooks'),
            'security' => true,
            default => false,
        };
    }

    /**
     * Honest status: "Connected" is only returned after a successful real
     * connection test recorded within the last seven days.
     */
    public function status(string $group): array
    {
        $enabled = $this->isEnabled($group);
        $configured = $this->isConfigured($group);
        $lastStatus = (string) $this->get("{$group}.last_test_status", '');
        $lastAt = $this->get("{$group}.last_test_at");
        $lastMessage = (string) $this->get("{$group}.last_test_message", '');
        $recent = false;

        if ($lastAt) {
            try {
                $recent = now()->subDays(7)->lte(\Carbon\Carbon::parse($lastAt));
            } catch (\Throwable) {
                $recent = false;
            }
        }

        $externalGroups = ['sms', 'firebase', 'google_maps', 'smtp', 'whatsapp'];

        if (!$enabled) {
            $status = 'disabled';
            $label = 'معطل';
        } elseif (!$configured) {
            $status = 'not_configured';
            $label = 'غير مهيأ';
        } elseif ($lastStatus === 'success' && $recent) {
            $status = in_array($group, $externalGroups, true) ? 'connected' : 'configured';
            $label = in_array($group, $externalGroups, true) ? 'متصل ومختبر' : 'مهيأ ومختبر';
        } elseif ($lastStatus === 'error') {
            $status = 'error';
            $label = 'خطأ في الاتصال';
        } else {
            $status = 'configured';
            $label = 'مهيأ (غير مختبر)';
        }

        $result = [
            'status' => $status,
            'label' => $label,
            'enabled' => $enabled,
            'configured' => $configured,
            'last_test_status' => $lastStatus ?: null,
            'last_test_at' => $lastAt,
            'last_test_message' => $lastMessage ?: null,
        ];

        if ($group === 'firebase') {
            $result['credentials_in_public'] = FirebaseCredentials::publicFileExists();
        }

        return $result;
    }

    public function recordTest(string $group, bool $success, ?string $message = null): void
    {
        $this->set("{$group}.last_test_status", $success ? 'success' : 'error', [
            'group' => $group,
            'is_secret' => false,
        ]);
        $this->set("{$group}.last_test_at", now()->toIso8601String(), [
            'group' => $group,
            'is_secret' => false,
        ]);
        $this->set("{$group}.last_test_message", $this->sanitizeMessage($message), [
            'group' => $group,
            'is_secret' => false,
        ]);
    }

    public function has(string $key): bool
    {
        $value = $this->get($key);

        return $value !== null && $value !== '';
    }

    public function hasAll(array $keys): bool
    {
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }

        return true;
    }

    public function hasAny(array $keys): bool
    {
        foreach ($keys as $key) {
            if ($this->has($key)) {
                return true;
            }
        }

        return false;
    }

    public function sanitizeMessage(?string $message): string
    {
        if (!$message) {
            return '';
        }

        $message = preg_replace('/(bearer\s+)[A-Za-z0-9\-._~+\/]+=*/i', '$1[redacted]', $message);
        $message = preg_replace('/(password|secret|token|api[_-]?key)\s*[:=]\s*\S+/i', '$1=[redacted]', $message);

        return Str::limit($message, 300);
    }

    /**
     * Legacy env configuration is still a supported transition path.
     */
    public function legacySmsConfigured(): bool
    {
        return (string) config('app.sms_base_url') !== ''
            && (string) config('app.sms_user_name') !== ''
            && (string) config('app.sms_password') !== '';
    }

    public function legacyFirebaseConfigured(): bool
    {
        if ((string) config('app.fcm_server_key') !== '') {
            return true;
        }

        return FirebaseCredentials::exists();
    }
}
