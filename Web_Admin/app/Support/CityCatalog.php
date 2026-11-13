<?php

namespace App\Support;

use App\Models\Vendor;

/** Reuses the existing admin city list and explicit vendors.city values. */
class CityCatalog
{
    private ?array $cities = null;

    public static function normalize(string $name): string
    {
        $name = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{0640}]/u', '', trim($name));
        $name = str_replace(['أ', 'إ', 'آ', 'ى'], ['ا', 'ا', 'ا', 'ي'], $name);
        return preg_replace('/\s+/u', ' ', mb_strtolower($name));
    }

    public function all(): array
    {
        if ($this->cities !== null) return $this->cities;
        $cities = [];
        $add = function (string $name, ?string $district = null) use (&$cities) {
            $key = self::normalize($name);
            if ($key === '') return;
            $id = 'city_'.substr(hash('sha256', $key), 0, 20);
            $cities[$id] ??= ['id' => $id, 'name' => trim($name), 'districts' => [], 'aliases' => []];
            $cities[$id]['aliases'] = array_values(array_unique([...$cities[$id]['aliases'], trim($name)]));
            if ($district) $cities[$id]['districts'] = array_values(array_unique([...$cities[$id]['districts'], $district]));
        };
        foreach (config('westbank_cities', []) as $district => $names) {
            foreach ($names as $name) $add($name, $district);
        }
        foreach (Vendor::where('is_active', 1)->whereNotNull('city')->distinct()->pluck('city') as $name) $add($name);
        return $this->cities = array_values($cities);
    }

    public function find(?string $id): ?array
    {
        return collect($this->all())->firstWhere('id', $id);
    }

    public function forName(?string $name): ?array
    {
        if (!$name || trim($name) === '') return null;
        $key = self::normalize($name);
        return $this->find('city_'.substr(hash('sha256', $key), 0, 20));
    }

    public static function publicData(array $city): array
    {
        return array_intersect_key($city, array_flip(['id', 'name', 'districts']));
    }
}
