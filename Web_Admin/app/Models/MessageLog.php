<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'provider',
        'template_key',
        'recipient_masked',
        'recipient_hash',
        'status',
        'provider_message_id',
        'error_code',
        'error_message',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
