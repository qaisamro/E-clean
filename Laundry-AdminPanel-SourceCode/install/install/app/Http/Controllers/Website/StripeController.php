<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function stripeSuccess(Order $order)
    {
        $order->update([
            'payment_status' => 'Paid',
            'order_status' => 'Order confirmed'
        ]);

        return redirect()->route('order.details', $order->id)->with('success', 'Payment successful!');
    }

    public function stripeCancel(Order $order)
    {
        $order->update([
            'payment_status' => 'pending',
            'order_status' => 'Cancelled'
        ]);
        return redirect()->route('order.details', $order->id)->with('error', 'Payment cancelled. Please try again.');
    }

    /**
     * Order failure page
     */
    public function orderFailure(Order $order)
    {
        return view('website.pages.order-failure', compact('order'));
    }
}
