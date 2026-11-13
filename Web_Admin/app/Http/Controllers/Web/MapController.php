<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function __construct(private GoogleMapsService $maps)
    {
    }

    public function reverseGeocode(Request $request)
    {
        abort_unless(auth()->check(), 403);

        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        return response()->json(
            $this->maps->reverseGeocode((float) $validated['lat'], (float) $validated['lng'])
        );
    }
}
