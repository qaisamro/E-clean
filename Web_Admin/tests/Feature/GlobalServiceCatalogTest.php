<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Models\Variant;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\Support\RefreshCityDatabase;
use Tests\TestCase;

class GlobalServiceCatalogTest extends TestCase
{
    use RefreshCityDatabase;

    public function test_public_services_are_global_and_service_vendor_list_is_derived_from_products(): void
    {
        $storeA = $this->vendor('catalog-a');
        $storeB = $this->vendor('catalog-b');
        $global = Service::create(['name' => 'Global Cleaning', 'is_active' => true, 'vendor_id' => null]);
        $legacy = Service::create(['name' => 'Legacy Store Service', 'is_active' => true, 'vendor_id' => $storeB->id]);
        $variant = Variant::create(['name' => 'Internal']);

        Product::create([
            'name' => 'A product', 'price' => 10, 'service_id' => $global->id,
            'variant_id' => $variant->id, 'vendor_id' => $storeA->id, 'is_active' => true,
        ]);
        Product::create([
            'name' => 'B legacy product', 'price' => 10, 'service_id' => $legacy->id,
            'variant_id' => $variant->id, 'vendor_id' => $storeB->id, 'is_active' => true,
        ]);

        $this->getJson('/api/services')
            ->assertOk()
            ->assertJsonFragment(['id' => $global->id, 'name' => 'Global Cleaning'])
            ->assertJsonMissing(['id' => $legacy->id, 'name' => 'Legacy Store Service']);

        $this->getJson("/api/services/{$global->id}/vendors")
            ->assertOk()
            ->assertJsonFragment(['id' => $storeA->id, 'name' => $storeA->name])
            ->assertJsonMissing(['id' => $storeB->id, 'name' => $storeB->name]);

        $this->getJson("/api/vendors/{$storeA->id}/services")
            ->assertOk()
            ->assertJsonFragment(['id' => $global->id, 'name' => 'Global Cleaning']);
    }

    public function test_vendor_cannot_manage_services_or_variants_and_product_form_hides_variant(): void
    {
        $vendor = $this->vendor('catalog-owner');
        $user = $vendor->owner;
        foreach (['service.index', 'variant.index', 'product.create', 'product.store'] as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }
        $global = Service::create(['name' => 'Admin Fixed Service', 'is_active' => true, 'vendor_id' => null]);
        $legacy = Service::create(['name' => 'Private Legacy Service', 'is_active' => true, 'vendor_id' => $vendor->id]);
        Variant::create(['name' => 'Hidden Internal Variant']);

        $this->actingAs($user)->get(route('service.index'))->assertForbidden();
        $this->get(route('variant.index'))->assertForbidden();

        $this->get(route('product.create'))
            ->assertOk()
            ->assertSee('Admin Fixed Service')
            ->assertDontSee('Private Legacy Service')
            ->assertDontSee('name="variant_id"', false)
            ->assertDontSee(route('service.index'))
            ->assertDontSee(route('variant.index'));

        Storage::fake('public');
        $this->post(route('product.store'), [
            'name' => 'Vendor product without variant input',
            'price' => 25,
            'service_id' => $global->id,
            'image' => UploadedFile::fake()->image('product.png'),
            'active' => 1,
        ])->assertRedirect(route('product.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Vendor product without variant input',
            'service_id' => $global->id,
            'variant_id' => Variant::first()->id,
            'vendor_id' => $vendor->id,
        ]);
    }

    public function test_catalog_adoption_migration_preserves_and_can_restore_legacy_owners(): void
    {
        $vendor = $this->vendor('legacy-owner');
        $service = Service::create([
            'name' => 'Existing production service',
            'is_active' => true,
            'vendor_id' => $vendor->id,
        ]);
        $migration = require database_path(
            'migrations/2026_11_09_210000_adopt_existing_services_as_global_catalog.php'
        );

        $migration->up();
        $this->assertNull($service->fresh()->vendor_id);
        $this->assertDatabaseHas('service_catalog_legacy_owners', [
            'service_id' => $service->id,
            'vendor_id' => $vendor->id,
        ]);

        $migration->down();
        $this->assertSame($vendor->id, $service->fresh()->vendor_id);
        $this->assertFalse(Schema::hasTable('service_catalog_legacy_owners'));
    }

    private function vendor(string $key): Vendor
    {
        $user = User::create([
            'first_name' => ucfirst($key), 'last_name' => 'Owner',
            'mobile' => $key . '-mobile', 'email' => $key . '@example.test',
            'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('vendor_admin', 'web'));

        return Vendor::create([
            'name' => ucfirst($key), 'slug' => $key,
            'user_id' => $user->id, 'is_active' => true,
        ]);
    }
}
