<?php

namespace App\Traits;

trait VendorScoped
{
    protected function currentVendorId(): ?int
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }
        if ($user->hasRole('vendor_admin') || $user->hasRole('vendor_sub')) {
            $vendorId = \App\Support\VendorAccount::idFor($user);
            abort_unless($vendorId, 403);
            return (int) $vendorId;
        }
        return null;
    }

    protected function scopeVendor($query)
    {
        $user = auth()->user();
        if (\App\Support\VendorAccount::isStoreStaff($user)) {
            $vendorId = \App\Support\VendorAccount::idFor($user);
            return $vendorId
                ? $query->where('vendor_id', $vendorId)
                : $query->whereRaw('1 = 0');
        }
        return $query;
    }
}
