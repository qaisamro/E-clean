<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ScopeVendor
{
    public function handle(Request $request, Closure $next)
    {
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            $vendorId = \App\Support\VendorAccount::idFor(auth()->user());
            if (!$vendorId) {
                abort(403, 'حساب التاجر غير مرتبط بمتجر');
            }
            // Share vendor_id for repositories
            $request->merge(['_vendor_id' => $vendorId]);
            view()->share('_vendor_id', $vendorId);
        }
        return $next($request);
    }
}
