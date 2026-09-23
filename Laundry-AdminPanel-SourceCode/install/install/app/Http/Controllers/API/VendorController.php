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
            ->with('logo');

        if ($search = $request->input('search')) {
            // Escape LIKE wildcards % _ and \ so user input cannot
            // manipulate the LIKE pattern. Query builder still binds
            // the value as a parameter, so no SQL injection.
            $escaped = addcslashes($search, '%_\\');
            $query->where('address', 'like', "%{$escaped}%");
        }

        $perPage = $request->input('per_page', 15);
        $vendors = $query->latest('id')->paginate($perPage);

        // Transform to include required fields: logoPath, name, address, phone
        // Keep pagination meta intact.
        $vendors->getCollection()->transform(function (Vendor $vendor) {
            return [
                'id'       => $vendor->id,
                'name'     => $vendor->name,
                'slug'     => $vendor->slug,
                'address'  => $vendor->address,
                'phone'    => $vendor->phone,
                'email'    => $vendor->email,
                'is_active'=> $vendor->is_active,
                'logoPath' => $vendor->logoPath, // accessor getLogoPathAttribute
            ];
        });

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
                'services' => function ($q) {
                    $q->where('is_active', 1)->with('thumbnail');
                }
            ])
            ->where('is_active', 1)
            ->findOrFail($id);

        return $this->json('vendor details', [
            'vendor' => [
                'id'       => $vendor->id,
                'name'     => $vendor->name,
                'slug'     => $vendor->slug,
                'address'  => $vendor->address,
                'phone'    => $vendor->phone,
                'email'    => $vendor->email,
                'logoPath' => $vendor->logoPath,
                'services' => ServiceResource::collection($vendor->services),
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

        $services = $vendor->services()
            ->where('is_active', 1)
            ->with('thumbnail')
            ->get();

        return $this->json('vendor services', [
            'vendor_id' => (int) $vendor->id,
            'services'  => ServiceResource::collection($services),
        ]);
    }
}
