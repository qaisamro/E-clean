<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The mobile app marks an address as default by posting only `is_default`
 * to POST /api/addresses/{address}. AddressRequest used to require
 * `address_name` and `area` on every update, so that request failed with a
 * 422, and neither the repository nor AddressResource handled the flag.
 */
class CustomerAddressDefaultTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_only_update_sets_one_default_and_clears_the_previous_one(): void
    {
        [$user, , $home, $work] = $this->makeCustomerWithTwoAddresses('owner');

        $this->actingAs($user, 'api')
            ->postJson("/api/addresses/{$work->id}", ['is_default' => '1'])
            ->assertOk()
            ->assertJsonFragment(['id' => $work->id, 'is_default' => true]);

        $this->assertDatabaseHas('addresses', ['id' => $work->id, 'is_default' => true]);
        $this->assertDatabaseHas('addresses', ['id' => $home->id, 'is_default' => false]);
    }

    public function test_default_only_update_can_clear_the_flag(): void
    {
        [$user, , $home] = $this->makeCustomerWithTwoAddresses('owner');

        $this->actingAs($user, 'api')
            ->postJson("/api/addresses/{$home->id}", ['is_default' => '0'])
            ->assertOk();

        $this->assertDatabaseHas('addresses', ['id' => $home->id, 'is_default' => false]);
    }

    public function test_full_update_can_also_set_the_default_flag(): void
    {
        [$user, , $home, $work] = $this->makeCustomerWithTwoAddresses('owner');

        $this->actingAs($user, 'api')
            ->postJson("/api/addresses/{$work->id}", [
                'address_name' => 'Work updated',
                'area' => 'Al-Bireh',
                'address_line' => 'Street 12',
                'is_default' => '1',
            ])
            ->assertOk()
            ->assertJsonFragment(['id' => $work->id, 'is_default' => true]);

        $this->assertDatabaseHas('addresses', [
            'id' => $work->id,
            'address_name' => 'Work updated',
            'is_default' => true,
        ]);
        $this->assertDatabaseHas('addresses', ['id' => $home->id, 'is_default' => false]);
    }

    public function test_store_can_create_an_address_as_default(): void
    {
        [$user, $customer, $home] = $this->makeCustomerWithTwoAddresses('owner');

        $this->actingAs($user, 'api')
            ->postJson('/api/addresses', [
                'address_name' => 'Parents',
                'area' => 'Nablus',
                'address_line' => 'Street 5',
                'is_default' => '1',
            ])
            ->assertOk()
            ->assertJsonFragment(['address_name' => 'Parents', 'is_default' => true]);

        $new = Address::where('customer_id', $customer->id)
            ->where('address_name', 'Parents')
            ->firstOrFail();

        $this->assertDatabaseHas('addresses', ['id' => $new->id, 'is_default' => true]);
        $this->assertDatabaseHas('addresses', ['id' => $home->id, 'is_default' => false]);
    }

    public function test_customer_cannot_mark_another_customers_address_as_default(): void
    {
        [$owner] = $this->makeCustomerWithTwoAddresses('owner');
        [, , , $otherWork] = $this->makeCustomerWithTwoAddresses('other');

        $this->actingAs($owner, 'api')
            ->postJson("/api/addresses/{$otherWork->id}", ['is_default' => '1'])
            ->assertNotFound();

        $this->assertDatabaseHas('addresses', ['id' => $otherWork->id, 'is_default' => false]);
    }

    public function test_default_flag_must_be_a_boolean(): void
    {
        [$user, , , $work] = $this->makeCustomerWithTwoAddresses('owner');

        $this->actingAs($user, 'api')
            ->postJson("/api/addresses/{$work->id}", ['is_default' => 'not-a-boolean'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['is_default']);
    }

    public function test_address_list_exposes_the_default_flag(): void
    {
        [$user, , $home, $work] = $this->makeCustomerWithTwoAddresses('owner');

        $this->actingAs($user, 'api')
            ->getJson('/api/addresses')
            ->assertOk()
            ->assertJsonFragment(['id' => $home->id, 'is_default' => true])
            ->assertJsonFragment(['id' => $work->id, 'is_default' => false]);
    }

    private function makeCustomerWithTwoAddresses(string $key): array
    {
        $user = User::create([
            'first_name' => ucfirst($key),
            'last_name' => 'Test',
            'mobile' => "{$key}-mobile",
            'email' => "{$key}-addresses@example.test",
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('customer', 'web'));

        $customer = Customer::create(['user_id' => $user->id]);

        $home = Address::create([
            'customer_id' => $customer->id,
            'address_name' => 'Home',
            'area' => 'Ramallah',
            'is_default' => true,
        ]);

        $work = Address::create([
            'customer_id' => $customer->id,
            'address_name' => 'Work',
            'area' => 'Al-Bireh',
            'is_default' => false,
        ]);

        return [$user, $customer, $home, $work];
    }
}
