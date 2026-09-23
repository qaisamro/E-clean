<?php

namespace App\Http\Controllers\Web\Products;

use App\Events\OrderMailEvent;
use App\Events\UserMailEvent;
use PDF;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeliveryCost;
use App\Models\InvoiceManage;
use App\Models\NotificationManage;
use App\Models\WebSetting;
use App\Repositories\DeviceKeyRepository;
use App\Repositories\DriverRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Services\NotificationServices;

class OrderController extends Controller
{
    private $orderRepo;
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepo = $orderRepository;
    }

    public function index(Request $request)
    {
        $orders = $this->orderRepo->getSortedByRequest($request);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // للسائق: السماح فقط برؤية طلبه المعيّن
        if (auth()->check() && auth()->user()->hasRole('driver')) {
            $driver = auth()->user()->driver;
            $allowedIds = collect();
            if ($driver) {
                $allowedIds = $driver->orders->pluck('id')->merge($driver->orderHistories->pluck('id'));
            }
            if (!$allowedIds->contains($order->id)) {
                abort(403, 'غير مصرح لك بعرض هذا الطلب');
            }
        }

        $quantity = 0;
        foreach ($order->products as $product) {
            $quantity += $product->pivot->quantity;
        }

        $drivers = (new DriverRepository())->getAll();
        $order->update([
            'is_show' => true
        ]);
        return view('orders.show', compact('order', 'quantity', 'drivers'));
    }

    public function statusUpdate(Request $request, Order $order)
    {
        $status = config('enums.order_status.' . $request->status);

        if (!in_array($status, config('enums.order_status'))) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حالة غير صالحة'], 422);
            }
            return back()->with('error', 'حالة غير صالحة');
        }

        $order = $this->orderRepo->StatusUpdateByRequest($order, $status);

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
            $devices = $order->customer->devices;
            $keys = $devices->pluck('key')->toArray();

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

    public function orderPaid(Request $request, Order $order)
    {
        $order->update([
            'payment_status' => config('enums.payment_status.paid')
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم دفع الطلب بنجاح',
                'payment_status' => $order->payment_status,
            ]);
        }

        return back()->with('success', 'تم دفع الطلب بنجاح');
    }

    public function printLabels(Order $order)
    {
        @ini_set('memory_limit', '512M');
        $productLabels = collect([]);
        $t = 1;
        foreach ($order->products as $key => $product) {
            for ($i = 0; $i < $product->pivot->quantity; $i++) {
                $productLabels[]    = [
                    'name' => $order->customer->user->name,
                    'code' => $order->order_code,
                    'date' => Carbon::parse($order->delivery_date)->format('M d, Y'),
                    'title' => $product->name,
                    'label' => $t . '/' . \request('quantity'),
                ];
                $t++;
            }
        }

        $labels = [];
        $i = 0;
        $r = 0;

        foreach ($productLabels as $key => $label) {
            if ($key + 1 == 1 || $key + 1 == $i) {
                $labels[$r] = [];
                $i = $key + 1 == 1 ? $i + 4 : $i + 3;
                $r++;
            }
            $labels[$r - 1][] = $label;
        }

        $pdf = PDF::loadView('pdf.generate-label', compact('labels'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('labels_' . now()->format('H-i-s') . '.pdf');
    }

    public function printInvioce(Order $order)
    {
        @ini_set('memory_limit', '512M');
        $quantity = 0;
        foreach ($order->products as $product) {
            $quantity += $product->pivot->quantity;
        }

        $deliveryCost = DeliveryCost::first();
        $webSetting = WebSetting::first();
        $invoice = InvoiceManage::first();

        if (!$webSetting || !$webSetting->address) {
            return redirect()->route('webSetting.index')->with('error', 'يرجى استكمال إعدادات الموقع');
        }

        if ($invoice?->type == 'pos') {

            return view('pdf.posIvoice', compact('quantity', 'order', 'deliveryCost', 'webSetting'));
        }

        $pdf = PDF::loadView('pdf.invoice', compact('order', 'quantity', 'deliveryCost', 'webSetting'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($order->order_code . ' - invioce.pdf');
    }
}
