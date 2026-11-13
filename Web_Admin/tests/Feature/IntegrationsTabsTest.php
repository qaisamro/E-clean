<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Integrations centre tab switching + vendor delete confirmation.
 *
 * The tab handler used to be inlined inside @section('content'), but the
 * layout prints the content *before* it loads jQuery, so "$ is not defined"
 * killed the handler and the tabs never reacted. These tests pin the correct
 * loading order and the deep-link targets.
 */
class IntegrationsTabsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('root', 'web');
        Role::findOrCreate('admin', 'web');
    }

    private function root(): User
    {
        $user = User::create([
            'first_name' => 'Super', 'last_name' => 'Root',
            'mobile' => '966500002000', 'email' => 'tabs-root@example.test',
            'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $user->assignRole('root');

        return $user;
    }

    public function test_tab_handler_is_printed_after_jquery_is_loaded(): void
    {
        $root = $this->root();

        $this->actingAs($root)
            ->get(route('integrations.index'))
            ->assertStatus(200);

        $html = $this->actingAs($root)->get(route('integrations.index'))->getContent();

        $jquery = strpos($html, 'jquery.min.js');
        $handler = strpos($html, 'click.icTabs');

        $this->assertNotFalse($jquery, 'jQuery must be loaded on the page');
        $this->assertNotFalse($handler, 'the tab handler must be rendered on the page');
        $this->assertLessThan(
            $handler,
            $jquery,
            'the tab handler must run AFTER jQuery, otherwise "$ is not defined" and no tab ever switches'
        );
    }

    public function test_tab_handler_uses_no_bootstrap_tab_api(): void
    {
        $html = $this->actingAs($this->root())->get(route('integrations.index'))->getContent();

        $this->assertStringNotContainsString('.tab(\'', $html);
        $this->assertStringNotContainsString('bootstrap.Tab', $html);
        $this->assertStringContainsString('e.preventDefault()', $html, 'pure jQuery handler');
    }

    public function test_handler_supports_hash_deep_linking_and_history(): void
    {
        $html = $this->actingAs($this->root())->get(route('integrations.index'))->getContent();

        // Deep link on load + browser back/forward.
        $this->assertStringContainsString('window.location.hash', $html);
        $this->assertStringContainsString('popstate', $html);
        $this->assertStringContainsString('pushState', $html);
    }

    public function test_every_tab_has_a_matching_pane(): void
    {
        $html = $this->actingAs($this->root())->get(route('integrations.index'))->getContent();

        preg_match_all('/data-target="#([^"]+)"/', $html, $tabs);

        $this->assertNotEmpty($tabs[1], 'the tab strip must be rendered');

        foreach (array_unique($tabs[1]) as $target) {
            $this->assertStringContainsString(
                'id="' . $target . '"',
                $html,
                'every tab must point at an existing pane, otherwise a deep link blanks the page'
            );
        }
    }

    public function test_stylesheet_is_cache_busted(): void
    {
        $html = $this->actingAs($this->root())->get(route('integrations.index'))->getContent();

        $this->assertStringContainsString('integrations.css?v=2', $html);
    }

    // ---------------------------------------------------------------------
    // Vendors delete confirmation
    // ---------------------------------------------------------------------

    public function test_vendor_delete_button_opens_the_confirmation_window(): void
    {
        $vendor = new Vendor();
        $vendor->name = 'متجر الاختبار';
        $vendor->address = 'الرياض';
        $vendor->phone = '966500002001';
        $vendor->is_active = true;
        $vendor->save();

        $html = $this->actingAs($this->root())->get(route('vendors.index'))->assertStatus(200)->getContent();

        $this->assertStringContainsString('btn-delete-vendor', $html);
        $this->assertStringContainsString('data-vendor-name="متجر الاختبار"', $html);
        $this->assertStringContainsString('name="_method"', $html, 'the form must spoof DELETE');

        // The confirmation *script* must run after jQuery (the button itself
        // is printed in the content, before jQuery — that is fine and expected).
        $jquery = strpos($html, 'jquery.min.js');
        $handler = strpos($html, 'ec-delete-popup');
        $this->assertNotFalse($jquery);
        $this->assertNotFalse($handler, 'the delete confirmation script must be rendered');
        $this->assertLessThan($handler, $jquery, 'the delete confirmation must run after jQuery');
    }

    public function test_vendor_delete_escaping_does_not_break_the_markup(): void
    {
        $vendor = new Vendor();
        $vendor->name = 'متجر "الأمان" & <b>الاختبار</b>';
        $vendor->address = 'جدة';
        $vendor->phone = '966500002002';
        $vendor->is_active = true;
        $vendor->save();

        $html = $this->actingAs($this->root())->get(route('vendors.index'))->assertStatus(200)->getContent();

        $this->assertStringNotContainsString('data-vendor-name="متجر "الأمان"', $html, 'the name must be HTML-escaped');
    }
}
