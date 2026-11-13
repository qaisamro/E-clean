<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_id',
        'service_id',
        'price',
        'quantity',
        'total_price',
        'is_payment_deferred',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    ####################### Relations ############################

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'item_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
