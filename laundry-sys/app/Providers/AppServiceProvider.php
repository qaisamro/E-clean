<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        // Fix Livewire for subfolder deployment (e.g., /pos_private)
        $appUrl = config('app.url');
        $parsed = parse_url($appUrl);
        $path = trim($parsed['path'] ?? '', '/');
        if ($path) {
            $prefix = '/' . $path;
            app(\Livewire\Mechanisms\HandleRequests\HandleRequests::class)->setUpdateRoute(function ($handle) use ($prefix) {
                return Route::post($prefix . '/livewire/update', $handle)->middleware('web');
            });
            Route::get($prefix . '/livewire/livewire.min.js', [\Livewire\Mechanisms\FrontendAssets\FrontendAssets::class, 'returnJavaScriptAsFile']);
            Route::get($prefix . '/livewire/livewire.min.js.map', [\Livewire\Mechanisms\FrontendAssets\FrontendAssets::class, 'maps']);
            // Make the generated script src include the prefix via asset_url config at runtime
            config(['livewire.asset_url' => url('livewire/livewire.min.js')]);
        }
    }
}
