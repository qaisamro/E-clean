<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Vendor extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function logo()
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    public function background()
    {
        return $this->belongsTo(Media::class, 'background_media_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getLogoPathAttribute()
    {
        if ($this->logo && Storage::exists($this->logo->src)) {
            return Storage::url($this->logo->src);
        }
        return asset('images/dummy/dummy-placeholder.png');
    }

    public function getBackgroundPathAttribute()
    {
        if ($this->background && Storage::disk('public')->exists($this->background->src)) {
            return Storage::disk('public')->url($this->background->src);
        }
        return null;
    }

    public function getIsActiveLabelAttribute()
    {
        return $this->is_active ? 'نشط' : 'غير نشط';
    }
}
