<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_category_id',
        'name',
        'sku',
        'kind',
        'unit',
        'purchase_price',
        'sale_price',
        'current_stock',
        'min_stock_alert',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'min_stock_alert' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        self::saved(fn () => Cache::forget('inventory_shortages_count'));
        self::deleted(fn () => Cache::forget('inventory_shortages_count'));
    }

    ####################### Relations ############################

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'stockable');
    }

    public function orderDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderDetail::class, 'item_id');
    }

    public function orderProducts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'item_service', 'item_id', 'service_id')
            ->as('details')
            ->withPivot(['price', 'note']);
    }

    ####################### Accessors ############################

    public function getIsLowStockAttribute(): bool
    {
        if ($this->kind === 'laundry') {
            return (float) $this->min_stock_alert > 0 && (float) $this->current_stock <= (float) $this->min_stock_alert;
        }
        if ((float) $this->current_stock <= 0) {
            return true;
        }
        return (float) $this->min_stock_alert > 0 && (float) $this->current_stock <= (float) $this->min_stock_alert;
    }

    public function getIsEmptyAttribute(): bool
    {
        return (float) $this->current_stock <= 0;
    }

    public function getStockValueAttribute(): float
    {
        return (float) $this->current_stock * (float) ($this->purchase_price ?? 0);
    }
}
