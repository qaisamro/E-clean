<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    protected $table = 'inventory_categories';

    protected $fillable = [
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'inventory_category_id');
    }
}
