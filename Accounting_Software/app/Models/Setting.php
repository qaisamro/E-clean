<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'name',
        'key',
        'value',
        'img'
    ];

    protected static function booted()
    {
        self::saved(function (Setting $setting) {
            Cache::forget('settings.' . $setting->key);
        });
    }

    public static function cachedSettingByKey($key)
    {
        return Cache::rememberForever('settings.' . $key, fn() => self::firstWhere('key', $key));
    }

    public function getImgUrlAttribute()
    {
        if ($this->img) {
            if (Storage::disk('public')->exists($this->img)) {
                return Storage::disk('public')->url($this->img);
            }
            return '';
        }
        return $this->img;
    }
}
