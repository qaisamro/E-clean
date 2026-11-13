<?php

namespace App\Http\Controllers\Web\Root;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Repositories\CouponRepository;

/**
 * Super Admin coupon management — GLOBAL coupons only.
 *
 * Completely separate from the vendor screens (/coupons), which only ever see
 * and create coupons belonging to that vendor's own store. A coupon created
 * here has vendor_id = null and is therefore accepted for every customer in
 * every store of the app.
 */
class CouponAdminController extends Controller
{
    public function __construct(
        private CouponRepository $repo
    ) {
    }

    /**
     * Only root and admin may manage global coupons.
     */
    protected function authorizeSuperAdmin(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->hasAnyRole(['root', 'admin']),
            403
        );
    }

    public function index()
    {
        $this->authorizeSuperAdmin();

        // Global coupons only — never a vendor's private coupon.
        $coupons = $this->repo->model()::global()->latest('id')->get();

        return view('coupon.admin.index', compact('coupons'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();

        return view('coupon.admin.create');
    }

    public function store(CouponRequest $request)
    {
        $this->authorizeSuperAdmin();

        $coupon = $this->repo->storeGlobalByRequest($request);

        $notification = null;
        if ($request->notify) {
            $notification = \App\Support\CouponNotifier::notify($coupon);
        }

        return redirect()->route('coupon.admin.index')
            ->with('success', 'تمت إضافة الكوبون العام بنجاح.')
            ->with('coupon_notification', $notification);
    }

    public function edit(Coupon $coupon)
    {
        $this->authorizeSuperAdmin();

        // A Super Admin only ever edits global coupons.
        abort_unless($coupon->isGlobal(), 404);

        return view('coupon.admin.edit', compact('coupon'));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $this->authorizeSuperAdmin();
        abort_unless($coupon->isGlobal(), 404);

        $this->repo->updateByRequest($request, $coupon);

        $notification = null;
        if ($request->notify) {
            $notification = \App\Support\CouponNotifier::notify($coupon);
        }

        return redirect()->route('coupon.admin.index')
            ->with('success', 'تم تحديث الكوبون العام بنجاح.')
            ->with('coupon_notification', $notification);
    }

    public function destroy(Coupon $coupon)
    {
        $this->authorizeSuperAdmin();
        abort_unless($coupon->isGlobal(), 404);

        $this->repo->deleteByRequest($coupon);

        return back()->with('success', 'تم حذف الكوبون العام بنجاح.');
    }

    /**
     * Notify every registered customer about the new GLOBAL coupon.
     *
     * A global coupon belongs to no single store, so the notification says
     * explicitly that it can be used in every store.
     *
     * Returns a small report the UI can display.
     */
    protected function notifyCustomers(Coupon $coupon): array
    {
        return \App\Support\CouponNotifier::notify($coupon);
    }
}
