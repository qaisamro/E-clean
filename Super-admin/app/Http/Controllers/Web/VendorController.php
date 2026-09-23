<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with(['owner','logo'])->latest()->get();
        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|min:6|confirmed',
            'logo' => 'nullable|image|max:2048',
        ]);

        // Create owner user - login via phone only, email auto-generated
        $generatedEmail = $request->phone . '@vendor.local';
        $user = User::create([
            'first_name' => $request->name,
            'last_name' => 'Vendor',
            'email' => $generatedEmail,
            'mobile' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => 1,
            'mobile_verified_at' => now(),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('vendor_admin');

        // Handle logo
        $logoMediaId = null;
        if ($request->hasFile('logo')) {
            $media = (new \App\Repositories\MediaRepository())->storeByRequest($request->file('logo'), 'images/vendors/', 'vendor logo', 'image');
            $logoMediaId = $media->id;
        }

        $vendor = Vendor::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $generatedEmail,
            'user_id' => $user->id,
            'logo_media_id' => $logoMediaId,
            'is_active' => true,
        ]);

        return redirect()->route('vendors.index')->with('success', 'تم إنشاء المتجر بنجاح - الدخول برقم الجوال: ' . $request->phone);
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20|unique:users,mobile,' . $vendor->user_id,
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $media = (new \App\Repositories\MediaRepository())->storeByRequest($request->file('logo'), 'images/vendors/', 'vendor logo', 'image');
            $vendor->logo_media_id = $media->id;
        }

        $generatedEmail = $request->phone . '@vendor.local';
        $vendor->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $generatedEmail,
        ]);

        if ($vendor->owner) {
            $vendor->owner->update([
                'email' => $generatedEmail,
                'mobile' => $request->phone,
                'first_name' => $request->name,
            ]);
            if ($request->filled('password')) {
                $request->validate(['password' => 'min:6|confirmed']);
                $vendor->owner->update(['password' => Hash::make($request->password)]);
            }
        }

        return redirect()->route('vendors.index')->with('success', 'تم تحديث المتجر بنجاح');
    }

    public function destroy(Vendor $vendor)
    {
        // Optionally delete owner user? Keep for audit
        $vendor->delete();
        return back()->with('success', 'تم حذف المتجر');
    }

    public function toggleStatus(Vendor $vendor)
    {
        $vendor->update(['is_active' => !$vendor->is_active]);
        return back()->with('success', 'تم تحديث الحالة');
    }

    public function orders(Vendor $vendor)
    {
        $orders = \App\Models\Order::where('vendor_id', $vendor->id)->with(['customer.user','address'])->latest()->get();
        return view('vendors.orders', compact('vendor','orders'));
    }

    public function account(Vendor $vendor)
    {
        $from = request('from');
        $to = request('to');
        $query = \App\Models\Order::where('vendor_id', $vendor->id);
        if ($from && $to) {
            $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }
        $orders = $query->get();
        $stats = [
            'total_orders' => $orders->count(),
            'total_sales' => round($orders->sum('total_amount'), 2),
            'paid_orders' => $orders->where('payment_status', config('enums.payment_status.paid'))->count(),
            'unpaid_orders' => $orders->whereIn('payment_status', [config('enums.payment_status.pending'), config('enums.payment_status.unpaid')])->count(),
            'total_revenue' => round($orders->where('payment_status', config('enums.payment_status.paid'))->sum('total_amount'), 2),
        ];
        return view('vendors.account', compact('vendor','orders','stats'));
    }
}
