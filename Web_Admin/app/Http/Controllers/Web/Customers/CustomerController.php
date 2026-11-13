<?php

namespace App\Http\Controllers\Web\Customers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\CustomerRepository;
use App\Http\Requests\RegistrationRequest;
use App\Models\Address;
use App\Models\User;

class CustomerController extends Controller
{
    /**
     * Staff accounts (root/admin/vendor_admin/driver/vendor_sub) may hold a
     * legacy customer row, but they are never listed, edited or deleted
     * from the customer management screens.
     */
    private function denyStaffRoleCustomer(Customer $customer): void
    {
        $user = $customer->user;
        abort_if(
            $user && $user->hasAnyRole(['root', 'admin', 'Admin', 'vendor_admin', 'driver', 'vendor_sub']),
            403,
            'هذا الحساب محمي — لا يمكن إدارته من صفحة العملاء.'
        );
    }

    /**
     * Whether a plain user account is a staff account that must stay
     * untouched from the customer screens.
     */
    private function isStaffRoleAccount(User $user): bool
    {
        return $user->hasAnyRole(['root', 'admin', 'Admin', 'vendor_admin', 'driver', 'vendor_sub']);
    }

    public function index()
    {
        // للسوبر أدمن فقط - منع التجار من رؤية قائمة العملاء (الإدارة)
        abort_if(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);

        $customers = (new CustomerRepository())->getAllOrFindBySearch();
        
        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        // للسوبر أدمن فقط - منع التجار من رؤية العملاء
        if (\App\Support\VendorAccount::isStoreStaff(auth()->user())) {
            abort(403, 'غير مصرح لك بعرض العملاء - للسوبر أدمن فقط');
        }
        $this->denyStaffRoleCustomer($customer);
        // للسائق: السماح فقط برؤية عملائه المرتبطين بطلباته
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            $driver = auth()->user()->driver;
            $allowedIds = collect();
            if ($driver && $driver->vendor_id) {
                $allowedIds = \App\Models\Order::query()
                    ->where('vendor_id', $driver->vendor_id)
                    ->where(function ($query) use ($driver) {
                        $query->whereHas('drivers', fn ($assigned) => $assigned->where('drivers.id', $driver->id))
                            ->orWhereHas('driverHistories', fn ($history) => $history->where('driver_id', $driver->id));
                    })
                    ->pluck('customer_id')->unique();
            }
            if (!$allowedIds->contains($customer->id)) {
                abort(403, 'غير مصرح لك بعرض هذا العميل');
            }
        }

        return view('customers.show', [
            'customer' => $customer
        ]);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(RegistrationRequest $request)
    {
        $user = (new UserRepository())->registerUser($request);
        (new CustomerRepository())->storeByUser($user);
        $user->assignRole('customer');
        $user->update([
            'mobile_verified_at' => now()
        ]);
        return redirect()->route('customer.index')->with('success', 'تم إنشاء العميل بنجاح');
    }

    public function edit(Customer $customer)
    {
        abort_if(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        $this->denyStaffRoleCustomer($customer);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        abort_if(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        $this->denyStaffRoleCustomer($customer);

        // Only root/admin may set a new password for the customer. The blade
        // hides the fields for everyone else, but this endpoint is reachable
        // by visitors too (customer.update) — enforce the same rule server-side.
        $actor = $request->user();
        $canSetPassword = $actor && ($actor->hasRole('root') || $actor->hasRole('admin'));

        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'mobile' => "required|numeric|unique:users,mobile," . $customer->user->id,
            'email' => "nullable|email|unique:users,email," . $customer->user->id,
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
        if ($canSetPassword) {
            $rules['password'] = 'nullable|confirmed|min:6';
        }
        $request->validate($rules);
        (new UserRepository())->updateProfileByRequest($request, $customer->user);

        if ($canSetPassword && $request->filled('password')) {
            $customer->user->update([
                'password' => Hash::make($request->password),
            ]);
            // The new password replaces the old credential: drop existing API
            // tokens so every device must sign in again with the new password.
            $customer->user->tokens()->delete();

            return redirect()->route('customer.index')
                ->with('success', 'تم تحديث العميل وتعيين كلمة مرور جديدة بنجاح');
        }

        return redirect()->route('customer.index')->with('success', 'تم تحديث العميل بنجاح');
    }

    public function delete(Customer $customer)
    {
        abort_if(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        $this->denyStaffRoleCustomer($customer);
        $user = $customer->user;
        $orders = $customer->orders;

        foreach ($orders as $order) {
            $order->payment?->delete();
            $order->products()->detach();
            $order->rating?->delete();
            $order->additionals()?->detach();
            $order->delete();
        }
        $customer->devices()?->delete();
        $customer->addresses()?->delete();

        $customer->cards()?->delete();

        $customer->notifications()?->delete();

        $customer->delete();

        $user->delete();

        // Always land on the list — after a delete, back() may point to the
        // (now missing) edit page.
        return redirect()->route('customer.index')->with('success', 'تم حذف العميل بنجاح');
    }

    public function toggleStatus(User $user)
    {
        abort_if(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        abort_if($this->isStaffRoleAccount($user), 403, 'هذا الحساب محمي — لا يمكن تعطيل حسابات الإدارة من صفحة العملاء.');
        (new UserRepository())->toggleStatus($user);
        return back()->with('success','تم تحديث الحالة بنجاح');
    }

    public function getCustomerAddresses($customerId)
    {
        $user = auth()->user();
        if ($user && ($user->hasRole('vendor_admin') || $user->hasRole('vendor_sub'))) {
            $vendorId = \App\Support\VendorAccount::idFor($user);
            $customer = Customer::find($customerId);
            if (!$customer) {
                abort(404);
            }
            $hasVendorOrder = $customer->orders()->where('vendor_id', $vendorId)->exists();
            $createdInVendorPos = in_array(
                (int) $customerId,
                array_map('intval', session('pos_customer_ids.' . $vendorId, [])),
                true
            );
            abort_unless($hasVendorOrder || $createdInVendorPos, 403);
        } elseif ($user && $user->hasRole('driver')) {
            $driver = $user->driver;
            $vendorId = $driver?->vendor_id;
            $customer = Customer::find($customerId);
            if (!$customer) {
                abort(404);
            }
            $hasVendorOrder = $customer->orders()->where('vendor_id', $vendorId)->exists();
            $createdInVendorPos = in_array(
                (int) $customerId,
                array_map('intval', session('pos_customer_ids.' . $vendorId, [])),
                true
            );
            abort_unless($hasVendorOrder || $createdInVendorPos, 403);
        }

        $addresses = Address::where('customer_id', $customerId)->get();
        return response()->json($addresses);
    }

}
