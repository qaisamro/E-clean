<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class OtpRateLimiter
{
    public function __construct(private IntegrationSettingsService $settings)
    {
    }

    public function normalizeContact(string $contact): string
    {
        $contact = trim($contact);

        if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
            return strtolower($contact);
        }

        $digits = preg_replace('/\D+/', '', $contact) ?? '';
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // Compare phone numbers by their significant local part so that
        // +970599123456, 00970599123456 and 0599123456 share one bucket.
        return strlen($digits) > 9 ? substr($digits, -9) : $digits;
    }

    public function check(string $action, string $contact, string $purpose, Request $request, ?int $userId = null): ?array
    {
        $normalized = $this->normalizeContact($contact);
        $lockKey = 'otp:lock:' . sha1($normalized);

        if (RateLimiter::tooManyAttempts($lockKey, 1)) {
            return [
                'reason' => 'locked',
                'retry_after' => RateLimiter::availableIn($lockKey),
            ];
        }

        $window = max(60, (int) $this->settings->get('otp.rate_limit_window', 3600));
        $limit = $this->maxFor($action);
        $ipLimit = max(1, (int) $this->settings->get('otp.ip_limit', 20));
        $deviceLimit = max(1, (int) $this->settings->get('otp.device_limit', 10));

        $buckets = [
            ['key' => "otp:{$action}:contact:" . sha1($normalized), 'max' => $limit],
            ['key' => "otp:{$action}:ip:" . sha1((string) $request->ip()), 'max' => $ipLimit],
        ];

        if ($userId) {
            $buckets[] = ['key' => "otp:{$action}:user:" . $userId, 'max' => $limit];
        }

        $device = (string) ($request->header('X-Device-Id') ?? $request->input('device_key', ''));
        if ($device !== '') {
            $buckets[] = ['key' => "otp:{$action}:device:" . sha1($device), 'max' => $deviceLimit];
        }

        foreach ($buckets as $bucket) {
            if (RateLimiter::tooManyAttempts($bucket['key'], $bucket['max'])) {
                return [
                    'reason' => 'rate_limited',
                    'retry_after' => RateLimiter::availableIn($bucket['key']),
                ];
            }
        }

        foreach ($buckets as $bucket) {
            RateLimiter::hit($bucket['key'], $window);
        }

        return null;
    }

    public function recordFailure(string $contact): void
    {
        $normalized = $this->normalizeContact($contact);
        $key = 'otp:fail:' . sha1($normalized);
        $maxAttempts = max(1, (int) $this->settings->get('otp.max_attempts', 5));
        $lockout = max(0, (int) $this->settings->get('otp.lockout_seconds', 900));

        RateLimiter::hit($key, max(60, $lockout ?: 900));

        if (RateLimiter::attempts($key) >= $maxAttempts && $lockout > 0) {
            RateLimiter::hit('otp:lock:' . sha1($normalized), $lockout);
            RateLimiter::clear($key);
        }
    }

    public function clear(string $contact): void
    {
        $normalized = $this->normalizeContact($contact);
        RateLimiter::clear('otp:fail:' . sha1($normalized));
        RateLimiter::clear('otp:lock:' . sha1($normalized));
    }

    public function maxFor(string $action): int
    {
        $key = match ($action) {
            'send' => 'otp.send_limit',
            'resend' => 'otp.resend_limit',
            'verify' => 'otp.verify_limit',
            default => 'otp.send_limit',
        };

        return max(1, (int) $this->settings->get($key, 5));
    }
}
