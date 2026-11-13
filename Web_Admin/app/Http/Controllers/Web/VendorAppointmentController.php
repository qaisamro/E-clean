<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\{Order, OrderAppointment, Vendor};
use App\Services\VendorAppointments;
use App\Support\OrderVendorOwnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorAppointmentController extends Controller
{
    private function vendor(): Vendor
    {
        abort_unless(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        $vendor = \App\Support\VendorAccount::vendorFor(auth()->user());
        abort_unless($vendor, 403);
        return $vendor;
    }

    public function edit(VendorAppointments $service)
    {
        $vendor = $this->vendor();
        $settings = $service->settings($vendor->id);
        $orders = Order::where('vendor_id', $vendor->id)->whereNotIn('order_status', ['Cancelled', 'Delivered'])
            ->whereDate('delivery_date', '>=', now(VendorAppointments::ZONE)->toDateString())->latest()->limit(100)->get();
        return response()->view('vendor.appointments.edit', compact('vendor', 'settings', 'orders'))
            ->header('Cache-Control', 'private, no-store');
    }

    public function update(Request $request, VendorAppointments $service)
    {
        $vendor = $this->vendor();
        $request->validate(['settings' => 'required|array']);
        $service->save($vendor->id, $request->input('settings'));
        return response()->json(['message' => 'تم حفظ الجدول. الحجوزات الحالية محفوظة دون تغيير.']);
    }

    public function reschedule(Request $request, Order $order, VendorAppointments $service)
    {
        $vendor = $this->vendor();
        abort_unless((int) $order->vendor_id === $vendor->id && OrderVendorOwnership::productsMatchOrder($order), 404);
        $v = $request->validate(['pick_date' => 'required|date_format:Y-m-d', 'pick_hour' => 'required|date_format:H:i',
            'delivery_date' => 'required|date_format:Y-m-d', 'delivery_hour' => 'required|date_format:H:i',
            'reason' => 'required|string|min:3|max:500', 'customer_agreed' => 'accepted']);
        DB::transaction(function () use ($vendor, $order, $service, $v) {
            Vendor::whereKey($vendor->id)->lockForUpdate()->firstOrFail();
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($order->order_status, ['Pending', 'Order confirmed'], true), 422, 'لا يمكن إعادة جدولة طلب بدأ تنفيذه.');
            abort_unless($service->settings($vendor->id)['enabled'], 422, 'فعّل جدول المتجر أولاً.');
            $pair = $service->validatePair($vendor->id, $v, $order->id);
            $booking = OrderAppointment::where('order_id', $order->id)->first();
            $history = $booking?->reschedule_history ?? [];
            $history[] = ['at' => now('UTC')->toIso8601String(), 'actor_id' => auth()->id(), 'reason' => $v['reason'],
                'previous' => $order->only(['pick_date', 'pick_hour', 'delivery_date', 'delivery_hour'])];
            $order->update(['pick_date' => $v['pick_date'], 'pick_hour' => $v['pick_hour'].':00',
                'delivery_date' => $v['delivery_date'], 'delivery_hour' => $v['delivery_hour'].':00']);
            $service->snapshot($order, $pair, $history);
        }, 3);
        return back()->with('success', 'تمت إعادة الجدولة بعد موافقة العميل، دون تغيير المبالغ.');
    }
}
