<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleMapsService
{
    public function __construct(
        private IntegrationSettingsService $settings,
        private MessageLogger $logger
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->settings->isEnabled('google_maps');
    }

    public function isConfigured(): bool
    {
        return $this->settings->isConfigured('google_maps');
    }

    public function hasKeyFor(string $platform): bool
    {
        return $this->settings->has("google_maps.{$platform}_api_key");
    }

    public function keyFor(string $platform): string
    {
        return (string) $this->settings->get("google_maps.{$platform}_api_key", '');
    }

    public function reverseGeocode(float $latitude, float $longitude): array
    {
        if ($this->isEnabled() && $this->settings->has('google_maps.server_api_key') && $this->settings->get('google_maps.reverse_geocoding_enabled', '1') === '1') {
            $result = $this->googleRequest('geocode/json', [
                'latlng' => $latitude . ',' . $longitude,
                'language' => app()->getLocale(),
            ]);

            if ($result['status'] === 'ok') {
                return $result;
            }

            if ($this->settings->get('google_maps.fallback_osm', '1') !== '1') {
                return $result;
            }
        }

        return $this->osmReverseGeocode($latitude, $longitude);
    }

    public function geocode(string $address): array
    {
        if (!$this->isEnabled()) {
            return ['status' => 'not_configured', 'message' => 'Google Maps is disabled.', 'address' => null, 'provider' => 'google'];
        }

        if (!$this->settings->has('google_maps.server_api_key')) {
            return ['status' => 'not_configured', 'message' => 'Google Maps server key is not configured.', 'address' => null, 'provider' => 'google'];
        }

        return $this->googleRequest('geocode/json', [
            'address' => $address,
            'language' => app()->getLocale(),
        ]);
    }

    public function test(): array
    {
        if (!$this->isEnabled()) {
            return ['status' => 'skipped', 'message' => 'Google Maps is disabled.', 'address' => null, 'provider' => 'google'];
        }

        if (!$this->settings->has('google_maps.server_api_key')) {
            return ['status' => 'not_configured', 'message' => 'Google Maps server key is not configured.', 'address' => null, 'provider' => 'google'];
        }

        // Test Google directly. Falling back to OpenStreetMap here would let a
        // valid OSM lookup mask an invalid Google key and record a false
        // "Connected" status for Google Maps.
        return $this->googleRequest('geocode/json', [
            'latlng' => '31.9522,35.2332',
            'language' => app()->getLocale(),
        ]);
    }

    public function status(): array
    {
        $status = $this->settings->status('google_maps');
        $status['platform_keys'] = [
            'server' => $this->hasKeyFor('server'),
            'web' => $this->hasKeyFor('web'),
            'android' => $this->hasKeyFor('android'),
            'ios' => $this->hasKeyFor('ios'),
        ];

        return $status;
    }

    private function googleRequest(string $endpoint, array $params): array
    {
        try {
            $response = Http::timeout(15)->get('https://maps.googleapis.com/maps/api/' . $endpoint, array_merge($params, [
                'key' => $this->keyFor('server'),
            ]));

            if (!$response->successful()) {
                return ['status' => 'error', 'message' => 'Google Maps returned HTTP ' . $response->status(), 'address' => null, 'provider' => 'google'];
            }

            $json = $response->json();
            $apiStatus = $json['status'] ?? 'UNKNOWN';

            if ($apiStatus === 'OK') {
                $first = $json['results'][0] ?? [];
                $address = $first['formatted_address']
                    ?? ($first['address_components'][0]['long_name'] ?? null);

                return [
                    'status' => 'ok',
                    'message' => 'OK',
                    'address' => $address,
                    'latitude' => $first['geometry']['location']['lat'] ?? null,
                    'longitude' => $first['geometry']['location']['lng'] ?? null,
                    'provider' => 'google',
                    'raw_status' => $apiStatus,
                ];
            }

            if ($apiStatus === 'ZERO_RESULTS') {
                return ['status' => 'not_found', 'message' => 'No address found for these coordinates.', 'address' => null, 'provider' => 'google'];
            }

            $message = $json['error_message'] ?? ('Google Maps API status: ' . $apiStatus);
            $this->logger->safeLog('warning', 'Google Maps request failed', ['api_status' => $apiStatus]);

            return ['status' => 'error', 'message' => $this->settings->sanitizeMessage($message), 'address' => null, 'provider' => 'google'];
        } catch (\Throwable $e) {
            $this->logger->safeLog('warning', 'Google Maps connection failed', ['exception' => get_class($e)]);

            return ['status' => 'error', 'message' => 'Google Maps connection failed.', 'address' => null, 'provider' => 'google'];
        }
    }

    private function osmReverseGeocode(float $latitude, float $longitude): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => config('app.name') . ' address lookup',
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'accept-language' => app()->getLocale(),
            ]);

            if ($response->successful() && !empty($response->json('display_name'))) {
                return [
                    'status' => 'ok',
                    'message' => 'OK',
                    'address' => $response->json('display_name'),
                    'provider' => 'openstreetmap',
                ];
            }

            return ['status' => 'error', 'message' => 'OpenStreetMap lookup failed.', 'address' => null, 'provider' => 'openstreetmap'];
        } catch (\Throwable $e) {
            $this->logger->safeLog('warning', 'OpenStreetMap lookup failed', ['exception' => get_class($e)]);

            return ['status' => 'error', 'message' => 'OpenStreetMap lookup failed.', 'address' => null, 'provider' => 'openstreetmap'];
        }
    }
}
