<?php

namespace App\Support;

/** WGS84 GeoJSON [longitude, latitude]; holes and MultiPolygon are respected. */
class GeoPolygon
{
    public static function containsWithAccuracy(array $geometry, float $lat, float $lng, float $metres): bool
    {
        if (!self::contains($geometry, $lat, $lng)) return false;
        if ($metres <= 0) return true;
        $polygons = $geometry['type'] === 'Polygon' ? [$geometry['coordinates']] : $geometry['coordinates'];
        $scaleX = 111320 * cos(deg2rad($lat));
        // Require clearance from every segment, including tiny holes between samples.
        foreach ($polygons as $polygon) {
            if (!is_array($polygon)) return false;
            foreach ($polygon as $ring) {
            if (!is_array($ring) || !array_is_list($ring)) return false;
            for ($i = 1; $i < count($ring); $i++) {
                if (!is_array($ring[$i]) || !is_array($ring[$i-1])) return false;
                $a = $ring[$i-1]; $b = $ring[$i];
                if (!array_is_list($a) || !array_is_list($b) || count($a) < 2 || count($b) < 2) return false;
                foreach ([$a[0], $a[1], $b[0], $b[1]] as $value) {
                    if (!is_numeric($value) || !is_finite((float) $value)) return false;
                }
                $ax = ($a[0] - $lng) * $scaleX; $ay = ($a[1] - $lat) * 111320;
                $bx = ($b[0] - $lng) * $scaleX; $by = ($b[1] - $lat) * 111320;
                $dx = $bx - $ax; $dy = $by - $ay;
                $length = $dx * $dx + $dy * $dy;
                $t = $length > 0 ? max(0, min(1, -($ax * $dx + $ay * $dy) / $length)) : 0;
                // Small margin for the local WGS84-to-metres approximation.
                if (hypot($ax + $t * $dx, $ay + $t * $dy) <= $metres * 1.02) return false;
            }
            }
        }
        return true;
    }

    public static function contains(array $geometry, float $lat, float $lng): bool
    {
        $polygons = match ($geometry['type'] ?? null) {
            'Polygon' => [$geometry['coordinates'] ?? []],
            'MultiPolygon' => $geometry['coordinates'] ?? [],
            default => [],
        };
        if (!is_array($polygons) || !array_is_list($polygons) || !is_finite($lat) || !is_finite($lng) || abs($lat) > 90 || abs($lng) > 180) return false;
        foreach ($polygons as $polygon) {
            if (!is_array($polygon) || !$polygon || !is_array($polygon[0] ?? null) || self::ring($polygon[0], $lat, $lng) !== true) continue;
            $hole = false;
            foreach (array_slice($polygon, 1) as $ring) {
                if (!is_array($ring) || self::ring($ring, $lat, $lng) !== false) { $hole = true; break; }
            }
            if (!$hole) return true;
        }
        return false;
    }

    private static function ring(array $ring, float $lat, float $lng): ?bool
    {
        if (!array_is_list($ring) || count($ring) < 4 || count($ring) > 50000) return null;
        if ($ring[0] != $ring[count($ring)-1]) return null;
        $inside = false;
        for ($i = 0, $j = count($ring) - 1; $i < count($ring); $j = $i++) {
            if (!is_array($ring[$i]) || !is_array($ring[$j]) || !array_is_list($ring[$i]) || !array_is_list($ring[$j]) || count($ring[$i]) < 2 || count($ring[$j]) < 2) return null;
            [$x, $y] = $ring[$i]; [$px, $py] = $ring[$j];
            if (!is_numeric($x) || !is_numeric($y) || !is_numeric($px) || !is_numeric($py)) return null;
            if (!is_finite((float) $x) || !is_finite((float) $y) || !is_finite((float) $px) || !is_finite((float) $py)) return null;
            if (abs((float) $x) > 180 || abs((float) $px) > 180 || abs((float) $y) > 90 || abs((float) $py) > 90) return null;
            // Reject points on edges; an administrative border must never silently pick a side.
            $cross = ($lng - $x) * ($py - $y) - ($lat - $y) * ($px - $x);
            if ($x === $px && $y === $py) continue;
            if (abs($cross) < 1e-10 && $lng >= min($x, $px) && $lng <= max($x, $px) && $lat >= min($y, $py) && $lat <= max($y, $py)) return null;
            if (($y > $lat) !== ($py > $lat) && $lng < ($px - $x) * ($lat - $y) / ($py - $y) + $x) $inside = !$inside;
        }
        return $inside;
    }
}
