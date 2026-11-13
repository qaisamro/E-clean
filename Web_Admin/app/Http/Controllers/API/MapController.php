<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\GoogleMapsService;
use App\Services\IntegrationSettingsService;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function __construct(
        private GoogleMapsService $maps,
        private IntegrationSettingsService $settings
    ) {
    }

    public function config()
    {
        return response()->json([
            'data' => [
                'enabled' => $this->maps->isEnabled(),
                'provider' => $this->maps->isEnabled() && $this->settings->has('google_maps.server_api_key') ? 'google' : 'openstreetmap',
                'reverse_geocoding' => (string) $this->settings->get('google_maps.reverse_geocoding_enabled', '1') === '1',
                'places' => $this->maps->isEnabled() && (string) $this->settings->get('google_maps.places_enabled', '1') === '1',
                'directions' => $this->maps->isEnabled() && (string) $this->settings->get('google_maps.directions_enabled', '1') === '1',
            ],
        ]);
    }

    public function reverseGeocode(Request $request)
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        return response()->json([
            'data' => $this->maps->reverseGeocode((float) $validated['lat'], (float) $validated['lng']),
        ]);
    }
}
