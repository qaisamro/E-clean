<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class StoreOrderAccess
{
    /**
     * Protect order reads, including invoice and label generation, from
     * cross-store access. Drivers may read current assignments and history.
     */
    public static function assertCanView(Order $order): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        // Store staff (owner or child account) are limited to their own store.
        if ($user->hasRole('vendor_admin') || $user->hasRole('vendor_sub')) {
            $vendorId = \App\Support\VendorAccount::idFor($user);
            abort_unless($vendorId !== null, 404);
            abort_unless((int) $order->vendor_id === (int) $vendorId, 404);
            abort_unless(OrderVendorOwnership::productsMatchOrder($order), 404);
            return;
        }

        if ($user->hasRole('driver')) {
            $driver = $user->driver;
            $eligible = $driver
                && $driver->vendor_id
                && $driver->is_approve
                && $user->is_active
                && (int) $driver->user_id === (int) $user->id;
            // Approved drivers of a store may view that store's orders,
            // like the store owner does — POS sales screens rely on it.
            $sameStore = $eligible
                && (int) $order->vendor_id === (int) $driver->vendor_id;
            abort_unless($sameStore && OrderVendorOwnership::productsMatchOrder($order), 404);
            return;
        }

        abort_unless($user->hasRole('root') || $user->hasRole('admin'), 403);
    }

    /**
     * Mutations by drivers require a current assignment; a history record is
     * never sufficient to change an order.
     */
    public static function assertCanMutate(Order $order): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        // Store staff (owner or child account) are limited to their own store.
        if ($user->hasRole('vendor_admin') || $user->hasRole('vendor_sub')) {
            $vendorId = \App\Support\VendorAccount::idFor($user);
            abort_unless($vendorId !== null, 404);
            abort_unless((int) $order->vendor_id === (int) $vendorId, 404);
            abort_unless(OrderVendorOwnership::productsMatchOrder($order), 404);
            return;
        }

        if ($user->hasRole('driver')) {
            $driver = $user->driver;
            $assigned = $driver
                && $driver->vendor_id
                && $driver->is_approve
                && $user->is_active
                && (int) $driver->user_id === (int) $user->id
                && (int) $order->vendor_id === (int) $driver->vendor_id
                && $driver->orders()->whereKey($order->id)->exists()
                && OrderVendorOwnership::productsMatchOrder($order);
            abort_unless($assigned, 404);
            return;
        }

        abort_unless($user->hasRole('root') || $user->hasRole('admin'), 403);
    }
}
