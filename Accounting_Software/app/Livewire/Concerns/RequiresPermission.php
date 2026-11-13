<?php

namespace App\Livewire\Concerns;

/**
 * Internal authorization for Livewire public action methods.
 *
 * Route middleware (permission:* / check-status) is NOT re-applied on
 * /livewire/update subsequent requests, so every public method that
 * mutates data must re-check authorization here. Super admins pass via
 * User::hasPermission(); suspended users are always rejected.
 */
trait RequiresPermission
{
    protected function requirePermission(string $permission): void
    {
        $user = auth()->user();

        abort_unless($user, 401);
        abort_if($user->isSuspended(), 403);
        abort_unless($user->hasPermission($permission), 403);
    }

    protected function requireCustomerGuard(): void
    {
        abort_unless(auth('customer')->check(), 403);
    }
}
