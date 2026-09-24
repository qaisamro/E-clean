<?php

namespace App\Http\Controllers\Web\Vendors;

use App\Http\Controllers\Controller;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('owner')->latest('id')->get();

        return view('vendors.index', compact('vendors'));
    }

    public function toggleActivationStatus(Vendor $vendor)
    {
        $vendor->update(['is_active' => !$vendor->is_active]);

        return back()->with('success', 'Store visibility updated successfully.');
    }
}