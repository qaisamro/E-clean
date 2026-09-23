<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Website\WebsiteSetting;
use App\Repositories\ServiceRepository;
use App\Repositories\WebsiteSettingsRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public $serviceRepo;
    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepo = $serviceRepository;
    }


    public function index(Request $request, WebsiteSettingsRepository $websiteSettingsRepo)
    {
        $services = $this->serviceRepo->getAll(true);
        $webSettings = $websiteSettingsRepo->index();
        // Multi-Vendor: fetch vendors with filter by address (partial match)
        $search = $request->input('search');
        $vendorsQuery = \App\Models\Vendor::where('is_active', true);
        if ($search) {
            $vendorsQuery->where('address', 'like', "%{$search}%");
        }
        $vendors = $vendorsQuery->latest()->get();
        return view('website.layouts.app', compact('webSettings', 'services', 'vendors'));
    }
}
