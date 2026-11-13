<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LegacyWebRoutesDisabledTest extends TestCase
{
    public function test_removed_legacy_web_route_prefixes_are_not_registered(): void
    {
        $blocked = [
            'areas', 'change-local', 'account', 'contacts', 'delivery-cost',
            'cart', 'checkout', 'faq', 'faq-categories', 'faqs', 'fcm',
            'form-login', 'mobile-app', 'invoice-manage', 'languages',
            'livewire', 'mail-config', 'orders/new', 'payment-gateway',
            'payment', 'privacy', 'social-links', 'sms-gateway', 'store',
            'stripe', 'stripe-key', 'update', 'web/test', 'website/contact',
            'website/service', 'website/services',
        ];

        $remaining = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => trim($route->uri(), '/'))
            ->filter(function (string $uri) use ($blocked): bool {
                return collect($blocked)->contains(
                    fn (string $prefix): bool => $uri === $prefix || str_starts_with($uri, $prefix.'/')
                );
            });

        $this->assertCount(0, $remaining);
    }

    public function test_web_login_uses_login_uri_and_passport_remains_available(): void
    {
        $routes = collect(Route::getRoutes()->getRoutes());

        $this->assertTrue($routes->contains(
            fn ($route): bool => trim($route->uri(), '/') === 'login' && in_array('POST', $route->methods(), true)
        ));
        $this->assertFalse($routes->contains(
            fn ($route): bool => trim($route->uri(), '/') === 'form-login'
        ));
        $this->assertTrue($routes->contains(
            fn ($route): bool => trim($route->uri(), '/') === 'oauth/token'
        ));
    }
}
