<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use App\Repositories\ProductRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $vendors = Vendor::query()
            ->active()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->get();

        return $this->json('vendors list', [
            'vendors' => VendorResource::collection($vendors),
        ]);
    }

    public function services(Vendor $vendor)
    {
        abort_unless($vendor->is_active, 404);

        $services = (new ServiceRepository())->getActiveServices($vendor->id);

        return $this->json('service list', [
            'services' => ServiceResource::collection($services),
        ]);
    }

    public function products(Request $request, Vendor $vendor)
    {
        abort_unless($vendor->is_active, 404);

        $products = (new ProductRepository())->getProductsByServiceIdAndVariantId(
            $request->input('service_id'),
            $request->input('variant_id'),
            $request->input('search'),
            $vendor->id
        );

        return $this->json('product list', [
            'products' => ProductResource::collection($products),
        ]);
    }
}