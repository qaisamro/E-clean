<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Repositories\CustomerRepository;

class DummyCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createUser();
    }

    private function createUser()
    {
        $user = User::factory()->create([
            'first_name' => 'RazinSoft',
            'email' => 'user@razinsoft.com',
            'password' => Hash::make('secret'),
            'mobile' => '01000000100',
            'is_active' => true,
        ]);
        $user->assignRole('customer');

        (new CustomerRepository())->storeByUser($user);
    }
}
