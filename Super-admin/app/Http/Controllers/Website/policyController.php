<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Website\WebsiteSetting;
use Illuminate\Http\Request;

class policyController extends Controller
{
    public function terms()
    {
        $setting = WebsiteSetting::where('key', 'terms_conditions')->first();
        return view('auth.terms-condition', compact('setting'));
    }
    public function privacy()
    {
        $setting = WebsiteSetting::where('key', 'privacy_policy')->first();
        return view('auth.privacy-policy', compact('setting'));
    }
}
