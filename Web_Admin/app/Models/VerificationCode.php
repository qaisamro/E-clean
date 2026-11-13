<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'attempts' => 'integer',
        'expires_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * The plaintext OTP is kept only in memory for the current delivery.
     * It is never persisted or serialized.
     */
    public ?string $plainOtp = null;

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
