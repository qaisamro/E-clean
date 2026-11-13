<?php

namespace App\Services;

use App\Models\VerificationCode;
use App\Repositories\VerificationCodeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OtpService
{
    public const PURPOSES = ['registration', 'login', 'password_reset', 'phone_change', 'generic'];

    public function __construct(
        private VerificationCodeRepository $codes,
        private IntegrationSettingsService $settings,
        private OtpRateLimiter $limiter,
        private NotificationDispatcher $dispatcher
    ) {
    }

    public function enabled(): bool
    {
        return (string) $this->settings->get('otp.enabled', '1') === '1';
    }

    /**
     * OTP can be enabled globally and then disabled for a single flow without
     * affecting the others. "generic" only follows the global switch.
     */
    public function enabledFor(string $purpose): bool
    {
        $purpose = $this->purposeForChannel($purpose);

        if (!$this->enabled()) {
            return false;
        }

        if ($purpose === 'generic') {
            return true;
        }

        return (string) $this->settings->get("otp.enabled_{$purpose}", '1') === '1';
    }

    /**
     * Create and deliver a new OTP. The plaintext code is returned only to the
     * caller so it can be shown in-app in an explicitly allowed test environment.
     */
    public function issue($user, string $contact, string $purpose, Request $request, bool $isResend = false): array
    {
        if (!in_array($purpose, self::PURPOSES, true)) {
            $purpose = 'generic';
        }

        if (!$this->enabledFor($purpose)) {
            return ['status' => 'disabled', 'message' => 'OTP is disabled for this operation.'];
        }

        $limited = $this->limiter->check($isResend ? 'resend' : 'send', $contact, $purpose, $request, $user?->id);
        if ($limited) {
            return [
                'status' => 'rate_limited',
                'message' => 'Too many OTP requests. Please try again later.',
                'retry_after' => $limited['retry_after'] ?? 60,
            ];
        }

        if ($isResend) {
            $cooldown = max(0, (int) $this->settings->get('otp.resend_after', 60));
            $latest = $this->codes->latestSent($contact, $purpose);
            if ($latest?->last_sent_at && $latest->last_sent_at->copy()->addSeconds($cooldown)->isFuture()) {
                $target = $latest->last_sent_at->copy()->addSeconds($cooldown);

                return [
                    'status' => 'cooldown',
                    'message' => 'Please wait before requesting another code.',
                    'retry_after' => max(1, (int) ceil(now()->diffInSeconds($target, false))),
                ];
            }
        }

        $lock = Cache::lock('otp:issue:' . sha1($purpose . '|' . $this->limiter->normalizeContact($contact)), 10);

        try {
            if (!$lock->get()) {
                return ['status' => 'rate_limited', 'message' => 'Another OTP request is in progress.', 'retry_after' => 5];
            }

            $record = $this->codes->findOrCreateByContact($contact, $purpose);
            $code = $record->plainOtp;
        } finally {
            optional($lock)->release();
        }

        $deliveries = $this->dispatcher->sendOtp($user, $contact, $purpose, (string) $code);
        $successful = collect($deliveries)->filter(fn ($item) => in_array($item['status'] ?? null, ['sent', 'queued'], true))->count();

        return [
            'status' => $successful > 0 ? 'sent' : 'delivery_failed',
            'message' => $successful > 0 ? 'Verification code sent.' : 'Verification code could not be delivered.',
            'channels' => $deliveries,
            'code' => (string) $code,
            'show_in_app' => $this->dispatcher->shouldShowInApp($contact),
            'expires_in' => max(30, (int) $this->settings->get('otp.ttl', 300)),
        ];
    }

    public function verify(
        string $contact,
        string $otp,
        string $purpose,
        Request $request,
        ?int $userId = null,
        bool $consume = false
    ): array {
        if (!in_array($purpose, self::PURPOSES, true)) {
            $purpose = 'generic';
        }

        if (!$this->enabledFor($purpose)) {
            return ['status' => 'disabled', 'message' => 'OTP is disabled for this operation.'];
        }

        $limited = $this->limiter->check('verify', $contact, $purpose, $request, $userId);
        if ($limited) {
            return [
                'status' => 'rate_limited',
                'message' => 'Too many verification attempts. Please try again later.',
                'retry_after' => $limited['retry_after'] ?? 60,
            ];
        }

        $record = $this->codes->verify($contact, $otp, $purpose);

        if (!$record) {
            $this->limiter->recordFailure($contact);

            return ['status' => 'invalid', 'message' => 'Invalid or expired verification code.'];
        }

        $this->limiter->clear($contact);

        if ($consume) {
            $record->delete();
        } else {
            $this->codes->markVerified($record);
        }

        return ['status' => 'verified', 'record' => $record];
    }

    public function verifyAndConsume(string $contact, string $otp, string $purpose, Request $request, ?int $userId = null): array
    {
        return $this->verify($contact, $otp, $purpose, $request, $userId, true);
    }

    public function showInApp(string $contact): bool
    {
        return $this->dispatcher->shouldShowInApp($contact);
    }

    public function normalizeContact(string $contact): string
    {
        return $this->limiter->normalizeContact($contact);
    }

    public function ttl(): int
    {
        return max(30, (int) $this->settings->get('otp.ttl', 300));
    }

    public function purposeForChannel(string $purpose): string
    {
        return in_array($purpose, self::PURPOSES, true) ? $purpose : 'generic';
    }

    public function latestValidToken(string $token): ?VerificationCode
    {
        return $this->codes->findValidToken($token, 'password_reset');
    }
}
