<?php

namespace App\Support;

use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorSub;

/**
 * Central scope resolution for "store staff" accounts:
 *
 *  - vendor_admin (the store owner, vendors.user_id) — full control of his
 *    own store, configured by Super Admin capabilities.
 *  - vendor_sub   (a child account the owner creates) — same store scope,
 *    but every capability is granted explicitly, permission by permission,
 *    directly on the user (the owner picks them in the permission grid).
 */
class VendorAccount
{
    /** @var array<int, VendorSub|null> */
    protected static array $linkCache = [];

    /** The vendor (store) this user belongs to, or null. */
    public static function vendorFor(?User $user): ?Vendor
    {
        if (!$user) {
            return null;
        }

        if ($user->hasRole('vendor_admin')) {
            return $user->vendor;
        }

        if ($user->hasRole('vendor_sub')) {
            return static::subLinkFor($user)?->vendor;
        }

        return null;
    }

    /** The vendor id this user belongs to, or null (cached). */
    public static function idFor(?User $user): ?int
    {
        $vendor = static::vendorFor($user);
        return $vendor ? (int) $vendor->id : null;
    }

    /** Any role that acts on behalf of a store (owner or child account). */
    public static function isStoreStaff(?User $user): bool
    {
        return $user && ($user->hasRole('vendor_admin') || $user->hasRole('vendor_sub'));
    }

    /** The sub-account link row (cached per request). */
    public static function subLinkFor(?User $user): ?VendorSub
    {
        if (!$user) {
            return null;
        }

        if (!array_key_exists($user->id, static::$linkCache)) {
            static::$linkCache[$user->id] = VendorSub::with('vendor')
                ->where('user_id', $user->id)
                ->first();
        }

        return static::$linkCache[$user->id];
    }
}
