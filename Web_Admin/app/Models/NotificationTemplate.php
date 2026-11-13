<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'key',
        'name',
        'subject',
        'body',
        'provider_template_name',
        'language',
        'variables',
        'active',
    ];

    protected $casts = [
        'variables' => 'array',
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
