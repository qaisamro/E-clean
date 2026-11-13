<?php

namespace Tests\Feature;

use App\Models\{Address, Customer, Order, Product, Service, User, Variant, Vendor, VendorInvoiceTemplate};
use App\Support\InvoiceTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Hash, Storage};
use Spatie\Permission\Models\{Permission, Role};
use Tests\TestCase;

class VendorInvoiceTemplateTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Vendor $vendor;
    private Vendor $otherVendor;
    private Order $order;
    private Order $otherOrder;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->owner = $this->user('owner', 'vendor_admin');
        $this->vendor = Vendor::create(['name' => 'متجر الاختبار', 'slug' => 'invoice-a', 'user_id' => $this->owner->id,
            'phone' => '0590000000', 'email' => 'store@example.test', 'address' => 'عنوان المتجر']);
        $otherOwner = $this->user('other-owner', 'vendor_admin');
        $this->otherVendor = Vendor::create(['name' => 'Other private store', 'slug' => 'invoice-b', 'user_id' => $otherOwner->id]);
        $customer = Customer::create(['user_id' => $this->user('buyer', 'customer')->id]);
        $address = Address::create(['customer_id' => $customer->id, 'address_name' => 'Home']);
        $this->order = $this->order($customer, $address, $this->vendor, 'INV-TEST-41');
        $this->otherOrder = $this->order($customer, $address, $this->otherVendor, 'OTHER-PRIVATE-42');
        $service = Service::create(['name' => 'خدمة الغسيل', 'vendor_id' => $this->vendor->id, 'is_active' => true]);
        $variant = Variant::create(['name' => 'Regular']);
        $this->product = Product::create(['name' => 'اسم منتج طويل بالعربية لاختبار التفاف النص في الفاتورة الحرارية',
            'service_id' => $service->id, 'variant_id' => $variant->id, 'vendor_id' => $this->vendor->id,
            'price' => 999, 'discount_price' => 888, 'is_active' => true]);
        $this->order->products()->attach($this->product->id, ['quantity' => 2, 'price' => 10, 'title' => 'اسم الصنف المحفوظ']);
        $this->owner->givePermissionTo(Permission::findOrCreate('order.show', 'web'));
    }

    public function test_editor_has_live_preview_and_only_own_orders(): void
    {
        $response = $this->actingAs($this->owner)->get(route('vendor.invoice-template.edit'))
            ->assertOk()->assertSee('تصميم قالب الفاتورة')->assertSee('id="invoice-preview"', false)
            ->assertSee('INV-TEST-41')->assertDontSee('OTHER-PRIVATE-42');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertDatabaseCount('vendor_invoice_templates', 0);
    }

    public function test_vendor_can_save_reload_and_reset_own_template_without_changing_vendor_or_orders(): void
    {
        $settings = InvoiceTemplate::defaults();
        $settings['text']['store_name'] = 'اسم للفاتورة فقط';
        $settings['text']['footer'] = 'ملاحظة مخصصة';
        $settings['paper'] = '58mm';
        $settings['visible']['customer_phone'] = false;
        $settings['sections'] = array_reverse($settings['sections']);
        $before = $this->order->fresh()->getAttributes();
        $this->actingAs($this->owner)->postJson(route('vendor.invoice-template.update'), [
            'settings' => $settings, 'vendor_id' => $this->otherVendor->id,
        ])->assertOk();
        $this->assertDatabaseCount('vendor_invoice_templates', 1);
        $this->assertDatabaseHas('vendor_invoice_templates', ['vendor_id' => $this->vendor->id]);
        $this->assertDatabaseMissing('vendor_invoice_templates', ['vendor_id' => $this->otherVendor->id]);
        $this->assertSame($before, $this->order->fresh()->getAttributes());
        $this->assertSame('متجر الاختبار', $this->vendor->fresh()->name);
        $this->get(route('vendor.invoice-template.edit'))->assertViewHas('settings', fn ($s) => $s === $settings);
        $this->deleteJson(route('vendor.invoice-template.reset'))->assertOk()->assertJsonPath('settings.paper', '80mm');
        $this->assertDatabaseCount('vendor_invoice_templates', 0);
    }

    public function test_each_copy_uses_the_same_saved_settings_and_immutable_real_order_data(): void
    {
        $settings = InvoiceTemplate::defaults(); $settings['text']['footer'] = 'Unified footer';
        VendorInvoiceTemplate::create(['vendor_id' => $this->vendor->id, 'settings' => $settings]);
        $before = $this->order->fresh()->getAttributes();
        foreach (['normal', 'customer', 'store'] as $copy) {
            $this->actingAs($this->owner)->get(route('order.pos-invoice', ['order' => $this->order->id, 'copy' => $copy]))
                ->assertOk()->assertViewHas('payload', function ($p) use ($settings, $copy) {
                    $this->assertSame($settings, $p['settings']);
                    $this->assertSame($copy, $p['copy']);
                    $this->assertSame('INV-TEST-41', $p['data']['order']['code']);
                    $this->assertEquals(20, $p['data']['order']['subtotal']);
                    $this->assertEquals(23, $p['data']['order']['total']);
                    $this->assertEquals(10, $p['data']['order']['items'][0]['unit_price']);
                    $this->assertEquals(20, $p['data']['order']['items'][0]['total']);
                    $this->assertSame('اسم الصنف المحفوظ', $p['data']['order']['items'][0]['name']);
                    $this->assertNull($p['data']['order']['tax']);
                    $this->assertNull($p['data']['order']['cashier']);
                    return true;
                });
        }
        $this->assertSame($before, $this->order->fresh()->getAttributes());
    }

    public function test_template_isolation_preserves_another_stores_settings_and_logo(): void
    {
        Storage::disk('local')->put('invoice-logos/other/private.png', 'private-logo');
        $settings = InvoiceTemplate::defaults(); $settings['text']['header'] = 'OTHER STORE SECRET';
        $other = VendorInvoiceTemplate::create(['vendor_id' => $this->otherVendor->id, 'settings' => $settings,
            'logo_path' => 'invoice-logos/other/private.png']);
        $this->actingAs($this->owner)->get(route('vendor.invoice-template.edit', ['vendor_id' => $this->otherVendor->id]))
            ->assertOk()->assertDontSee('OTHER STORE SECRET');
        $this->postJson(route('vendor.invoice-template.update'), [
            'settings' => InvoiceTemplate::defaults(), 'vendor_id' => $this->otherVendor->id,
            'remove_logo' => true, 'logo_path' => 'invoice-logos/other/private.png',
        ])->assertOk();
        $this->deleteJson(route('vendor.invoice-template.reset'), ['vendor_id' => $this->otherVendor->id])->assertOk();
        $this->assertSame($settings, $other->fresh()->settings);
        Storage::disk('local')->assertExists('invoice-logos/other/private.png');
    }

    public function test_replacing_a_logo_removes_only_the_previous_owned_logo(): void
    {
        $this->actingAs($this->owner);
        $this->post(route('vendor.invoice-template.update'), ['settings' => InvoiceTemplate::defaults(),
            'logo' => UploadedFile::fake()->image('one.jpg')], ['Accept' => 'application/json'])->assertOk();
        $old = VendorInvoiceTemplate::firstOrFail()->logo_path;
        $this->post(route('vendor.invoice-template.update'), ['settings' => InvoiceTemplate::defaults(),
            'logo' => UploadedFile::fake()->image('two.jpg')], ['Accept' => 'application/json'])->assertOk();
        $new = VendorInvoiceTemplate::firstOrFail()->logo_path;
        $this->assertNotSame($old, $new);
        Storage::disk('local')->assertMissing($old);
        Storage::disk('local')->assertExists($new);
    }

    public function test_all_three_paper_formats_and_default_fallback(): void
    {
        foreach (['A4', '80mm', '58mm'] as $paper) {
            $settings = InvoiceTemplate::defaults(); $settings['paper'] = $paper;
            $this->actingAs($this->owner)->postJson(route('vendor.invoice-template.update'), ['settings' => $settings])->assertOk();
            $this->get(route('order.pos-invoice', $this->order))->assertOk()
                ->assertViewHas('payload', fn ($p) => $p['settings']['paper'] === $paper);
        }
        $this->deleteJson(route('vendor.invoice-template.reset'))->assertOk();
        $this->get(route('order.pos-invoice', $this->order))->assertOk()
            ->assertViewHas('payload', fn ($p) => $p['settings'] === InvoiceTemplate::defaults());
    }

    public function test_foreign_order_is_not_available_for_preview_or_any_copy(): void
    {
        foreach (['normal', 'customer', 'store'] as $copy) {
            $this->actingAs($this->owner)->get(route('order.pos-invoice', ['order' => $this->otherOrder->id, 'copy' => $copy]))
                ->assertNotFound()->assertDontSee('OTHER-PRIVATE-42');
        }
        $this->get(route('vendor.invoice-template.preview', ['order' => $this->otherOrder->id]))->assertNotFound();
    }

    public function test_customer_and_driver_cannot_use_editor_or_print_routes(): void
    {
        foreach (['customer', 'driver'] as $role) {
            auth()->logout();
            $user = $this->user('denied-'.$role, $role);
            foreach (['vendor.invoice-template.edit', 'vendor.invoice-template.update', 'order.pos-invoice'] as $permission) {
                $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
            }
            $this->actingAs($user)->get(route('vendor.invoice-template.edit'))->assertForbidden();
            $this->postJson(route('vendor.invoice-template.update'), ['settings' => InvoiceTemplate::defaults()])->assertForbidden();
            $this->get(route('order.pos-invoice', $this->order))->assertForbidden();
        }
    }

    public function test_admin_can_print_a_valid_order_using_its_vendor_template(): void
    {
        $settings = InvoiceTemplate::defaults(); $settings['accent'] = '#112233';
        VendorInvoiceTemplate::create(['vendor_id' => $this->vendor->id, 'settings' => $settings]);
        foreach (['root', 'admin'] as $role) {
            auth()->logout();
            $this->actingAs($this->user('invoice-'.$role, $role))->get(route('order.pos-invoice', $this->order))
                ->assertOk()->assertViewHas('payload', fn ($p) => $p['settings']['accent'] === '#112233');
        }
    }

    public function test_mixed_or_unresolved_orders_are_not_branded_as_one_store(): void
    {
        $this->product->update(['vendor_id' => $this->otherVendor->id]);
        $this->actingAs($this->owner)->get(route('order.pos-invoice', $this->order))->assertNotFound();
        auth()->logout();
        $this->actingAs($this->user('mixed-root', 'root'))->get(route('order.pos-invoice', $this->order))->assertStatus(409);
        $this->order->update(['vendor_id' => null]);
        $this->get(route('order.pos-invoice', $this->order))->assertStatus(409);
    }

    public function test_empty_store_has_an_honest_preview_without_fake_order_values(): void
    {
        $this->actingAs($this->owner)->get(route('vendor.invoice-template.preview'))->assertOk()
            ->assertViewHas('payload', fn ($p) => $p['data']['order'] === null && $p['data']['vendor']['name'] === $this->vendor->name);
    }

    public function test_empty_copy_query_defaults_to_normal(): void
    {
        $this->actingAs($this->owner)->get(route('order.pos-invoice', ['order' => $this->order->id, 'copy' => '']))
            ->assertOk()->assertViewHas('payload', fn ($p) => $p['copy'] === 'normal');
    }

    public function test_zero_historical_price_is_preserved_and_null_does_not_use_catalog_price(): void
    {
        $this->order->products()->updateExistingPivot($this->product->id, ['price' => 0]);
        $this->actingAs($this->owner)->get(route('order.pos-invoice', $this->order))->assertOk()
            ->assertViewHas('payload', fn ($p) => (float) $p['data']['order']['items'][0]['unit_price'] === 0.0);
        $this->order->products()->updateExistingPivot($this->product->id, ['price' => null]);
        $this->get(route('order.pos-invoice', $this->order))->assertOk()
            ->assertViewHas('payload', fn ($p) => $p['data']['order']['items'][0]['unit_price'] === null);
    }

    public function test_rejects_markup_css_injection_unknown_financial_fields_and_invalid_section_order(): void
    {
        foreach ([['text', 'header', '<script>alert(1)</script>'], ['accent', null, 'red; background:url(https://bad.test)'],
            ['total_amount', null, 0], ['sections', null, array_fill(0, 6, 'brand')], ['paper', null, '100mm']] as [$key, $child, $value]) {
            $settings = InvoiceTemplate::defaults();
            if ($child) $settings[$key][$child] = $value; else $settings[$key] = $value;
            $this->actingAs($this->owner)->postJson(route('vendor.invoice-template.update'), ['settings' => $settings])->assertUnprocessable();
        }
        $this->assertDatabaseCount('vendor_invoice_templates', 0);
        $this->getJson(route('order.pos-invoice', ['order' => $this->order->id, 'copy' => 'invalid']))->assertUnprocessable();
    }

    public function test_logo_upload_is_private_validated_and_removed_on_reset(): void
    {
        $this->actingAs($this->owner)->post(route('vendor.invoice-template.update'), [
            'settings' => json_encode(InvoiceTemplate::defaults()), 'logo' => UploadedFile::fake()->image('logo.png', 120, 120),
        ], ['Accept' => 'application/json'])->assertOk();
        $template = VendorInvoiceTemplate::firstOrFail();
        Storage::disk('local')->assertExists($template->logo_path);
        $this->get(route('order.pos-invoice', $this->order))->assertOk()
            ->assertViewHas('payload', fn ($p) => str_starts_with($p['logo'], 'data:image/png;base64,'));
        $this->deleteJson(route('vendor.invoice-template.reset'))->assertOk();
        Storage::disk('local')->assertMissing($template->logo_path);
        foreach ([UploadedFile::fake()->create('unsafe.svg', 1, 'image/svg+xml'),
            UploadedFile::fake()->image('big.jpg')->size(1025), UploadedFile::fake()->image('wide.png', 2401, 1)] as $file) {
            $this->post(route('vendor.invoice-template.update'), ['settings' => InvoiceTemplate::defaults(), 'logo' => $file],
                ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('logo');
        }
    }

    public function test_order_details_have_three_print_links_without_removing_legacy_print(): void
    {
        $this->actingAs($this->owner)->get(route('order.show', $this->order))
            ->assertOk()->assertSee('طباعة عادية')->assertSee('طباعة الزبون')->assertSee('طباعة المحل');
        $this->owner->givePermissionTo(Permission::findOrCreate('pos.index', 'web'));
        $this->get(route('pos.index'))->assertOk()->assertSee('/pos-invoice');
    }

    public function test_export_browser_fixture_when_requested(): void
    {
        if (!$folder = getenv('INVOICE_BROWSER_ARTIFACTS')) {
            $this->assertTrue(true);
            return;
        }
        if (!is_dir($folder)) mkdir($folder, 0777, true);
        $this->actingAs($this->owner);
        file_put_contents($folder.'/editor.html', $this->get(route('vendor.invoice-template.edit'))->assertOk()->getContent());
        file_put_contents($folder.'/document.html', $this->get(route('vendor.invoice-template.preview', ['order' => $this->order->id]))->assertOk()->getContent());
    }

    private function user(string $key, string $role): User
    {
        $user = User::create(['first_name' => $key, 'last_name' => 'Test', 'mobile' => $key.'-mobile',
            'email' => $key.'@example.test', 'password' => Hash::make('password'), 'is_active' => true]);
        $user->assignRole(Role::findOrCreate($role, 'web'));
        return $user;
    }

    private function order(Customer $customer, Address $address, Vendor $vendor, string $code): Order
    {
        return Order::create(['customer_id' => $customer->id, 'address_id' => $address->id, 'vendor_id' => $vendor->id,
            'order_code' => $code, 'prefix' => 'LM', 'pick_date' => today(), 'delivery_date' => today()->addDay(),
            'amount' => 20, 'discount' => 2, 'delivery_charge' => 5, 'total_amount' => 23,
            'payment_status' => config('enums.payment_status.pending'), 'order_status' => config('enums.order_status.pending'),
            'payment_type' => config('enums.payment_types.cash_on_delivery'), 'payment_method' => 'cash']);
    }
}
