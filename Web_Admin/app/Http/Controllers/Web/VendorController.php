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
        $query = Vendor::with(['owner','logo'])->latest();
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $vendorId = \App\Support\VendorAccount::idFor(auth()->user());
            $query = $vendorId
                ? $query->whereKey($vendorId)
                : $query->whereRaw('1 = 0');
        }
        $vendors = $query->get();
        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        abort_if(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        abort_if(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'city_other' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|min:6|confirmed',
            'logo' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
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

        $city = $request->city === 'other' ? $request->city_other : $request->city;

        $vendor = Vendor::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'address' => $request->address,
            'city' => $city,
            'phone' => $request->phone,
            'email' => $generatedEmail,
            'user_id' => $user->id,
            'logo_media_id' => $logoMediaId,
            'latitude' => $request->filled('latitude') ? $request->latitude : null,
            'longitude' => $request->filled('longitude') ? $request->longitude : null,
            'is_active' => true,
        ]);

        return redirect()->route('vendors.index')->with('success', 'تم إنشاء المتجر بنجاح - الدخول برقم الجوال: ' . $request->phone);
    }

    public function edit(Vendor $vendor)
    {
        $this->assertVendorScope($vendor);
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $this->assertVendorScope($vendor);
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'city_other' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20|unique:users,mobile,' . $vendor->user_id,
            'logo' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($request->hasFile('logo')) {
            $media = (new \App\Repositories\MediaRepository())->storeByRequest($request->file('logo'), 'images/vendors/', 'vendor logo', 'image');
            $vendor->logo_media_id = $media->id;
        }

        $city = $request->city === 'other' ? $request->city_other : $request->city;

        $generatedEmail = $request->phone . '@vendor.local';
        $vendor->update([
            'name' => $request->name,
            'address' => $request->address,
            'city' => $city,
            'phone' => $request->phone,
            'email' => $generatedEmail,
            'latitude' => $request->filled('latitude') ? $request->latitude : $vendor->latitude,
            'longitude' => $request->filled('longitude') ? $request->longitude : $vendor->longitude,
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
        // Only root/admin manage stores; middleware already enforces this —
        // keep the guard in depth.
        abort_if(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        abort_unless(auth()->user()->hasRole('root') || auth()->user()->hasRole('admin'), 403);
        $this->assertVendorScope($vendor);

        // Deleting a store is only allowed when nothing points at it.
        // Links either hard-block the delete (order_appointments is RESTRICT)
        // or silently orphan data (orders/products/... are SET NULL and would
        // lose the store attribution). Show exactly what blocks it and
        // suggest the deactivate path instead of crashing or destroying data.
        $linked = $this->linkedDataCounts($vendor);

        if (array_sum($linked) > 0) {
            $parts = [];
            foreach ($linked as $label => $count) {
                if ($count > 0) {
                    $parts[] = $label . ' (' . $count . ')';
                }
            }

            return back()->with(
                'error',
                'لا يمكن حذف هذا المتجر لوجود بيانات مرتبطة به: ' . implode('، ', $parts)
                . '. استخدم زر «تفعيل/إيقاف» بجانب المتجر لإيقافه من التطبيق مع الاحتفاظ بسجل الطلبات.'
            );
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(fn () => $vendor->delete());
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with(
                'error',
                'تعذر حذف المتجر لوجود بيانات مرتبطة به. استخدم زر «تفعيل/إيقاف» بدلاً من الحذف.'
            );
        }

        return redirect()->route('vendors.index')->with('success', 'تم حذف المتجر');
    }

    /**
     * Rows referencing this vendor that must not disappear blindly: every
     * table whose FK is SET NULL would lose its store attribution (orders,
     * products, drivers, ...), and order_appointments is RESTRICT. CASCADE
     * tables (vendor_appointment_settings, vendor_invoice_templates,
     * vendor_notification_logs, store_customers) are designed by the schema
     * author to vanish together with the vendor, so they are intentionally
     * not counted here.
     */
    private function linkedDataCounts(Vendor $vendor): array
    {
        $id = (int) $vendor->id;

        return [
            'طلبات' => \Illuminate\Support\Facades\DB::table('orders')->where('vendor_id', $id)->count(),
            'مواعيد الطلبات' => \Illuminate\Support\Facades\DB::table('order_appointments')->where('vendor_id', $id)->count(),
            'منتجات' => \Illuminate\Support\Facades\DB::table('products')->where('vendor_id', $id)->count(),
            'خدمات' => \Illuminate\Support\Facades\DB::table('services')->where('vendor_id', $id)->count(),
            'سائقون' => \Illuminate\Support\Facades\DB::table('drivers')->where('vendor_id', $id)->count(),
            'كوبونات' => \Illuminate\Support\Facades\DB::table('coupons')->where('vendor_id', $id)->count(),
            'عروض' => \Illuminate\Support\Facades\DB::table('offers')->where('vendor_id', $id)->count(),
            'بنرات' => \Illuminate\Support\Facades\DB::table('banners')->where('vendor_id', $id)->count(),
            'إشعارات' => \Illuminate\Support\Facades\DB::table('notifications')->where('vendor_id', $id)->count(),
        ];
    }

    public function toggleStatus(Vendor $vendor)
    {
        $this->assertVendorScope($vendor);
        $vendor->update(['is_active' => !$vendor->is_active]);
        return back()->with('success', 'تم تحديث الحالة');
    }

    /**
     * إظهار/إخفاء المتجر من قسم «شركاؤنا الموثوقون» في التطبيق.
     * السوبر أدمن فقط — حسابات المتجر نفسها لا تستطيع تغيير الظهور.
     */
    public function togglePartner(Vendor $vendor)
    {
        abort_if(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        $vendor->update(['show_in_partners' => !$vendor->show_in_partners]);
        return back()->with('success', 'تم تحديث ظهور المتجر في قسم «شركاؤنا الموثوقون»');
    }

    public function settings()
    {
        $vendor = \App\Support\VendorAccount::vendorFor(auth()->user());
        abort_unless($vendor, 403);
        return view('vendor.settings', compact('vendor'));
    }

    /**
     * Separate standalone dashboard for each store owner (and child accounts).
     * Shows only this store's stats and recent orders.
     */
    public function dashboard()
    {
        if (!\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            return redirect()->route('root');
        }

        $vendor = \App\Support\VendorAccount::vendorFor(auth()->user());
        abort_unless($vendor, 403);

        $base = \App\Models\Order::where('vendor_id', $vendor->id);
        $validOrderIds = \App\Support\OrderVendorOwnership::filterConsistentOrders(
            (clone $base)->with('products.service')->get()
        )->pluck('id');
        $base->whereIn('id', $validOrderIds);
        $paid = config('enums.payment_status.paid');
        // NOTE: counts use config('enums.order_status.*') values because
        // those are what is actually stored (e.g. 'Picked your order').
        $st = config('enums.order_status');

        $allOrders = (clone $base)->with(['customer.user'])->latest()->get();
        $paidOrders = $allOrders->where('payment_status', $paid)->values();
        $pendingOrders = $allOrders->where('order_status', $st['pending'])->values();
        $deliveredOrders = $allOrders->where('order_status', $st['delivered'])->values();
        $inProgressOrders = $allOrders->whereIn('order_status', [
            $st['picked_order'],
            $st['processing'],
            $st['on_the_way'],
        ])->values();
        $vendorServices = \App\Models\Service::where('vendor_id', $vendor->id)->latest()->get();
        $vendorProducts = \App\Models\Product::with('service')->where('vendor_id', $vendor->id)->latest()->get();
        $vendorOffers = \App\Models\Offer::with('product')->where('vendor_id', $vendor->id)->latest()->get();

        $stats = [
            'total_orders' => $allOrders->count(),
            'pending' => $pendingOrders->count(),
            'confirmed' => $allOrders->where('order_status', $st['order_confirmed'])->count(),
            'in_progress' => $inProgressOrders->count(),
            'delivered' => $deliveredOrders->count(),
            'ready_for_pickup' => $allOrders->where('order_status', $st['ready_for_pickup'])->count(),
            'on_the_way' => $allOrders->where('order_status', $st['on_the_way'])->count(),
            'cancelled' => $allOrders->where('order_status', $st['cancelled'])->count(),
            'revenue' => round($paidOrders->sum('total_amount'), 2),
            'services' => $vendorServices->count(),
            'products' => $vendorProducts->count(),
            'offers' => $vendorOffers->count(),
        ];

        $recentOrders = $allOrders->take(8);

        return view('vendor.dashboard', compact(
            'vendor',
            'stats',
            'recentOrders',
            'paidOrders',
            'allOrders',
            'pendingOrders',
            'deliveredOrders',
            'inProgressOrders',
            'vendorServices',
            'vendorProducts',
            'vendorOffers'
        ));
    }

    public function updateSettings(Request $request)
    {
        $vendor = \App\Support\VendorAccount::vendorFor(auth()->user());
        abort_unless($vendor, 403);
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'required|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'background' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_logo' => 'sometimes|boolean',
            'remove_background' => 'sometimes|boolean',
        ]);
        $vendor->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'latitude' => $request->filled('latitude') ? $request->latitude : $vendor->latitude,
            'longitude' => $request->filled('longitude') ? $request->longitude : $vendor->longitude,
        ]);

        $mediaRepo = new \App\Repositories\MediaRepository();

        $swap = function ($fileKey, $removeKey, $relation, $fk, $description) use ($request, $vendor, $mediaRepo) {
            $current = $vendor->{$relation};
            if ($request->hasFile($fileKey)) {
                $media = $mediaRepo->storeByRequest($request->file($fileKey), 'images/vendors/', $description, 'image');
                $vendor->{$fk} = $media->id;
                if ($current) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($current->src);
                    $current->delete();
                }
            } elseif ($request->boolean($removeKey) && $current) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($current->src);
                $current->delete();
                $vendor->{$fk} = null;
            }
        };
        $swap('logo', 'remove_logo', 'logo', 'logo_media_id', 'vendor logo');
        $swap('background', 'remove_background', 'background', 'background_media_id', 'vendor background');

        $vendor->save();

        if ($vendor->owner) {
            $vendor->owner->update([
                'mobile' => $request->phone,
                'first_name' => $request->name,
            ]);
        }
        return redirect()->route('vendor.settings')->with('success', 'تم تحديث إعدادات المتجر بنجاح');
    }

    protected function assertVendorScope(Vendor $vendor): void
    {
        $user = auth()->user();
        if (\App\Support\VendorAccount::isStoreStaff($user)) {
            $vendorId = \App\Support\VendorAccount::idFor($user);
            abort_unless($vendorId && (int) $vendor->id === (int) $vendorId, 404);
        }
    }
}
