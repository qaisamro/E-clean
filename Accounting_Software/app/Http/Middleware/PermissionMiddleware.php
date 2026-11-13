<?php

namespace App\Http\Middleware;

use App\Support\PermissionRegistry;
use Closure;
use Illuminate\Http\Request;

/**
 * Gate route access behind a single granular permission key.
 * Super admins bypass; everybody else needs the key in their
 * permissions array. Violations render the 403 page with a friendly toast.
 */
class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        if (! $user->hasPermission($permission)) {
            $label = PermissionRegistry::labels()[$permission] ?? $permission;
            abort(403, 'لا تملك صلاحية: ' . $label);
        }
        return $next($request);
    }
}
