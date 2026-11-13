<?php

namespace App\Http\Controllers\Web\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use App\Models\Driver;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Services\OrderStatusNotificationService;
use App\Support\StoreOrderAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    public function index()
    {
        $query = Driver::with(['user', 'orders']);
        if ($this->isVendorAdmin()) {
            // The inactive-drivers list is not available to stores.
            abort_if(request()->boolean('deactive'), 404);

            $vendorId = auth()->user()->vendor?->id;
            $query = $vendorId
                ? $query->where('vendor_id', $vendorId)
                : $query->whereRaw('1 = 0');
        }

        if (request()->boolean('deactive')) {
            $query->whereHas('user', fn ($users) => $users->where('is_active', false));
        } else {
            $query->whereHas('user', fn ($users) => $users->where('is_active', true));
        }

        $drivers = $query->latest('id')->get();
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(DriverRequest $request)
    {
        $vendorId = $this->isVendorAdmin()
            ? auth()->user()->vendor?->id
            : ($request->filled('vendor_id') ? (int) $request->vendor_id : null);
        if ($this->isVendorAdmin()) {
            abort_unless($vendorId, 403);
        }

        $user = (new UserRepository())->registerUser($request);
        $user->assignRole('driver');

        Driver::create([
            'user_id' => $user->id,
            'vendor_id' => $vendorId,
            'is_approve' => true,
        ]);

        return redirect()->route('driver.index')->with('success', 'تم إنشاء السائق بنجاح');
    }

    public function edit(Driver $driver)
    {
        $this->assertDriverScope($driver);
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $this->assertDriverScope($driver);
        $user = $driver->user;
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile' => ['nullable', 'numeric', Rule::unique('users', 'mobile')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        (new UserRepository())->updateProfileByRequest($request, $user);
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('driver.index')->with('success', 'تم تحديث السائق بنجاح');
    }

    public function destroy(Driver $driver)
    {
        $this->assertDriverScope($driver);

        DB::transaction(function () use ($driver) {
            $driver->driverDevices()->delete();
            $driver->orders()->detach();
            $driver->orderHistories()->detach();
            $driver->user?->delete();
            $driver->delete();
        });

        return redirect()->route('driver.index')->with('success', 'تم حذف السائق');
    }

    public function details(Driver $driver)
    {
        $this->assertDriverScope($driver);
        $driver->load(['user', 'orders', 'orderHistories']);
        return view('drivers.show', compact('driver'));
    }

    public function toggleStatus(Driver $driver)
    {
        $this->assertDriverScope($driver);
        $driver->update(['is_approve' => !$driver->is_approve]);
        return back()->with('success', 'تم تحديث الحالة');
    }

    public function driverAssign(Order $order, Driver $driver)
    {
        StoreOrderAccess::assertCanMutate($order);
        abort_unless($driver->is_approve && $driver->user?->is_active, 404);
        abort_unless(
            $order->vendor_id
                && $driver->vendor_id
                && (int) $driver->vendor_id === (int) $order->vendor_id,
            404
        );
        $this->assertDriverScope($driver);

        $status = request()->input('status', 'delivery');
        abort_unless(in_array($status, ['delivery', 'pick-up'], true), 422);
        $order->drivers()->syncWithoutDetaching([
            $driver->id => ['status' => $status, 'is_accept' => false],
        ]);

        return back()->with('success', 'تم إسناد الطلب للسائق');
    }

    public function account()
    {
        $user = auth()->user();
        abort_unless($user && $user->hasRole('driver'), 403);
        $driver = $user->driver;
        abort_unless($driver && $driver->vendor_id && $driver->is_approve && $user->is_active, 403);

        $orders = $driver->orders()
            ->where('orders.vendor_id', $driver->vendor_id)
            ->with(['products', 'address', 'customer.user'])
            ->latest('orders.id')
            ->get()
            ->filter(fn (Order $order) => \App\Support\OrderVendorOwnership::productsMatchOrder($order))
            ->values();
        $histories = $driver->orderHistories()
            ->with(['products', 'address', 'customer.user'])
            ->latest('driver_histories.created_at')
            ->get()
            ->filter(fn (Order $order) => \App\Support\OrderVendorOwnership::productsMatchOrder($order))
            ->values();

        return view('drivers.account', compact('driver', 'orders', 'histories'));
    }

    public function orderStatusUpdate(Request $request, Order $order)
    {
        $user = $request->user();
        abort_unless($user && $user->hasRole('driver'), 403);
        abort_unless($user->is_active && $user->driver?->is_approve, 403);
        StoreOrderAccess::assertCanMutate($order);

        // Drivers may only move an order forward through the delivery flow:
        // Picked (تم استلام طلبك) → On the way (بالطريق اليك) → Delivered (تم التسليم).
        $statusKey = $request->input('status');
        $driverAllowed = ['picked_order', 'on_the_way', 'delivered'];
        if (!in_array($statusKey, $driverAllowed, true)) {
            return response()->json([
                'success' => false,
                'message' => 'يمكن للسائق تغيير الحالة إلى (تم استلام طلبك / بالطريق اليك / تم التسليم) فقط',
            ], 422);
        }

        $status = config('enums.order_status.' . $statusKey);
        if (!$status || !in_array($status, config('enums.order_status'), true)) {
            return response()->json([
                'success' => false,
                'message' => 'حالة غير صالحة',
            ], 422);
        }

        $order = (new OrderRepository())->statusUpdateByRequest($order, $status);
        app(OrderStatusNotificationService::class)->notify($order, $statusKey);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب',
            'order_status' => $status,
        ]);
    }

    protected function isVendorAdmin(): bool
    {
        return auth()->check() && \App\Support\VendorAccount::isStoreStaff(auth()->user());
    }

    protected function assertDriverScope(Driver $driver): void
    {
        if ($this->isVendorAdmin()) {
            $vendorId = auth()->user()->vendor?->id;
            abort_unless($vendorId && (int) $driver->vendor_id === (int) $vendorId, 404);
        }
    }
}
