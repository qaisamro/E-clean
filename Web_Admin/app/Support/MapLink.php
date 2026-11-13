<?php

namespace App\Support;

use App\Models\Address;

/**
 * Builds a Google Maps URL for an address: exact coordinates when the
 * address carries them, otherwise a text search on the free-form
 * address parts. Returns null when nothing usable is known.
 */
class MapLink
{
    public static function addressUrl(?Address $address): ?string
    {
        if (!$address) {
            return null;
        }

        $lat = $address->latitude;
        $lng = $address->longitude;
        if ($lat !== null && $lng !== null && $lat !== '' && $lng !== '' && is_numeric($lat) && is_numeric($lng)) {
            return 'https://www.google.com/maps/search/?api=1&query=' . $lat . ',' . $lng;
        }

        $parts = array_filter([
            $address->address_line,
            $address->address_name,
            $address->house_no,
            $address->block,
            $address->road_no,
            $address->area,
            $address->address_line2,
        ], fn ($v) => $v !== null && trim((string) $v) !== '');

        $query = trim(implode(' ', $parts));

        return $query === ''
            ? null
            : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($query);
    }
}
