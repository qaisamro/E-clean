<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $fillable = ['name', 'description', 'name_bn', 'description_bn', 'thumbnail_id', 'is_active'];

    // ----------Relations
    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function lowestProduct()
    {
        return $this->hasOne(Product::class)
            ->whereNull('product_id')
            ->where('is_active', 1)
            ->orderByRaw('
            CASE
                WHEN discount_price > 0 THEN discount_price
                ELSE price
            END ASC
        ');
    }



    public function variants()
    {
        return $this->belongsToMany(Variant::class, (new ServiceVariant())->getTable())
            ->orderBy('position', 'asc')
            ->withTimestamps();
    }

    public function additionals()
    {
        return $this->belongsToMany(Additional::class, AdditionalService::class);
    }

    // --------- Attributes
    public function getThumbnailPathAttribute()
    {
        if ($this->thumbnail && Storage::exists($this->thumbnail->src)) {
            return Storage::url($this->thumbnail->src);
        }
        return asset('images/dummy/dummy-placeholder.png');
    }

    //---------- Scopes
    public function scopeIsActive(Builder $builder)
    {
        return $builder->where('is_active', true);
    }

    public function scopeSearch(Builder $builder, $search)
    {
        if ($search) {
            return $builder->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        return $builder;
    }

    public function scopeCategory(Builder $builder, $category)
    {
        // Category column doesn't exist in the database
        return $builder;
    }
}
