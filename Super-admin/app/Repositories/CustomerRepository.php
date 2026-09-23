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
            if (!$driver) {
                return collect();
            }
            $driverOrderIds = $driver->orders->pluck('id')->merge($driver->orderHistories->pluck('id'))->unique()->filter();
            if ($driverOrderIds->isEmpty()) {
                return collect();
            }
            $customerIds = \App\Models\Order::whereIn('id', $driverOrderIds)->pluck('customer_id')->unique()->filter();
            if ($customerIds->isEmpty()) {
                return collect();
            }
            $customers = $customers->whereIn('id', $customerIds);
        }

        // للتاجر: لا يرى العملاء إطلاقاً - فقط السوبر أدمن (root) يرى كل العملاء
        if (auth()->check() && auth()->user()->hasRole('vendor_admin')) {
            return collect();
        }

        if ($searchKey) {
            $customers = $customers->whereHas('user', function ($user) use ($searchKey) {
                $user->where('first_name', 'like', "%{$searchKey}%")
                    ->orWhere('last_name', 'like', "%{$searchKey}%")
                    ->orWhere('email', 'like', "%{$searchKey}%");
            });
        }

        return $customers->latest('id')->get();
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
