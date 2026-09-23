<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Offer extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    //------------ Relations
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    //----------- Attridutes
    public function getThumbnailPathAttribute()
    {
        if ($this->thumbnail && Storage::exists($this->thumbnail->src)) {
            return Storage::url($this->thumbnail->src);
        }
        return asset('images/dummy/dummy-placeholder.png');
    }

    public function getVendorNameAttribute()
    {
        return $this->vendor?->name ?? 'N/A';
    }

    public function getDiscountLabelAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return '%' . rtrim(rtrim(number_format($this->discount_value, 2), '0'), '.');
        }
        return currencyPosition($this->discount_value);
    }

    //----------- Scope
    public function scopeIsActive(Builder $builder, bool $activity = true)
    {
        return $builder->where('is_active', $activity);
    }
}