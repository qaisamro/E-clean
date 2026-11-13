<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GoogleMapsService;
use App\Services\IntegrationSettingsService;
use App\Services\IntegrationTester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MapsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function settings(): IntegrationSettingsService
    {
        return app(IntegrationSettingsService::class);
    }

    private function enableGoogle(string $key = 'server-key-test'): void
    {
        $this->settings()->set('google_maps.enabled', '1', ['group' => 'google_maps']);
        $this->settings()->set('google_maps.server_api_key', $key, [
            'group' => 'google_maps',
            'is_secret' => true,
        ]);
    }

    private function googleOkResponse(): array
    {
        return [
            'status' => 'OK',
            'results' => [[
                'formatted_address' => 'Al Manara Square, Ramallah, Palestine',
                'geometry' => ['location' => ['lat' => 31.9522, 'lng' => 35.2332]],
            ]],
        ];
    }

    public function test_disabled_google_maps_falls_back_to_openstreetmap(): void
    {
        Http::fake([
            'https://nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Ramallah, Palestine',
            ], 200),
        ]);

        $result = app(GoogleMapsService::class)->reverseGeocode(31.9522, 35.2332);

        $this->assertSame('ok', $result['status']);
        $this->assertSame('openstreetmap', $result['provider']);
        $this->assertSame('Ramallah, Palestine', $result['address']);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'googleapis.com'));
    }

    public function test_configured_google_maps_is_used_with_the_server_key(): void
    {
        $this->enableGoogle();
        Http::fake([
            'https://maps.googleapis.com/*' => Http::response($this->googleOkResponse(), 200),
        ]);

        $result = app(GoogleMapsService::class)->reverseGeocode(31.9522, 35.2332);

        $this->assertSame('ok', $result['status']);
        $this->assertSame('google', $result['provider']);
        $this->assertSame('Al Manara Square, Ramallah, Palestine', $result['address']);
        $this->assertSame(31.9522, $result['latitude']);

        Http::assertSent(function ($request) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);

            return str_contains($request->url(), 'maps.googleapis.com/maps/api/geocode/json')
                && $query['key'] === 'server-key-test'
                && $query['latlng'] === '31.9522,35.2332';
        });
    }

    public function test_google_failure_uses_osm_only_when_fallback_is_enabled(): void
    {
        $this->enableGoogle();
        $this->settings()->set('google_maps.fallback_osm', '1', ['group' => 'google_maps']);

        Http::fake([
            'https://maps.googleapis.com/*' => Http::response([
                'status' => 'REQUEST_DENIED',
                'error_message' => 'The provided API key is invalid.',
            ], 200),
            'https://nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Fallback address',
            ], 200),
        ]);

        $result = app(GoogleMapsService::class)->reverseGeocode(31.9, 35.2);
        $this->assertSame('openstreetmap', $result['provider']);
        $this->assertSame('Fallback address', $result['address']);

        $this->settings()->set('google_maps.fallback_osm', '0', ['group' => 'google_maps']);
        $disabledFallback = app(GoogleMapsService::class)->reverseGeocode(31.9, 35.2);

        $this->assertSame('error', $disabledFallback['status']);
        $this->assertSame('google', $disabledFallback['provider']);
        $this->assertStringNotContainsString('server-key-test', (string) $disabledFallback['message']);
    }

    public function test_connection_test_never_falls_back_to_osm_when_the_google_key_fails(): void
    {
        $this->enableGoogle('invalid-google-key');
        $this->settings()->set('google_maps.fallback_osm', '1', ['group' => 'google_maps']);

        Http::fake([
            'https://maps.googleapis.com/*' => Http::response([
                'status' => 'REQUEST_DENIED',
                'error_message' => 'The provided API key is invalid.',
            ], 200),
            'https://nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Fallback address',
            ], 200),
        ]);

        $result = app(GoogleMapsService::class)->test();

        $this->assertSame('error', $result['status']);
        $this->assertSame('google', $result['provider']);
        $this->assertStringNotContainsString('invalid-google-key', (string) $result['message']);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'nominatim.openstreetmap.org'));
    }

    public function test_failed_google_connection_test_is_not_recorded_as_connected(): void
    {
        $this->enableGoogle('invalid-google-key');

        Http::fake([
            'https://maps.googleapis.com/*' => Http::response([
                'status' => 'REQUEST_DENIED',
                'error_message' => 'The provided API key is invalid.',
            ], 200),
            'https://nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Fallback address',
            ], 200),
        ]);

        $result = app(IntegrationTester::class)->test('google_maps');

        $this->assertSame('error', $result['status']);
        $this->assertSame('error', $this->settings()->get('google_maps.last_test_status'));
        $this->assertNotSame('connected', $this->settings()->status('google_maps')['status']);
    }

    public function test_geocoding_requires_configuration(): void
    {
        $result = app(GoogleMapsService::class)->geocode('Ramallah');

        $this->assertSame('not_configured', $result['status']);
        $this->assertNull($result['address']);
    }

    public function test_status_reports_which_platform_keys_exist_without_exposing_them(): void
    {
        $this->enableGoogle();
        $this->settings()->set('google_maps.web_api_key', 'web-key-test', [
            'group' => 'google_maps',
            'is_secret' => true,
        ]);

        $status = app(GoogleMapsService::class)->status();

        $this->assertTrue($status['platform_keys']['server']);
        $this->assertTrue($status['platform_keys']['web']);
        $this->assertFalse($status['platform_keys']['android']);
        $this->assertFalse($status['platform_keys']['ios']);
        $this->assertStringNotContainsString('web-key-test', json_encode($status));
    }

    public function test_public_map_config_never_contains_api_keys(): void
    {
        $this->enableGoogle('super-secret-maps-key');

        $response = $this->getJson('/api/maps/config');

        $response->assertOk()
            ->assertJsonPath('data.enabled', true)
            ->assertJsonPath('data.provider', 'google');
        $response->assertDontSee('super-secret-maps-key');
    }

    public function test_public_reverse_geocode_endpoint_returns_an_address(): void
    {
        $this->enableGoogle();
        Http::fake([
            'https://maps.googleapis.com/*' => Http::response($this->googleOkResponse(), 200),
        ]);

        $this->getJson('/api/maps/reverse-geocode?lat=31.9522&lng=35.2332')
            ->assertOk()
            ->assertJsonPath('data.status', 'ok')
            ->assertJsonPath('data.provider', 'google')
            ->assertJsonPath('data.address', 'Al Manara Square, Ramallah, Palestine');
    }

    public function test_authenticated_reverse_geocode_web_route_is_protected(): void
    {
        $this->getJson('/maps/reverse-geocode?lat=31.9&lng=35.2')->assertUnauthorized();

        $admin = User::create([
            'first_name' => 'Map',
            'last_name' => 'Admin',
            'mobile' => 'map-admin-mobile',
            'email' => 'map-admin@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole(Role::findOrCreate('root', 'web'));

        Http::fake([
            'https://nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Ramallah',
            ], 200),
        ]);

        $this->actingAs($admin)
            ->getJson('/maps/reverse-geocode?lat=31.9&lng=35.2')
            ->assertOk()
            ->assertJsonPath('address', 'Ramallah');
    }
}
