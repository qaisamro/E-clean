<?php


namespace App\Repositories;

use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Traits\VendorScoped;

class CouponRepository extends Repository
{
    use VendorScoped;

    public function model()
    {
        return Coupon::class;
    }

    public function getAll()
    {
        $coupons = $this->model()::latest('id');
        return $this->scopeVendor($coupons)->get();
    }

    public function storeByRequest(CouponRequest $request): Coupon
    {
        return $this->model()::create($this->couponAttributes($request, $this->currentVendorId()));
    }

    /**
     * Super Admin coupon: vendor_id stays null so the discount applies in every
     * store, for every customer, across the whole app.
     */
    public function storeGlobalByRequest(CouponRequest $request): Coupon
    {
        return $this->model()::create($this->couponAttributes($request, null));
    }

    protected function couponAttributes(CouponRequest $request, ?int $vendorId): array
    {
        $startedAt = $request->start_date . ' ' . $request->start_time . ":00";
        $expiredAt = $request->expired_date . ' ' . $request->expired_time . ":00";

        return [
            'code' => $request->code,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount,
            'started_at' =>  $startedAt,
            'min_amount' => $request->min_amount,
            'expired_at' => $expiredAt,
            'vendor_id' => $vendorId,
        ];
    }

    public function updateByRequest(CouponRequest $request,  Coupon $coupon): Coupon
    {
        $startedAt = $request->start_date . ' ' . $request->start_time . ":00";
        $expiredAt = $request->expired_date . ' ' . $request->expired_time . ":00";

        $coupon->update([
            'code' => $request->code,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount,
            'started_at' =>  $startedAt,
            'min_amount' => $request->min_amount,
            'expired_at' => $expiredAt,
        ]);
        return $coupon;
    }

    /**
     * Find a coupon the way the cart/order system uses it.
     *
     *  - With a store context (vendorId): the store's own coupon wins,
     *    then the global coupon.
     *  - Anonymous / multi-vendor cart: a global coupon with the same code
     *    is ALWAYS preferred and evaluated on its own min_amount — this is
     *    the case the super admin expects their global coupon to enforce.
     *  - No global with that code: the store-scoped coupon applies if its
     *    own min_amount is met (store coupons still work anywhere their
     *    code is given, just like before).
     */
    public function findByCoupon($coupon, $amount, $vendorId = null)
    {
        $base = $this->model()::where('code', $coupon);
        if ($vendorId) {
            $vendorCoupon = (clone $base)->where('vendor_id', $vendorId)->isValid($amount)->first();
            if ($vendorCoupon) {
                return $vendorCoupon;
            }
            return (clone $base)->whereNull('vendor_id')->isValid($amount)->first();
        }
        $globalCoupon = (clone $base)->whereNull('vendor_id')->first();
        if ($globalCoupon) {
            return (clone $base)->where('id', $globalCoupon->id)->isValid($amount)->first();
        }
        // Store-scoped coupon with no global twin: keep working, with its min enforced.
        return (clone $base)->isValid($amount)->first();
    }

    public function findById($id)
    {
        return $this->model()::find($id);
    }

    public function deleteByRequest(Coupon $coupon): bool
    {
        return (bool) $coupon->delete();
    }
}
