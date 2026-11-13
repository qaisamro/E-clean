<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'webhook_endpoint_id',
        'event',
        'delivery_uuid',
        'dedupe_key',
        'payload',
        'signature',
        'status',
        'attempts',
        'response_code',
        'response_body',
        'error',
        'last_attempt_at',
        'delivered_at',
        'next_retry_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'attempts' => 'integer',
        'last_attempt_at' => 'datetime',
        'delivered_at' => 'datetime',
        'next_retry_at' => 'datetime',
    ];

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id');
    }
}
