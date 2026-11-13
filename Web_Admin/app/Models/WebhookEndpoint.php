<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebhookEndpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'secret',
        'events',
        'description',
        'active',
        'created_by',
    ];

    protected $hidden = ['secret'];

    protected $casts = [
        'secret' => 'encrypted',
        'events' => 'array',
        'active' => 'boolean',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }
}
