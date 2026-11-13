<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Admin permissions screen (Super Admin > Admins > Permissions):
 *  - granting / revoking must never explode just because a permission row is
 *    missing (spatie throws PermissionDoesNotExist),
 *  - granted permissions must show up in the admin's own account, i.e. as
 *    sidebar sections exactly like they do for the Super Admin.
 */
class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('root', 'web');
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('customer', 'web');
    }

    private function root(): User
    {
        $user = User::create([
            'first_name' => 'Super', 'last_name' => 'Root',
            'mobile' => '966500001000', 'email' => 'root-perm@example.test',
            'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $user->assignRole('root');

        return $user;
    }

    private function admin(string $email = 'perm-admin@example.test', string $mobile = '966500001001'): User
    {
        $user = User::create([
            'first_name' => 'Plain', 'last_name' => 'Admin',
            'mobile' => $mobile, 'email' => $email,
            'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $user->assignRole('admin');

        return $user;
    }

    /**
     * Grant exactly the way the controller now does it: create the row when
     * missing, then attach it. spatie throws for unknown names.
     */
    private function grant(User $user, string ...$names): User
    {
        foreach ($names as $name) {
            $user->givePermissionTo(Permission::findOrCreate($name, 'web'));
        }

        return $user;
    }

    // ---------------------------------------------------------------------
    // Permissions management removed: supervisors now inherit the root role
    // automatically, so there is no per-permission setup and no permissions
    // page (route admin.show / admin.set-permission were removed).
    // ---------------------------------------------------------------------

    // ---------------------------------------------------------------------
    // Sidebar: granted permissions behave like Super Admin sections
    // ---------------------------------------------------------------------

    private function dashboardBody(User $user): string
    {
        // admin.index renders layouts.app, which contains the sidebar.
        return $this->actingAs($user)->get(route('admin.index'))->assertStatus(200)->getContent();
    }

    public function test_admin_sees_a_sidebar_section_for_a_granted_permission(): void
    {
        $root = $this->root();
        $admin = $this->grant($this->admin(), 'customer.index');

        $body = $this->dashboardBody($admin);

        $this->assertStringContainsString(route('customer.index'), $body, 'Customers section must appear');
        $this->assertStringNotContainsString(route('vendors.index'), $body, 'Stores section must stay hidden');
        $this->assertStringNotContainsString('#sitePagesManage', $body, 'Site pages section must stay hidden');

        // The Super Admin keeps everything.
        $this->assertStringContainsString(route('customer.index'), $this->dashboardBody($root));
        $this->assertStringContainsString(route('vendors.index'), $this->dashboardBody($root));
        $this->assertStringContainsString('#sitePagesManage', $this->dashboardBody($root));
    }

    public function test_admin_sees_stores_and_site_pages_when_granted(): void
    {
        $admin = $this->grant($this->admin(), 'vendors.index', 'web.setting');

        $body = $this->dashboardBody($admin);

        $this->assertStringContainsString(route('vendors.index'), $body);
        $this->assertStringContainsString(route('web.setting'), $body);
    }

    public function test_admin_without_any_permission_sees_no_super_admin_sections(): void
    {
        $body = $this->dashboardBody($this->admin());

        $this->assertStringNotContainsString(route('customer.index'), $body);
        $this->assertStringNotContainsString(route('vendors.index'), $body);
        $this->assertStringNotContainsString(route('web.setting'), $body);
    }

    public function test_unrelated_sidebar_sections_are_untouched(): void
    {
        // Notifications / Profile were already permission driven and
        // must keep working for a plain admin.
        $admin = $this->grant($this->admin(), 'notification.index', 'profile.index');

        $body = $this->dashboardBody($admin);

        $this->assertStringContainsString(route('notification.index'), $body);
        $this->assertStringContainsString(route('profile.index'), $body);
        $this->assertStringNotContainsString('/revenues', $body);
    }
}
