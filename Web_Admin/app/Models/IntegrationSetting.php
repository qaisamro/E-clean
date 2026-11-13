<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationSetting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'is_secret', 'type', 'description'];

    protected $casts = [
        'is_secret' => 'boolean',
    ];

    public static function getValue(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        if (!$row) {
            return $default;
        }

        if (!$row->is_secret) {
            return $row->value;
        }

        if ($row->value === null || $row->value === '') {
            return $default;
        }

        try {
            return decrypt($row->value);
        } catch (\Throwable) {
            // A rotated APP_KEY must not make the whole integration center
            // unusable. Treat unreadable secrets as not configured.
            return $default;
        }
    }

    public static function setValue(string $key, $value, array $meta = []): void
    {
        $isSecret = $meta['is_secret'] ?? false;
        static::updateOrCreate(
            ['key' => $key],
            [
                'group' => $meta['group'] ?? 'general',
                'value' => $isSecret ? encrypt($value) : $value,
                'is_secret' => $isSecret,
                'type' => $meta['type'] ?? 'string',
                'description' => $meta['description'] ?? null,
            ]
        );
    }
}