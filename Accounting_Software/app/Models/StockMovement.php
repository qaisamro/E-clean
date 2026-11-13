<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stockable_type',
        'stockable_id',
        'type',
        'quantity',
        'balance_after',
        'reference',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'type' => StockMovementType::class,
        'quantity' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    ####################### Relations ############################

    public function stockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
