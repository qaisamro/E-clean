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


    public function index(WebsiteSettingsRepository $websiteSettingsRepo)
    {
        $services = $this->serviceRepo->getAll(true);
        $webSettings = $websiteSettingsRepo->index();
        return view('website.layouts.app', compact('webSettings', 'services'));
    }
}
