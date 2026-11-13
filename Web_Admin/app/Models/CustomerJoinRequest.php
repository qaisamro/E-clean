<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerJoinRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'request_metadata' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'tracking_token_expires_at' => 'datetime',
        'tracking_token_revoked_at' => 'datetime',
    ];

    protected $hidden = [
        'tracking_token_hash',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'بانتظار الموافقة',
            self::STATUS_APPROVED => 'مقبول',
            self::STATUS_REJECTED => 'مرفوض',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Latest join request for a user, tolerant of a missing table.
     *
     * FAIL-OPEN. Only safe while the approval feature is switched off (or for
     * legacy accounts): if the table is missing there is nothing to check and
     * existing customers must keep working.
     *
     * @see self::latestForUserOrFail() for the approval-protected path.
     */
    public static function safeLatestForUser($userId): ?self
    {
        try {
            return static::where('user_id', $userId)->latest('id')->first();
        } catch (\Throwable $e) {
            if (self::isMissingTableError($e)) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * Latest join request for a user, FAIL-CLOSED.
     *
     * Used whenever the approval feature is enabled: if the store cannot be
     * read, approval status is unverifiable, so callers must deny rather than
     * treat the account as approved.
     *
     * @throws JoinRequestStoreUnavailable
     */
    public static function latestForUserOrFail($userId): ?self
    {
        try {
            return static::where('user_id', $userId)->latest('id')->first();
        } catch (\Throwable $e) {
            if (self::isMissingTableError($e)) {
                throw new \App\Exceptions\JoinRequestStoreUnavailable(previous: $e);
            }

            throw $e;
        }
    }

    /**
     * Create a join request, FAIL-CLOSED.
     *
     * Used for approval-protected registrations: an account must never be
     * created without its approval record, so a missing store aborts the
     * registration instead of silently granting access.
     *
     * @throws JoinRequestStoreUnavailable
     */
    public static function createOrFail(array $attributes): self
    {
        try {
            return static::create($attributes);
        } catch (\Throwable $e) {
            if (self::isMissingTableError($e)) {
                throw new \App\Exceptions\JoinRequestStoreUnavailable(previous: $e);
            }

            throw $e;
        }
    }

    /**
     * Detect "table does not exist" across database drivers
     * (MySQL/MariaDB 1146 / SQLSTATE 42S02, SQLite "no such table").
     */
    public static function isMissingTableError(\Throwable $e): bool
    {
        $errorInfo = property_exists($e, 'errorInfo') ? $e->errorInfo : null;
        $driverCode = is_array($errorInfo) ? ($errorInfo[1] ?? null) : null;

        if ($e->getCode() === '42S02' || $driverCode === 1146) {
            return true;
        }

        $message = strtolower($e->getMessage());

        return str_contains($message, 'no such table')
            || str_contains($message, 'does not exist')
            || str_contains($message, "doesn't exist")
            || str_contains($message, 'base table or view not found');
    }

    /**
     * Generate a new tracking token and store its hash.
     * Returns the plaintext token (shown only once to the client).
     */
    public function generateTrackingToken(int $ttlDays = 30): string
    {
        $plainToken = bin2hex(random_bytes(32)); // 64 hex chars
        $this->update([
            'tracking_token_hash' => hash('sha256', $plainToken),
            'tracking_token_expires_at' => now()->addDays($ttlDays),
        ]);
        return $plainToken;
    }

    /**
     * Verify a tracking token against the stored hash.
     * Also rejects expired and manually revoked tokens.
     */
    public function verifyTrackingToken(string $plainToken): bool
    {
        if (!$this->tracking_token_hash || !$this->tracking_token_expires_at) {
            return false;
        }

        if ($this->tracking_token_expires_at->isPast()) {
            return false;
        }

        if ($this->tracking_token_revoked_at !== null) {
            return false;
        }

        return hash_equals($this->tracking_token_hash, hash('sha256', $plainToken));
    }

    /**
     * Check if tracking token is expired.
     */
    public function isTrackingTokenExpired(): bool
    {
        return $this->tracking_token_expires_at === null || $this->tracking_token_expires_at->isPast();
    }

    /**
     * Check if tracking token was manually revoked.
     */
    public function isTrackingTokenRevoked(): bool
    {
        return $this->tracking_token_revoked_at !== null;
    }

    /**
     * Revoke the current tracking token. Admins can use this to
     * invalidate an already-issued token immediately.
     */
    public function revokeTrackingToken(): void
    {
        $this->forceFill(['tracking_token_revoked_at' => now()])->save();
    }

    /**
     * Find a join request by plaintext tracking token (hash comparison).
     * Returns null for unknown, expired or revoked tokens.
     *
     * @throws JoinRequestStoreUnavailable when the store cannot be read, so a
     *         status is never guessed or half-verified.
     */
    public static function findByTrackingToken(string $plainToken): ?self
    {
        if (strlen($plainToken) < 16 || strlen($plainToken) > 256) {
            return null;
        }

        $hash = hash('sha256', $plainToken);

        try {
            return static::where('tracking_token_hash', $hash)
                ->where('tracking_token_expires_at', '>', now())
                ->whereNull('tracking_token_revoked_at')
                ->latest('id')
                ->first();
        } catch (\Throwable $e) {
            if (self::isMissingTableError($e)) {
                throw new \App\Exceptions\JoinRequestStoreUnavailable(previous: $e);
            }

            throw $e;
        }
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => '#16a34a',
            self::STATUS_REJECTED => '#dc2626',
            default => '#d97706',
        };
    }
}
