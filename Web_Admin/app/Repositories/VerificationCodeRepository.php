<?php

namespace App\Repositories;

use App\Models\VerificationCode;
use App\Services\IntegrationSettingsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VerificationCodeRepository extends Repository
{
    public function model()
    {
        return VerificationCode::class;
    }

    public function findOrCreateByContact($contact, string $purpose = 'generic', ?int $ttl = null, ?int $length = null): VerificationCode
    {
        $settings = app(IntegrationSettingsService::class);
        $ttl ??= max(30, (int) $settings->get('otp.ttl', 300));
        $length ??= (int) $settings->get('otp.length', 6);

        [$plainOtp, $otpHash] = $this->generateOtp(in_array($length, [4, 6, 8], true) ? $length : 6);

        $record = $this->model()::updateOrCreate(
            ['contact' => $contact, 'purpose' => $purpose],
            [
                'otp' => $otpHash,
                'token' => $this->generateUniqueToken(),
                'attempts' => 0,
                'expires_at' => now()->addSeconds($ttl),
                'last_sent_at' => now(),
                'verified_at' => null,
            ]
        );

        // Kept in memory only for the current delivery.
        $record->plainOtp = $plainOtp;

        return $record;
    }

    /**
     * Verify an OTP without consuming it. Attempts are incremented atomically
     * so concurrent requests cannot bypass the maximum-attempts limit.
     */
    public function verify($contact, $otp, string $purpose = 'generic', ?int $maxAttempts = null): ?VerificationCode
    {
        $maxAttempts ??= max(1, (int) app(IntegrationSettingsService::class)->get('otp.max_attempts', 5));

        return DB::transaction(function () use ($contact, $otp, $purpose, $maxAttempts) {
            $record = $this->latestFor($contact, $purpose, lock: true);

            if (!$record || $record->isExpired() || $record->verified_at) {
                return null;
            }

            if ($record->attempts >= $maxAttempts) {
                return null;
            }

            if (!Hash::check((string) $otp, (string) $record->otp)) {
                $record->increment('attempts');

                return null;
            }

            return $record->fresh();
        });
    }

    public function verifyAndConsume($contact, $otp, string $purpose = 'generic', ?int $maxAttempts = null): ?VerificationCode
    {
        $record = $this->verify($contact, $otp, $purpose, $maxAttempts);

        if ($record) {
            $record->delete();
        }

        return $record;
    }

    /**
     * Legacy helper kept for compatibility. It does not delete the record.
     */
    public function checkCode($contact, $otp, string $purpose = 'generic'): ?VerificationCode
    {
        return $this->verify($contact, $otp, $purpose);
    }

    public function markVerified(VerificationCode $record): void
    {
        $record->forceFill([
            'verified_at' => now(),
            'expires_at' => now()->addMinutes(15),
        ])->save();
    }

    public function findValidToken(string $token, string $purpose = 'password_reset'): ?VerificationCode
    {
        $record = $this->model()::where('token', $token)
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();

        if (!$record || !$record->verified_at || $record->isExpired()) {
            return null;
        }

        return $record;
    }

    public function checkByToken($token)
    {
        return $this->model()::where('token', $token)->latest('id')->first();
    }

    public function latestFor(string $contact, string $purpose = 'generic', bool $lock = false): ?VerificationCode
    {
        $query = $this->model()::where('contact', $contact);

        if ($purpose === 'generic') {
            $query->where(function ($q) {
                $q->where('purpose', 'generic')->orWhereNull('purpose');
            });
        } else {
            // Accept legacy generic rows but prefer the exact purpose.
            $query->where(function ($q) use ($purpose) {
                $q->where('purpose', $purpose)->orWhere('purpose', 'generic')->orWhereNull('purpose');
            })->orderByRaw('CASE WHEN purpose = ? THEN 0 ELSE 1 END', [$purpose]);
        }

        $query->latest('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    public function latestSent(string $contact, string $purpose = 'generic'): ?VerificationCode
    {
        return $this->latestFor($contact, $purpose);
    }

    public function cleanupExpired(): int
    {
        return $this->model()::whereNotNull('expires_at')
            ->where('expires_at', '<', now()->subDay())
            ->delete();
    }

    private function generateOtp(int $length): array
    {
        do {
            $otp = '';
            for ($i = 0; $i < $length; $i++) {
                $otp .= (string) random_int(0, 9);
            }
            $hash = Hash::make($otp);
        } while ($this->query()->where('otp', $hash)->exists());

        return [$otp, $hash];
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while ($this->query()->where('token', $token)->exists());

        return $token;
    }
}
