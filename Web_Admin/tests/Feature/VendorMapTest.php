<?php

namespace Tests\Feature;

use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_coordinates_are_additive_and_come_from_the_store(): void
    {
        Vendor::query()->delete();
        $vendor = Vendor::create(['name' => 'Map store', 'slug' => 'map-store',
            'address' => 'Hebron', 'phone' => '12345', 'email' => 'map@example.test',
            'is_active' => true, 'latitude' => 31.5326, 'longitude' => 35.0998]);

        $response = $this->getJson('/api/vendors')->assertOk()
            ->assertJsonPath('message', 'vendor list')
            ->assertJsonPath('data.vendors.data.0.id', $vendor->id)
            ->assertJsonPath('data.vendors.data.0.name', 'Map store')
            ->assertJsonPath('data.vendors.data.0.address', 'Hebron')
            ->assertJsonStructure(['data' => ['vendors' => ['current_page', 'last_page',
                'data' => [['id', 'name', 'slug', 'address', 'phone', 'email', 'is_active', 'logoPath', 'latitude', 'longitude']]]]]);
        $item = $response->json('data.vendors.data.0');
        $this->assertEqualsWithDelta(31.5326, $item['latitude'], 0.0000001);
        $this->assertEqualsWithDelta(35.0998, $item['longitude'], 0.0000001);
        $this->assertSame('12345', $item['phone']);
        $this->assertSame('map@example.test', $item['email']);
        $this->assertSame('map-store', $item['slug']);
        $this->assertCount(12, $item);
        $this->assertArrayHasKey('city_id', $item);
    }

    public function test_missing_coordinates_are_null_and_inactive_stores_stay_hidden(): void
    {
        Vendor::query()->delete();
        Vendor::create(['name' => 'No location', 'address' => 'Hebron', 'is_active' => true]);
        Vendor::create(['name' => 'Hidden', 'is_active' => false, 'latitude' => 10, 'longitude' => 20]);
        $this->getJson('/api/vendors?search=Hebron')->assertOk()
            ->assertJsonCount(1, 'data.vendors.data')
            ->assertJsonPath('data.vendors.data.0.latitude', null)
            ->assertJsonPath('data.vendors.data.0.longitude', null);
    }
}
