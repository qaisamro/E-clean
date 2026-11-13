<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Super Admin "admins" screen:
 *  - creating an admin must not blow up with a 500 when the "root" permission
 *    does not exist yet,
 *  - the status switch must be a real POST form (it used to be an <a> link,
 *    which produced 405 Method Not Allowed),
 *  - admins can be deleted.
 */
class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // This install spells the role "admin"; create both spellings' base.
        Role::findOrCreate('root', 'web');
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('customer', 'web');
    }

    private function root(string $suffix = ''): User
    {
        $user = User::create([
            'first_name' => 'Super', 'last_name' => 'Root',
            'mobile' => '966500000910' . $suffix, 'email' => 'root-admin' . $suffix . '@example.test',
            'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $user->assignRole('root');

        return $user;
    }

    private function adminPayload(array $extra = []): array
    {
        return array_merge([
            'first_name' => 'New', 'last_name' => 'Admin',
            'email' => 'new-admin@example.test', 'mobile' => '966500000911',
            'gender' => 'male', 'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $extra);
    }

    public function test_creating_an_admin_works_even_without_the_root_permission(): void
    {
        // Production starts without a permission literally named "root".
        $this->assertFalse(Permission::where('name', 'root')->exists());

        $this->actingAs($this->root())
            ->post(route('admin.store'), $this->adminPayload())
            ->assertRedirect(route('admin.index'));

        $admin = User::where('email', 'new-admin@example.test')->first();
        $this->assertNotNull($admin, 'the admin account must be created');
        $this->assertTrue($admin->hasRole('admin'), 'role name is "Admin" in this install');
        $this->assertTrue((bool) $admin->is_active, 'a new admin must be able to log in');
        $this->assertTrue($admin->hasPermissionTo('root'));
    }

    public function test_creating_an_admin_creates_the_root_permission_once(): void
    {
        $root = $this->root();
        $this->actingAs($root)
            ->post(route('admin.store'), $this->adminPayload(['email' => 'a1@example.test', 'mobile' => '966500000921']))
            ->assertRedirect();

        $this->assertSame(1, Permission::where('name', 'root')->count());

        $this->actingAs($root)
            ->post(route('admin.store'), $this->adminPayload(['email' => 'a2@example.test', 'mobile' => '966500000922']))
            ->assertRedirect();

        $this->assertSame(1, Permission::where('name', 'root')->count(), 'must not duplicate the permission');
    }

    public function test_status_switch_route_accepts_post_only(): void
    {
        $root = $this->root();
        $admin = User::create([
            'first_name' => 'Toggle', 'last_name' => 'Me',
            'mobile' => '966500000930', 'email' => 'toggle@example.test',
            'password' => Hash::make('password'), 'is_active' => false,
        ]);
        $admin->assignRole('admin');

        // GET must be rejected (405) — the old broken link used GET.
        $this->actingAs($root)->get(route('admin.status-update', $admin->id))->assertStatus(405);

        // POST works and flips the flag.
        $this->actingAs($root)->post(route('admin.status-update', $admin->id))->assertRedirect();
        $this->assertTrue((bool) $admin->fresh()->is_active);

        $this->actingAs($root)->post(route('admin.status-update', $admin->id))->assertRedirect();
        $this->assertFalse((bool) $admin->fresh()->is_active);
    }

    public function test_activated_admin_can_authenticate(): void
    {
        // The web console authenticates admins; the /api/login endpoint is
        // reserved for customers (it needs a customer record).
        $root = $this->root();
        $admin = User::create([
            'first_name' => 'Login', 'last_name' => 'Test',
            'mobile' => '966500000931', 'email' => 'admin-login@example.test',
            'password' => Hash::make('password123'), 'is_active' => false,
        ]);
        $admin->assignRole('admin');

        // While inactive, the console lookup refuses the account.
        $this->assertNull((new \App\Repositories\UserRepository())->findActiveBycontact('admin-login@example.test'));

        $this->actingAs($root)->post(route('admin.status-update', $admin->id))->assertRedirect();

        // After activation the same lookup succeeds => the admin can sign in.
        $found = (new \App\Repositories\UserRepository())->findActiveBycontact('admin-login@example.test');
        $this->assertNotNull($found);
        $this->assertTrue(Hash::check('password123', $found->password));
    }

    public function test_admin_can_be_deleted(): void
    {
        $root = $this->root();
        $admin = User::create([
            'first_name' => 'Delete', 'last_name' => 'Me',
            'mobile' => '966500000940', 'email' => 'delete-me@example.test',
            'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $this->actingAs($root)
            ->delete(route('admin.destroy', $admin->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $root = $this->root();

        $this->actingAs($root)
            ->delete(route('admin.destroy', $root->id))
            ->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $root->id]);
    }

    public function test_admin_cannot_delete_a_root_account(): void
    {
        $root = $this->root();
        $otherRoot = User::create([
            'first_name' => 'Other', 'last_name' => 'Root',
            'mobile' => '966500000950', 'email' => 'other-root@example.test',
            'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $otherRoot->assignRole('root');

        $this->actingAs($root)
            ->delete(route('admin.destroy', $otherRoot->id))
            ->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $otherRoot->id]);
    }

    public function test_admins_list_renders(): void
    {
        User::create([
            'first_name' => 'Listed', 'last_name' => 'Admin',
            'mobile' => '966500000960', 'email' => 'listed@example.test',
            'password' => Hash::make('password'), 'is_active' => true,
        ])->assignRole(Role::findOrCreate('admin', 'web'));

        $this->actingAs($this->root())
            ->get(route('admin.index'))
            ->assertStatus(200)
            ->assertSee('listed@example.test');
    }
}
