<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemService extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'item_service';

    protected $fillable = [
        'id',
        'service_id',
        'item_id',
        'price',
        'note',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    ########################## Relations ##########################

    public function item(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'item_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
