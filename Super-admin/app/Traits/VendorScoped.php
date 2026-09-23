<?php

namespace App\Traits;

trait VendorScoped
{
    protected function currentVendorId(): ?int
    {
        if (auth()->check() && auth()->user()->hasRole('vendor_admin')) {
            return auth()->user()->vendor?->id;
        }
        return null;
    }

    protected function scopeVendor($query)
    {
        $vendorId = $this->currentVendorId();
        if ($vendorId) {
            return $query->where('vendor_id', $vendorId);
        }
        return $query;
    }
}
