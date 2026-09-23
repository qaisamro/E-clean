<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public $serviceRepo;
    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepo = $serviceRepository;
    }

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $variant = $request->input('variant', 'all');
        $sort = $request->input('sort', 'recommended');

        $services = $this->serviceRepo->getActive(true, 6, $search, null, $sort);

        // Filter services by variant if selected
        if ($variant !== 'all') {
            $services = $services->filter(function ($service) use ($variant) {
                return $service->variants()->where('name', $variant)->exists();
            });
        }

        // Get all unique variants from database
        $variants = \App\Models\Variant::distinct()->pluck('name')->toArray();

        if (empty($variants)) {
            $variants = [];
        }

        return view('website.pages.services', compact('services', 'variants', 'search', 'variant', 'sort'));
    }


    public function show()
    {
        return view('website.pages.service-details');
    }

    public function vendorStore($vendorId)
    {
        $vendor = \App\Models\Vendor::where('is_active', true)->findOrFail($vendorId);
        $services = \App\Models\Service::where('vendor_id', $vendorId)->where('is_active', true)->with('lowestProduct')->get();
        return view('website.pages.vendor_store', compact('vendor', 'services'));
    }
}
