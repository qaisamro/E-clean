<?php

namespace App\Http\Controllers\Web\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use App\Models\Driver;
use App\Models\NotificationManage;
use App\Models\Order;
use App\Repositories\DriverRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Services\NotificationServices;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = (new DriverRepository())->getAllActive()->load('orders', 'user');
        if (request()->deactive) {
            $drivers = (new DriverRepository())->getAllDeactive()->load('orders', 'user');
        }
        return view('drivers.index', compact('drivers'));
    }

    public function account()
    {
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            $driver = auth()->user()->driver;
            if (!$driver) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }
            $orders = $driver->orders()->with('customer.user')->get();
            $histories = $driver->orderHistories()->with('customer.user')->get();
            return view('drivers.account', compact('driver', 'orders', 'histories'));
        }
        abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
    }

    public function orderStatusUpdate(Request $request, Order $order)
    {
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            $driver = auth()->user()->driver;

            $assignedOrder = $driver?->orders()->where('orders.id', $order->id)->first();
            if (!$assignedOrder) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'هذا الطلب غير معيّن لك'], 403);
                }
                return back()->with('error', 'هذا الطلب غير معيّن لك');
            }

            $status = config('enums.order_status.' . $request->status);
            if (!in_array($status, config('enums.order_status'))) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'حالة غير صالحة'], 422);
                }
                return back()->with('error', 'حالة غير صالحة');
            }

            $order = (new OrderRepository())->StatusUpdateByRequest($order, $status);

            $manageMap = [
                'pending' => 'new_order',
                'order_confirmed' => 'order_confirmed',
                'picked_order' => 'order_picked',
                'processing' => 'order_processing',
                'cancelled' => 'order_cancelled',
                'delivered' => 'order_delivered',
            ];
            $notificationOrder = NotificationManage::where('name', $manageMap[$request->status] ?? $request->status)->first();

            if ($order->customer && $order->customer->devices->count()) {
                $keys = $order->customer->devices->pluck('key')->toArray();

                if ($notificationOrder?->is_active) {
                    $message = $notificationOrder->message;
                    $title = $notificationOrder->title;
                } else {
                    $message = "مرحبًا {$order->customer->name}، تم تحديث حالة طلبك إلى " . __($status) . ". رقم الطلب: {$order->order_code}";
                    $title = 'تحديث حالة الطلب';
                }

                (new NotificationServices())->sendNotification($message, $keys, $title);
                (new NotificationRepository())->storeByRequest($order->customer->id, $message, $title);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث الحالة بنجاح',
                    'order_status' => $order->order_status,
                ]);
            }

            return back()->with('success', 'تم تحديث الحالة بنجاح');
        }

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك'], 403);
        }
        return back()->with('error', 'غير مصرح لك');
    }

    public function create(Request $request)
    {
        return view('drivers.create');
    }

    public function store(DriverRequest $request)
    {
        $user = (new UserRepository())->registerUser($request);

        $driver = (new DriverRepository())->storeByUser($user);

        $user->assignRole('driver');

        $user->update([
            'mobile_verified_at' => now()
        ]);
        $driver->update([
            'is_approve' => true
        ]);

        return redirect()->route('driver.index')->with('success','تمت إضافة السائق بنجاح');
    }

    public function driverAssign(Order $order, $driver)
    {

        $orderStatus = ($order->order_status == config('enums.order_status.pending') || $order->order_status == config('enums.order_status.order_confirmed')) ? 'pick-up' : 'delivery';
        $order->drivers()->attach($driver,['status' => $orderStatus]);

        $driver = (new DriverRepository())->findById($driver);
        $keys = $driver->driverDevices->pluck('key')->toArray();

        $message = $orderStatus == 'pick-up' ? 'تم تعيينك لطلب استلام. رقم الطلب: LM' . $order->order_code : 'تم تعيينك لطلب تسليم. رقم الطلب: LM' . $order->order_code;

        $notificationManage = NotificationManage::where('name', 'driver_assigned')->first();

        if ($notificationManage?->is_active) {

            (new NotificationServices())->sendNotification($message, $keys, 'طلب جديد');

        }

        return redirect()->back()->with('success','تم تعيين السائق بنجاح');
    }

    public function details(Driver $driver)
    {
        return view('drivers.show', compact('driver'));
    }

    public function toggleStatus(Driver $driver)
    {
        $driver->update([
            'is_approve' => !$driver->is_approve
        ]);
        return back()->with('success','تم تحديث الحالة بنجاح');
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $user = $driver->user;
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'required|numeric|unique:users,mobile,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ];
        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            $media = (new \App\Repositories\MediaRepository())->storeByRequest($request->profile_photo, 'images/customers/', 'customer images', 'image');
            $data['profile_photo_id'] = $media->id;
        }

        $user->update($data);

        return redirect()->route('driver.index')->with('success', 'تم تحديث السائق بنجاح');
    }

    public function destroy(Driver $driver)
    {
        $user = $driver->user;

        // detach orders
        $driver->orders()->detach();
        $driver->orderHistories()->detach();
        $driver->driverDevices()->delete();
        $driver->delete();

        if ($user) {
            // delete related customer data if any, but driver user is separate
            $user->devices()?->delete();
            $user->delete();
        }

        return back()->with('success', 'تم حذف السائق بنجاح');
    }
}
