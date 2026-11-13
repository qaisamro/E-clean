<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        $routeName = $request->route()?->getName();

        // Offers management is store-scoped: the store owner or a child
        // account explicitly granted the offer permission may access it.
        if (str_starts_with((string) $routeName, 'offer.')) {
            $allowedOffer = $user->hasRole('vendor_admin')
                || ($user->hasRole('vendor_sub') && $user->can($routeName));
            if (!$allowedOffer) {
                throw new AccessDeniedHttpException();
            }
            return $next($request);
        }

        // POS / Products / Orders management are store staff tools only
        $storeAdminPrefixes = ['pos.', 'product.', 'order.'];
        $isStoreAdminRoute = false;
        foreach ($storeAdminPrefixes as $prefix) {
            if (str_starts_with((string) $routeName, $prefix)) {
                $isStoreAdminRoute = true;
                break;
            }
        }
        if ($isStoreAdminRoute && !($user->hasRole('vendor_admin') || $user->hasRole('driver') || $user->hasRole('vendor_sub'))) {
            throw new AccessDeniedHttpException();
        }

        // Root and administrator accounts may access the Web_Admin console.
        if (
            $user->hasRole('root')
            || $user->hasRole('admin')
            || ($user->hasRole('Admin') && $user->can('root'))
        ) {
            return $next($request);
        }

        if (!$routeName) {
            throw new AccessDeniedHttpException();
        }

        // These roles may only enter their own dashboard landing page, which
        // immediately redirects to their role-specific area.
        if (
            $routeName === 'root'
            && ($user->hasRole('driver') || $user->hasRole('vendor_admin') || $user->hasRole('vendor_sub'))
        ) {
            return $next($request);
        }

        if ($user->hasRole('driver')) {
            if (in_array($routeName, [
                'driver.account',
                'driver.order.status.change',
                'web-notifications.feed',
                'maps.reverse-geocode',
                // View-only access to the order details page. Drivers cannot
                // reach POS, invoice editing, payment confirmation or printing.
                'order.show',
            ], true)) {
                return $next($request);
            }
            throw new AccessDeniedHttpException();
        }

        if ($user->hasRole('vendor_sub')) {
            // Baseline of a child account: the profile self-service area and
            // the live notification feed. The dashboard itself, each stat
            // box and the recent-orders table are granted by the owner —
            // see the "لوحة التحكم والبطاقات" permission group.
            if (in_array($routeName, [
                'profile.index',
                'profile.edit',
                'profile.update',
                'profile.change-password',
                'profile.change-password.form',
                'web-notifications.feed',
                'maps.reverse-geocode',
            ], true)) {
                return $next($request);
            }

            // Sub-account management is reserved for the store owner.
            if (str_starts_with((string) $routeName, 'vendor.subs.')) {
                throw new AccessDeniedHttpException();
            }

            // Permissions from the catalog are pre-created by migration, but
            // a route name that is not in the catalog must simply deny —
            // never hit PermissionDoesNotExist.
            $permission = \Spatie\Permission\Models\Permission::where('name', $routeName)
                ->where('guard_name', 'web')
                ->first();
            if ($permission && $user->hasPermissionTo($permission)) {
                return $next($request);
            }

            throw new AccessDeniedHttpException();
        }

        if ($user->hasRole('vendor_admin')) {
            // Store owners always manage their own child (sub) accounts.
            // The sub branch above keeps child accounts away from these.
            if (str_starts_with((string) $routeName, 'vendor.subs.')) {
                return $next($request);
            }

            if (str_starts_with($routeName, 'service.') || str_starts_with($routeName, 'variant.')) {
                throw new AccessDeniedHttpException();
            }
            if (in_array($routeName, [
                'vendor.dashboard',
                'vendor.settings',
                'vendor.settings.update',
                'vendor.customers.index',
                'vendor.notifications.index',
                'vendor.customers.edit',
                'vendor.customers.update',
                // Full profile area for the store owner (self-scoped pages).
                'profile.index',
                'profile.edit',
                'profile.update',
                'profile.change-password',
                'profile.change-password.form',
                'customer.addresses',
                'vendor.notifications.index',
                'vendor.notifications.send',
                'vendor.invoice-template.edit',
                'vendor.appointments.edit',
                'vendor.appointments.update',
                'vendor.appointments.reschedule',
                'vendor.invoice-template.update',
                'vendor.invoice-template.reset',
                'vendor.invoice-template.preview',
                'new.orders',
                'web-notifications.feed',
                'orderIncomplete.paid',
                'driver.assign',
                'driver.edit',
                'driver.update',
                'driver.destroy',
                'driver.status.toggle',
                // POS success screen: confirm payment on own store's orders.
                // The controller enforces store ownership (canTouchOrder).
                'order.payment-status',
                // POS customer address list (safe: controller enforces store scope).
                'customer.addresses',
            ], true) || $user->can($routeName === 'order.pos-invoice' ? 'order.show' : $routeName)) {
                return $next($request);
            }

            throw new AccessDeniedHttpException();
        }

        // Route aliases are used for the dashboard and its order-count polling.
        $permissionName = match ($routeName) {
            'root' => 'dashboard.overview',
            'new.orders' => 'order.index',
            default => $routeName,
        };

        if ($user->hasRole('visitor') && $user->can($permissionName)) {
            return $next($request);
        }

        throw new AccessDeniedHttpException();
    }
}
