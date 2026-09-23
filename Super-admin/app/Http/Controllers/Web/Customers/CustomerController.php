<?php

namespace App\Http\Controllers\Web\Customers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\CustomerRepository;
use App\Http\Requests\RegistrationRequest;
use App\Models\Address;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {

        $customers = (new CustomerRepository())->getAllOrFindBySearch();
       
        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        // للسوبر أدمن فقط - منع التجار من رؤية العملاء
        if (auth()->check() && auth()->user()->hasRole('vendor_admin')) {
            abort(403, 'غير مصرح لك بعرض العملاء - للسوبر أدمن فقط');
        }
        // للسائق: السماح فقط برؤية عملائه المرتبطين بطلباته
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            $driver = auth()->user()->driver;
            $allowedIds = collect();
            if ($driver) {
                $driverOrderIds = $driver->orders->pluck('id')->merge($driver->orderHistories->pluck('id'))->unique();
                $allowedIds = \App\Models\Order::whereIn('id', $driverOrderIds)->pluck('customer_id')->unique();
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
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'mobile' => "required|numeric|unique:users,mobile," . $customer->user->id,
            'email' => "nullable|email|unique:users,email," . $customer->user->id,
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
        (new UserRepository())->updateProfileByRequest($request, $customer->user);

        return redirect()->route('customer.index')->with('success', 'تم تحديث العميل بنجاح');
    }

    public function delete(Customer $customer)
    {
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

        return back()->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function toggleStatus(User $user)
    {
        (new UserRepository())->toggleStatus($user);
        return back()->with('success','تم تحديث الحالة بنجاح');
    }

    public function getCustomerAddresses($customerId)
{
    $addresses = Address::where('customer_id', $customerId)->get();
    return response()->json($addresses);
}

}
