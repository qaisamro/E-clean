<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\StripeCheckoutSessionVerifier;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function stripeSuccess(
        Request $request,
        Order $order,
        StripeCheckoutSessionVerifier $verifier
    )
    {
        $sessionId = $request->query('session_id');
        if (!is_string($sessionId) || $sessionId === '' || strlen($sessionId) > 255
            || !$verifier->verify($order, $sessionId)) {
            return redirect()->route('web.home')
                ->with('error', 'تعذر التحقق من الدفع؛ لم يتم تحديث حالة الطلب.');
        }

        $order->update([
            'payment_status' => 'Paid',
            'order_status' => 'Order confirmed'
        ]);

        return redirect()->route('order.details', $order->id)->with('success', 'عملية الدفع ناجحة!');
    }

    public function stripeCancel(Order $order)
    {
        return redirect()->route('web.home')
            ->with('error', 'تم إلغاء الدفع. لم يتم تغيير حالة الطلب.');
    }

    /**
     * Order failure page
     */
    public function orderFailure(Order $order)
    {
        $customer = auth()->user() ? auth()->user()->customer : null;
        abort_unless($customer && (int) $order->customer_id === (int) $customer->id, 404);

        return view('website.pages.order-failure', compact('order'));
    }
}
