<?php

namespace Tests\Feature;

use App\Models\{Vendor, Service, Product, Variant, Offer, Banner, Media};
use App\Support\{CityCatalog, GeoPolygon};
use Illuminate\Support\Facades\{Cache, Http};
use Tests\Support\RefreshCityDatabase;
use Tests\TestCase;

class CityHomeTest extends TestCase
{
    use RefreshCityDatabase;

    private function store(?string $city, bool $active = true): Vendor
    {
        return Vendor::create(['name' => 'Store '.uniqid(), 'city' => $city,
            'address' => 'نص عنوان في مدينة أخرى', 'is_active' => $active,
            'latitude' => $city === 'دورا' ? 31.507 : 31.532,
            'longitude' => $city === 'دورا' ? 35.028 : 35.099]);
    }

    public function test_catalog_reuses_admin_cities_and_explicit_vendor_city_without_address_inference(): void
    {
        $this->store('مدينة يحددها المشرف');
        $this->store(null);
        $response = $this->getJson('/api/cities')->assertOk()->json('data.cities');
        $names = array_column($response, 'name');
        $this->assertContains('دورا', $names);
        $this->assertContains('مدينة يحددها المشرف', $names);
        $this->assertNotContains('نص عنوان في مدينة أخرى', $names);
        $this->assertSame(count($names), count(array_unique(array_column($response, 'id'))));
        $this->assertSame(app(CityCatalog::class)->forName('أريحا')['id'], app(CityCatalog::class)->forName('اريحا')['id']);
    }

    public function test_vendor_filter_is_additive_and_excludes_missing_city_and_other_cities(): void
    {
        Vendor::query()->delete();
        $wanted = $this->store('دورا'); $this->store('الخليل'); $this->store(null); $this->store('دورا', false);
        $id = app(CityCatalog::class)->forName('دورا')['id'];
        $this->getJson('/api/vendors')->assertOk()->assertJsonCount(3, 'data.vendors.data');
        $this->getJson('/api/vendors?city_id='.$id)->assertOk()
            ->assertJsonCount(1, 'data.vendors.data')->assertJsonPath('data.vendors.data.0.id', $wanted->id)
            ->assertJsonPath('data.vendors.data.0.city_id', $id)
            ->assertJsonStructure(['data' => ['vendors' => ['data' => [['id','name','slug','address','phone','email','logoPath','latitude','longitude','is_active']]]]]);
        foreach (['vendors','services','products','offers','promotions','banners/wide'] as $endpoint) {
            $this->getJson('/api/'.$endpoint.'?city_id=invalid')->assertUnprocessable();
        }
    }

    public function test_every_home_catalog_uses_vendor_city_and_preserves_product_prices(): void
    {
        $variant = Variant::create(['name' => 'Variant', 'is_active' => true]);
        $media = Media::create(['src' => 'city-test.png', 'type' => 'image']);
        $selectedProduct = null;
        foreach (['دورا', 'الخليل'] as $city) {
            $vendor = $this->store($city);
            $service = Service::create(['name' => 'Service '.$city, 'vendor_id' => null, 'is_active' => true]);
            $product = Product::create(['name' => 'Product '.$city, 'vendor_id' => $vendor->id,
                'service_id' => $service->id, 'variant_id' => $variant->id, 'price' => 27.5, 'discount_price' => 22, 'is_active' => true]);
            if ($city === 'دورا') $selectedProduct = $product;
            Offer::create(['title' => 'Offer '.$city, 'vendor_id' => $vendor->id, 'is_active' => true]);
            foreach (['promotional', 'wide'] as $section) Banner::create(['title' => $city,
                'vendor_id' => $vendor->id, 'thumbnail_id' => $media->id, 'is_active' => true,
                'is_banner' => false, 'banner_section' => $section]);
        }
        $id = app(CityCatalog::class)->forName('دورا')['id'];
        $this->getJson('/api/services')->assertOk()->assertJsonCount(2, 'data.services');
        $this->getJson('/api/services?city_id='.$id)->assertOk()->assertJsonCount(2, 'data.services');
        foreach (['products'=>'products','offers'=>'offers','promotions'=>'promotions','banners/wide'=>'banners'] as $endpoint => $key) {
            $this->getJson('/api/'.$endpoint)->assertOk()->assertJsonCount(2, 'data.'.$key);
            $this->getJson('/api/'.$endpoint.'?city_id='.$id)->assertOk()->assertJsonCount(1, 'data.'.$key);
        }
        $this->getJson('/api/products?city_id='.$id)->assertJsonPath('data.products.0.id', $selectedProduct->id)
            ->assertJsonPath('data.products.0.current_price', 22)->assertJsonPath('data.products.0.old_price', 27.5);
        $this->assertSame(27.5, (float) $selectedProduct->fresh()->price);
        $empty = app(CityCatalog::class)->forName('بيت لحم')['id'];
        $this->getJson('/api/vendors?city_id='.$empty)->assertOk()->assertJsonCount(0, 'data.vendors.data');
    }

