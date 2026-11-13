<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * GET /api/vendors?search=&per_page=15
     * Returns paginated active vendors with partial address match.
     * Uses parameter binding + LIKE wildcard escaping to prevent
     * SQL/wildcard injection (see notes below).
     */
    public function index(Request $request)
    {
        $request->validate([
            'search'   => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Vendor::query()
            ->where('is_active', 1)
            ->with(['logo', 'background', 'owner']);

        if ($search = $request->input('search')) {
            // Escape LIKE wildcards % _ and \ so user input cannot
            // manipulate the LIKE pattern. Query builder still binds
            // the value as a parameter, so no SQL injection.
            $escaped = addcslashes($search, '%_\\');
            $query->where('address', 'like', "%{$escaped}%");
        }

        \App\Support\CatalogCityFilter::apply($query, \App\Support\CatalogCityFilter::selected($request), true);
        $catalog = app(\App\Support\CityCatalog::class);
        $perPage = $request->input('per_page', 15);
        $vendors = $query->latest('id')->paginate($perPage);

        // Transform to include required fields: logoPath, name, address, phone, rating
        // Keep pagination meta intact.
        $vendors->getCollection()->transform(fn (Vendor $vendor) => $this->vendorPayload($vendor, $catalog));

        return $this->json('vendor list', [
            'vendors' => $vendors,
        ]);
    }

    /**
     * GET /api/vendors/{id}
     * Returns single active vendor with its active services.
     */
    public function show($id)
    {
        $vendor = Vendor::with([
                'logo',
                'background',
                'owner',
            ])
            ->where('is_active', 1)
            ->findOrFail($id);

        $ratings = \App\Models\Rating::whereHas('order', function ($q) use ($vendor) {
            $q->where('vendor_id', $vendor->id);
        })->pluck('rating');

        $ratingCount = $ratings->count();
        $avgRating = $ratingCount > 0 ? round($ratings->avg(), 1) : 5.0;
        $phone = $vendor->phone ?: ($vendor->owner?->mobile ?: ($vendor->owner?->phone ?: ''));

        return $this->json('vendor details', [
            'vendor' => [
                'id'       => $vendor->id,
                'name'     => $vendor->name,
                'slug'     => $vendor->slug,
                'address'  => $vendor->address,
                'phone'    => $phone,
                'email'    => $vendor->email,
                'logoPath' => $vendor->logoPath,
                'backgroundPath' => $vendor->backgroundPath,
                'rating'   => (float) $avgRating,
                'rating_count' => (int) $ratingCount,
                'services' => ServiceResource::collection($this->servicesForVendor($vendor->id)),
            ]
        ]);
    }

    /**
     * GET /api/vendors/{id}/services
     * Returns services for a vendor where vendor_id = id
     */
    public function services($id)
    {
        $vendor = Vendor::where('is_active', 1)->findOrFail($id);

        $services = $this->servicesForVendor($vendor->id);

        return $this->json('vendor services', [
            'vendor_id' => (int) $vendor->id,
            'services'  => ServiceResource::collection($services),
        ]);
    }

    /**
     * GET /api/services/{service}/vendors
     * Active stores that currently offer the global service through at least
     * one active top-level product. City filtering uses the existing contract.
     */
    public function serviceVendors(Request $request, \App\Models\Service $service)
    {
        abort_unless($service->vendor_id === null && $service->is_active, 404);
        $request->validate(['per_page' => 'nullable|integer|min:1|max:100']);

        $query = Vendor::query()
            ->where('is_active', 1)
            ->whereHas('products', fn ($products) => $products
                ->whereNull('product_id')
                ->where('is_active', 1)
                ->where('service_id', $service->id))
            ->with(['logo', 'background', 'owner']);

        \App\Support\CatalogCityFilter::apply(
            $query,
            \App\Support\CatalogCityFilter::selected($request),
            true
        );

        $catalog = app(\App\Support\CityCatalog::class);
        $vendors = $query->latest('id')->paginate($request->input('per_page', 100));
        $vendors->getCollection()->transform(fn (Vendor $vendor) => $this->vendorPayload($vendor, $catalog));

        return $this->json('service vendors', [
            'service' => new ServiceResource($service->load('thumbnail')),
            'vendors' => $vendors,
        ]);
    }

    private function servicesForVendor(int $vendorId)
    {
        return \App\Models\Service::query()
            ->global()
            ->isActive()
            ->whereHas('products', fn ($products) => $products
                ->where('vendor_id', $vendorId)
                ->whereNull('product_id')
                ->where('is_active', 1))
            ->with('thumbnail')
            ->orderBy('name')
            ->get();
    }

    private function vendorPayload(Vendor $vendor, $catalog): array
    {
        $ratings = \App\Models\Rating::whereHas('order', fn ($q) =>
            $q->where('vendor_id', $vendor->id)
        )->pluck('rating');
        $ratingCount = $ratings->count();
        $avgRating = $ratingCount > 0 ? round($ratings->avg(), 1) : 5.0;
        $phone = $vendor->phone ?: ($vendor->owner?->mobile ?: ($vendor->owner?->phone ?: ''));

        return [
            'id' => $vendor->id,
            'name' => $vendor->name,
            'slug' => $vendor->slug,
            'address' => $vendor->address,
            'city' => $vendor->city,
            'city_id' => $catalog->forName($vendor->city)['id'] ?? null,
            'latitude' => $vendor->latitude === null ? null : (float) $vendor->latitude,
            'longitude' => $vendor->longitude === null ? null : (float) $vendor->longitude,
            'phone' => $phone,
            'email' => $vendor->email,
            'is_active' => $vendor->is_active,
            'show_in_partners' => (bool) $vendor->show_in_partners,
            'logoPath' => $vendor->logoPath,
            'backgroundPath' => $vendor->backgroundPath,
            'rating' => (float) $avgRating,
            'rating_count' => (int) $ratingCount,
        ];
    }
}
