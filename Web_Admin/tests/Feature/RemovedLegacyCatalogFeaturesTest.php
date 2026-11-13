<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Additional;
use App\Models\Service;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;
use Spatie\Permission\Models\Role;
use Tests\Support\RefreshCityDatabase;
use Tests\TestCase;

class RemovedLegacyCatalogFeaturesTest extends TestCase
{
    use RefreshCityDatabase;

    public function test_additional_services_and_sub_product_management_are_unreachable(): void
    {
        $this->actingAs($this->root());

        $this->assertFalse(Route::has('additional.index'));
        $this->assertFalse(Route::has('additional.create'));
        $this->assertFalse(Route::has('product.subproduct.index'));
        $this->assertFalse(Route::has('product.subproduct.store'));

        $this->get('/additional-services')->assertNotFound();
        $this->get('/products/1/subproducts')->assertNotFound();
        $this->get('/sub-product/1/edit')->assertNotFound();
    }

    public function test_super_admin_sidebar_shows_services_and_service_page_has_safe_delete_action(): void
    {
        $service = Service::create([
            'name' => 'Global service',
            'name_bn' => 'خدمة عامة',
            'is_active' => true,
            'vendor_id' => null,
        ]);

        $this->actingAs($this->root())
            ->get(route('service.index'))
            ->assertOk()
            ->assertSee(route('service.index'))
            ->assertSee(route('service.delete', $service))
            ->assertDontSee('additional-services');

        $this->delete(route('service.delete', $service))
            ->assertRedirect(route('service.index'));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_service_delete_detects_soft_deleted_products_instead_of_throwing_500(): void
    {
        $service = Service::create([
            'name' => 'Service with archived product',
            'is_active' => false,
            'vendor_id' => null,
        ]);
        $variant = Variant::create(['name' => 'Internal']);
        $product = Product::create([
            'name' => 'Archived product',
            'price' => 10,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'is_active' => false,
        ]);
        $product->delete();

        $this->actingAs($this->root())
            ->from(route('service.index'))
            ->delete(route('service.delete', $service))
            ->assertRedirect(route('service.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('services', ['id' => $service->id]);
        $this->assertSame(1, Product::withTrashed()->where('service_id', $service->id)->count());
    }

    public function test_product_api_keeps_legacy_key_empty_and_never_exposes_sub_products(): void
    {
        $service = Service::create(['name' => 'Cleaning', 'is_active' => true]);
        $variant = Variant::create(['name' => 'Internal']);
        $parent = Product::create([
            'name' => 'Main product',
            'price' => 20,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Retired child',
            'price' => 5,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'product_id' => $parent->id,
            'is_active' => true,
        ]);

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonFragment(['id' => $parent->id, 'sub_products' => []])
            ->assertJsonMissing(['name' => 'Retired child']);
    }

    public function test_retired_additional_service_input_cannot_change_order_amount(): void
    {
        $service = Service::create(['name' => 'Cleaning', 'is_active' => true]);
        $variant = Variant::create(['name' => 'Internal']);
        $product = Product::create([
            'name' => 'Priced product',
            'price' => 20,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'is_active' => true,
        ]);
        $additional = Additional::create([
            'title' => 'Retired surcharge',
            'price' => 999,
            'is_active' => true,
        ]);
        $request = Request::create('/api/orders', 'POST', [
            'products' => [['id' => $product->id, 'quantity' => 2]],
            'additional_service_id' => [$additional->id],
            'coupon_id' => null,
        ]);

        $method = new \ReflectionMethod(OrderRepository::class, 'getAmount');
        $method->setAccessible(true);
        $amount = $method->invoke(new OrderRepository(), $request);

        $this->assertSame(40.0, (float) $amount['subTotal']);
        $this->assertSame(40.0, (float) $amount['total']);
    }

    private function root(): User
    {
        $user = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'mobile' => 'root-legacy-features',
            'email' => 'root-legacy-features@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('root', 'web'));

        return $user;
    }
}