    private function polygon(): array
    {
        return ['type'=>'Polygon', 'coordinates'=>[[[35,31],[36,31],[36,32],[35,32],[35,31]]]];
    }

    public function test_real_pcbs_boundaries_resolve_approved_cities_without_external_geocoding(): void
    {
        // Geographic test reference points, never production fixtures or customer GPS.
        Http::fake(['*' => Http::response(['geojson'=>['type'=>'Point'], 'name'=>'wrong city'])]);
        foreach ([['دورا',31.507,35.028], ['الخليل',31.532,35.099],
            ['بيت لحم',31.705,35.202], ['رام الله',31.903,35.203]] as [$name,$lat,$lng]) {
            $id = app(CityCatalog::class)->forName($name)['id'];
            $this->postJson('/api/cities/resolve', ['latitude'=>$lat,'longitude'=>$lng,'accuracy'=>25])
                ->assertOk()->assertJsonPath('data.city.id',$id);
        }
        Http::assertNothingSent();
    }

    public function test_real_city_edges_and_uncertain_or_outside_locations_fail_closed(): void
    {
        $features = json_decode(file_get_contents(config('city_location.boundaries_path')),true)['features'];
        foreach (['13502840','13502780','13452240','12301810'] as $code) {
        $dura = collect($features)->firstWhere('properties.locality_code',$code);
        $dura['geometry'] = json_decode(file_get_contents(resource_path('geo/pcbs-localities-2017/'.$code.'.json')),true);
        $ring = $dura['geometry']['type']==='Polygon' ? $dura['geometry']['coordinates'][0] : $dura['geometry']['coordinates'][0][0];
        [$lng,$lat]=$ring[0];
        $this->postJson('/api/cities/resolve',['latitude'=>$lat,'longitude'=>$lng,'accuracy'=>25])
            ->assertOk()->assertJsonPath('data.city',null);
        }
        foreach ([[0,0,10],[31.507,35.028,200]] as [$lat,$lng,$accuracy]) {
            $this->postJson('/api/cities/resolve',['latitude'=>$lat,'longitude'=>$lng,'accuracy'=>$accuracy])
                ->assertOk()->assertJsonPath('data.city',null);
        }
        $this->postJson('/api/cities/resolve',['latitude'=>95,'longitude'=>35])->assertUnprocessable();
    }

    public function test_borders_holes_and_malformed_polygons_fail_closed(): void
    {
        $polygon = $this->polygon();
        $this->assertTrue(GeoPolygon::contains($polygon,31.5,35.5));
        $this->assertFalse(GeoPolygon::contains($polygon,31,35.5));
        $polygon['coordinates'][] = [[35.4,31.4],[35.6,31.4],[35.6,31.6],[35.4,31.6],[35.4,31.4]];
        $this->assertFalse(GeoPolygon::contains($polygon,31.5,35.5));
        $this->assertFalse(GeoPolygon::contains($polygon,31.4,35.5));
        $this->assertFalse(GeoPolygon::contains(['type'=>'Polygon','coordinates'=>[null]],31.5,35.5));
    }

    public function test_unmapped_overlapping_and_corrupt_boundary_data_never_guesses_a_city(): void
    {
        $path = config('city_location.boundaries_path');
        $index = json_decode(file_get_contents($path),true);
        $dura = collect($index['features'])->firstWhere('properties.locality_code','13502840');
        $tmp = tempnam(dirname($path),'boundary-test-');
        try {
            config(['city_location.boundaries_path'=>$tmp]);
            $unmapped=$dura; $unmapped['properties']['city_id']=null;
            $corrupt=$dura; $corrupt['properties']['geometry_sha256']=str_repeat('0',64);
            foreach ([[$unmapped],[$dura,$dura],[$corrupt]] as $features) {
                file_put_contents($tmp,json_encode(['type'=>'FeatureCollection','features'=>$features]));
                $this->postJson('/api/cities/resolve',['latitude'=>31.507,'longitude'=>35.028,'accuracy'=>25])
                    ->assertOk()->assertJsonPath('data.city',null);
            }
        } finally {
            config(['city_location.boundaries_path'=>$path]);
            unlink($tmp);
        }
    }
}

