<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class InstallerRoutesDisabledTest extends TestCase
{
    public function test_all_installation_routes_are_removed(): void
    {
        $installRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route): bool {
                $uri = trim($route->uri(), '/');

                return $uri === 'install' || str_starts_with($uri, 'install/');
            });

        $this->assertCount(0, $installRoutes);
    }

    public function test_installer_get_endpoints_return_not_found(): void
    {
        foreach ([
            '/install',
            '/install/app-final-installation',
            '/install/check-permissions',
            '/install/check-requirments',
            '/install/configure',
            '/install/publish-config',
            '/install/refresh-csrf-token',
        ] as $uri) {
            $this->get($uri)->assertNotFound();
        }
    }

    public function test_installer_post_endpoints_return_not_found(): void
    {
        $this->post('/install/app-configure/0')->assertNotFound();
        $this->post('/install/verify-purchase')->assertNotFound();
    }

    public function test_updater_routes_are_removed_without_removing_passport(): void
    {
        $routes = collect(Route::getRoutes()->getRoutes());

        $hasUpdaterRoute = $routes->contains(
            fn ($route): bool => str_starts_with(trim($route->uri(), '/'), 'update')
        );
        $hasPassportTokenRoute = $routes->contains(
            fn ($route): bool => trim($route->uri(), '/') === 'oauth/token'
        );

        $this->assertFalse($hasUpdaterRoute);
        $this->assertTrue($hasPassportTokenRoute);
    }
}
