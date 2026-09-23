<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use App\Repositories\PaymentGatewayRepository;
use App\Http\Requests\PaymentGatewayRequest;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\StripePayService;
use App\Services\RazorPayService;
use App\Services\PaystackService;


use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;



use App\Models\WebSetting;

class PaymentGatewayController extends Controller
{
    public function __construct(
        protected StripePayService $stripeService,
        protected RazorPayService $razorpayService,
        protected PaystackService $paystackService
    ) {
    }

    /**
     * Show payment gateway
     */
    public function index()
    {


        $paymentGateways = PaymentGateway::get();
        return $paymentGateways;

        //return($paymentGateways);


        //    return view('paymentGateway.index', compact('paymentGateways'));
    }


    public function getByID(Request $request)
{
    $paymentGateway = PaymentGateway::findOrFail($request->id);

    $webSetting = WebSetting::first();


    $order = Order::latest()->first();

    if (!$order) {
        return response()->json([
            'success' => false,
            'message' => 'Order not found'
        ], 404);
    }

    $orderCode = $order->order_code ?? $order->id;
     $grandTotal = (float) $order->total_amount;

     $config = json_decode($paymentGateway->config, true);

    if ($paymentGateway->name->value === 'stripe') {

         if (!$config || empty($config['secret_key'])) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe configuration not found'
            ], 400);
        }

        try {
            Stripe::setApiKey($config['secret_key']);

            $currency_name = strtolower($webSetting->currency_name ?? 'usd');

            if ($currency_name === 'bdt') {
                $currency_name = 'usd';
            }

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $currency_name,
                            'unit_amount' => (int) round($grandTotal * 100),
                            'product_data' => [
                                'name' => "Order #{$orderCode}",
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                // 'success_url' => url('/api/stripe/success?session_id={CHECKOUT_SESSION_ID}'),
                'success_url' => url('/stripe/success/' . $order->id),
                'cancel_url' => url('/api/stripe/cancel'),
            ]);

            return response()->json([
                'success' => true,
                'checkout_url' => $session->url,
                'session_id' => $session->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    return response()->json([
        'success' => false,
        'message' => 'Unsupported payment gateway'
    ], 400);

}


    public function processOrder(Request $request, $order)
    {
        $paymentGateway = PaymentGateway::where('name', $request->payment_method)->first();
        $config = json_decode($paymentGateway->config);

        $request['paid_amount'] = $request->total_amount ?? null;
        $request['description'] = $order->instruction ?? null;
        $request['mode'] = $paymentGateway->mode ?? null;

        $this->{$request->payment_method . 'Service'}->paymentProcess($request, $config);

        $transactionRepo = new TransactionRepository();
        $transactionRepo->updateWhenComplatePay($order, $request->payment_method);

        return response()->json([
            'success' => true,
            'message' => 'Payment successfully processed',
        ], 200);



    }
    public function success()
    {
        return 'payment success';
    }

}
