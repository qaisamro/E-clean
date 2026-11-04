<?php

namespace App\Http\Controllers\API\Customers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\DeviceKey;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function show()
    {
        $customer = auth()->user()->customer;
        return $this->json('customer details', [
            'customer' => (new CustomerResource($customer))
        ]);
    }

    public function toggleStatus(User $user)
    {
        (new UserRepository())->toggleStatus($user);
        return $this->json('Deactive Successfully');
    }

    /**
     * Permanently delete the authenticated customer's account.
     * Personal data is scrubbed (orders are kept for store records),
     * device tokens removed, and API tokens revoked.
     */
    public function destroyAccount()
    {
        $user = auth()->user();
        $tag = 'deleted_' . $user->id . '_' . time();

        if ($user->customer) {
            DeviceKey::where('customer_id', $user->customer->id)->delete();
        }
        $user->devices()->delete();

        $user->update([
            'first_name' => 'Deleted',
            'last_name' => 'User',
            'email' => $tag . '@deleted.local',
            'mobile' => $tag,
            'password' => Hash::make(Str::random(32)),
            'is_active' => 0,
            'profile_photo_id' => null,
        ]);

        foreach ($user->tokens as $token) {
            $token->revoke();
        }

        return $this->json('Account deleted successfully');
    }
}
