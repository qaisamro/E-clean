<?php

namespace App\Services;

use App\Support\CityCatalog;
use App\Support\GeoPolygon;

/** Versioned PCBS locality polygons; reverse-geocoder Points and names are not evidence.
 * Customer coordinates never leave the application server.
 */
class CityLocationResolver
{
    private ?array $boundaries = null;

    public function __construct(private CityCatalog $catalog) {}

    public function resolve(float $lat, float $lng, float $accuracy = 0): ?array
    {
        if (!is_finite($accuracy) || $accuracy < 0 || $accuracy > 100) return null;
        if ($this->boundaries === null) {
            try {
                $data = json_decode(file_get_contents(config('city_location.boundaries_path')), true, 512, JSON_THROW_ON_ERROR);
                $this->boundaries = ($data['type'] ?? null) === 'FeatureCollection' ? ($data['features'] ?? []) : [];
            } catch (\Throwable) { return null; }
        }
        $matches = [];
        foreach ($this->boundaries as $feature) {
            $box = $feature['bbox'] ?? [];
            if (count($box) !== 4 || $lng < $box[0] || $lat < $box[1] || $lng > $box[2] || $lat > $box[3]) continue;
            $code = $feature['properties']['locality_code'] ?? '';
            if (!is_string($code) || !ctype_digit($code)) return null;
            try {
                $raw = file_get_contents(dirname(config('city_location.boundaries_path')).'/pcbs-localities-2017/'.$code.'.json');
                if (!hash_equals($feature['properties']['geometry_sha256'] ?? '', hash('sha256', $raw))) return null;
                $geometry = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable) { return null; }
            if (!is_array($geometry) || !GeoPolygon::contains($geometry, $lat, $lng)) continue;
            $feature['geometry'] = $geometry;
            // An unconfigured locality or overlapping polygons must not silently choose a city.
            $matches[] = $feature;
        }
        if (count($matches) !== 1) return null;
        $feature = $matches[0];
        $id = $feature['properties']['city_id'] ?? null;
        $city = is_string($id) ? $this->catalog->find($id) : null;
        if (!$city || !GeoPolygon::containsWithAccuracy($feature['geometry'], $lat, $lng, $accuracy)) return null;
        return CityCatalog::publicData($city);
    }
}
