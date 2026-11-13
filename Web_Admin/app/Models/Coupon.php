<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    //========= relationships ==============
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, (new CouponUser())->getTable())
            ->withTimestamps();
    }

    //----------- Scope
    public function scopeIsValid(Builder $builder, $minAmount): Builder
    {
        return $builder->where('min_amount', '<=', $minAmount)
            ->where('expired_at', '>=', now())
            ->where('started_at', '<=', now());
    }

    /**
     * Only "global" coupons (vendor_id IS NULL) — created by a Super Admin and
     * usable in every store. Kept separate from each vendor's own coupons.
     */
    public function scopeGlobal(Builder $builder): Builder
    {
        return $builder->whereNull('vendor_id');
    }

    public function isGlobal(): bool
    {
        return $this->vendor_id === null;
    }

    public static function calculate($total, $coupon)
    {
        if(!$coupon){
            return 0;
        }
        $discount = $coupon->discount;
        if($coupon->discount_type == 'percent'){
            $discount = ($total / 100) * $discount;
        }
        return $discount;
    }
}
