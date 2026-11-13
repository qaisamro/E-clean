<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\StripeKey;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomerRepository extends Repository
{
    public function model()
    {
        return Customer::class;
    }

    public function getAllOrFindBySearch()
    {
        $searchKey = \request('search');

       $customers = $this->model()::with('user');

        // للسائق: إظهار فقط العملاء الذين لديهم طلبات معيّنة له
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            $driver = auth()->user()->driver;
            if (!$driver || !$driver->vendor_id) {
                return collect();
            }
            $customers = $customers->whereHas('orders', function ($orders) use ($driver) {
                $orders->where('vendor_id', $driver->vendor_id)
                    ->where(function ($assigned) use ($driver) {
                        $assigned->whereHas('drivers', fn ($query) => $query->where('drivers.id', $driver->id))
                            ->orWhereHas('driverHistories', fn ($query) => $query->where('driver_id', $driver->id));
                    });
            });
        }

        // للتاجر الفرعي أو المالك: لا يرى عملاء غير متجره إلا كما تقرر صفحات متجره
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            return collect();
        }

        if ($searchKey) {
            $customers = $customers->whereHas('user', function ($user) use ($searchKey) {
                $user->where('first_name', 'like', "%{$searchKey}%")
                    ->orWhere('last_name', 'like', "%{$searchKey}%")
                    ->orWhere('email', 'like', "%{$searchKey}%");
            });
        }

        // Staff accounts (root/admin/vendor_admin/driver/vendor_sub) are never
        // shown as "customers" of the management list — they have their own
        // login flows. Legacy rows stay in the DB untouched.
        return $customers->latest('id')->get()->filter(
            fn ($customer) => $customer->user
                && !$customer->user->hasAnyRole(['root', 'admin', 'Admin', 'vendor_admin', 'driver', 'vendor_sub'])
        )->values();
    }

    public function findByUserId(int $userId): ?Customer
    {
        return $this->query()->where('user_id', $userId)->first();
    }

    public function storeByUser(User $user): Customer
    {
        return $this->create([
            'user_id' => $user->id,
        ]);
    }

    public function updateByUser(Customer $customer): Customer
    {
        $user = $customer->user;

        $stripeCustomer = (new PaymentRepository())->makeCustomer(name: $user->name, email: $user->email);
        $this->update($customer, [
            'user_id' => $user->id,
            'stripe_customer' => $stripeCustomer?->id
        ]);
        return $customer;
    }
}
