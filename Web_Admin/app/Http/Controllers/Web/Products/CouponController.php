<?php

namespace App\Http\Controllers\Web\Products;

use App\Models\Coupon;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Repositories\CouponRepository;

class CouponController extends Controller
{
    /**
     * This controller is the VENDOR interface only.
     *
     * /coupons shows and creates coupons belonging to the signed-in vendor's own
     * store. Global coupons (vendor_id = null) created by a Super Admin under
     * /admin/coupons are never listed or editable here, so the two interfaces
     * stay completely separate.
     */
    protected function assertVendorOnly(): void
    {
        abort_unless(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
    }

    public function index()
    {
        $this->assertVendorOnly();
        $coupons = (new CouponRepository())->getAll();

        return view('coupon.index', compact('coupons'));
    }

    public function create()
    {
        $this->assertVendorOnly();
        return view('coupon.create');
    }

    public function store(CouponRequest $request)
    {
        $this->assertVendorOnly();
        $coupon = (new CouponRepository())->storeByRequest($request);

        $notification = null;
        if ($request->notify) {
            $notification = \App\Support\CouponNotifier::notify($coupon);
        }

        return redirect()->route('coupon.index')
            ->with('success', 'تمت إضافة الكوبون بنجاح.')
            ->with('coupon_notification', $notification);
    }

    protected function assertCouponScope(Coupon $coupon): void
    {
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $vendorId = \App\Support\VendorAccount::idFor(auth()->user());
            abort_unless($vendorId && (int) $coupon->vendor_id === (int) $vendorId, 404);
        }
    }

    public function edit(Coupon $coupon)
    {
        $this->assertVendorOnly();
        $this->assertCouponScope($coupon);
        return view('coupon.edit', compact('coupon'));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $this->assertVendorOnly();
        $this->assertCouponScope($coupon);
        (new CouponRepository())->updateByRequest($request, $coupon);

        $notification = null;
        if ($request->notify) {
            $notification = \App\Support\CouponNotifier::notify($coupon);
        }

        return redirect()->route('coupon.index')
            ->with('success', 'تم تحديث الكوبون بنجاح.')
            ->with('coupon_notification', $notification);
    }

    public function destroy(Coupon $coupon)
    {
        $this->assertVendorOnly();
        $this->assertCouponScope($coupon);
        (new CouponRepository())->deleteByRequest($coupon);

        return back()->with('success', 'تم حذف الكوبون بنجاح.');
    }

    /**
     * Notify every registered customer about this store's coupon.
     *
     * Each customer receives BOTH:
     *  - a push notification (Firebase, all registered devices), AND
     *  - a row in the app's in-app notifications list (visible in the app).
     *
     * The message states WHICH store the coupon belongs to, plus the code,
     * discount and minimum order — so customers know exactly how to use it.
     */
    protected function notifyCustomers(Coupon $coupon): array
    {
        return \App\Support\CouponNotifier::notify($coupon);
    }
}
