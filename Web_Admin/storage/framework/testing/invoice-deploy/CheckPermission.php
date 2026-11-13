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

        // Root and administrator accounts may access the Web_Admin console.
        if (
            $user->hasRole('root')
            || $user->hasRole('admin')
            || ($user->hasRole('Admin') && $user->can('root'))
        ) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if (!$routeName) {
            throw new AccessDeniedHttpException();
        }

        // These roles may only enter their own dashboard landing page, which
        // immediately redirects to their role-specific area.
        if (
            $routeName === 'root'
            && ($user->hasRole('driver') || $user->hasRole('vendor_admin'))
        ) {
            return $next($request);
        }

        if ($user->hasRole('driver')) {
            if (in_array($routeName, [
                'driver.account',
                'driver.order.status.change',
            ], true)) {
                return $next($request);
            }
            throw new AccessDeniedHttpException();
        }

        if ($user->hasRole('vendor_admin')) {
            if (in_array($routeName, [
                'vendor.dashboard',
                'vendor.settings',
                'vendor.settings.update',
                'vendor.customers.index',
                'vendor.notifications.index',
                'vendor.notifications.send',
            ], true) || $user->can($routeName)) {
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
