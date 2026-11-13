<?php

namespace App\Providers;

use App\Repositories\GeneralSettingRepository;
use App\Services\Smtp\SocketSmtpConnector;
use App\Services\Smtp\SmtpConnector;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmtpConnector::class, SocketSmtpConnector::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // This project's oauth_clients.id is a legacy auto-increment integer, not
        // a UUID. Passport 13 defaults to UUID client ids, which makes
        // ClientRepository::find() return null for integer ids and silently
        // rejects every valid access token (401 on all protected API routes).
        \Laravel\Passport\Passport::$clientUuids = false;

        // Public installer, updater and Livewire endpoints are intentionally
        // disabled. The application uses neither installation/update screens
        // nor Livewire interactions in production.
        $this->app->booted(function (): void {
            $router = $this->app['router'];
            $routes = new RouteCollection();

            foreach ($router->getRoutes() as $route) {
                $uri = trim($route->uri(), '/');

                if (in_array($uri, ['install', 'update', 'livewire'], true)
                    || str_starts_with($uri, 'install/')
                    || str_starts_with($uri, 'update/')
                    || str_starts_with($uri, 'livewire/')) {
                    continue;
                }

                $routes->add($route);
            }

            $router->setRoutes($routes);
        });

        Schema::defaultStringLength(191);
    }
}
