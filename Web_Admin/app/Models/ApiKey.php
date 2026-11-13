<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key_prefix',
        'key_hash',
        'scopes',
        'expires_at',
        'revoked_at',
        'last_used_at',
        'created_by',
    ];

    protected $hidden = ['key_hash'];

    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->revoked_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function hasScope(string $scope): bool
    {
        $scopes = $this->scopes ?? [];

        if (in_array('*', $scopes, true) || in_array($scope, $scopes, true)) {
            return true;
        }

        foreach ($scopes as $granted) {
            if (str_ends_with($granted, '.*') && str_starts_with($scope, substr($granted, 0, -1))) {
                return true;
            }
        }

        return false;
    }
}
