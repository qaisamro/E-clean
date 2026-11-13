<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationWebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'event_type',
        'external_id',
        'payload',
        'signature_valid',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'signature_valid' => 'boolean',
        'processed_at' => 'datetime',
    ];
}
