<?php


namespace App\Repositories;

use App\Http\Requests\CardRequest;
use Stripe\Token;
use Stripe\Stripe;
use Stripe\Customer;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\StripeKey;
use Stripe\Checkout\Session;

class PaymentRepository extends Repository
{
    public function __construct()
    {
        $stripeKey = StripeKey::first();
        Stripe::setApiKey($stripeKey?->secret_key);
    }

    public function model()
    {
        return Payment::class;
    }

    public function getAll()
    {
        $customer = auth()->user()->customer;
        return $this->query()->where('customer_id', $customer->id)->get();
    }

    public function storeByRequest(array $payment, $order): Payment
    {
        return $this->create([
            'order_id' => $order->id,
            'object' => $payment['object'],
            'brand' => $payment['brand'],
            'status' => $payment['status'],
            'exp' => $payment['exp'],
            'last_no' => $payment['last_no'],
            'transaction' => $payment['transaction'],
            'amount' => $payment['amount'],
        ]);
    }

    public function storeByRequestFromApi($request): Payment
    {
        return $this->create([
            'order_id' => $request->order_id,
            'object' => $request->object,
            'brand' => $request->brand,
            'status' => $request->status,
            'exp' => $request->exp,
            'last_no' => $request->last_no,
            'transaction' => $request->transaction,
            'amount' => $request->amount,
        ]);
    }

    public function makeCustomer(string $name, string $email = null)
    {
        $info = ['name' => $name];
        if($email)
            $info['email'] = $email;

        $customer = Customer::create($info);
        return $customer;
    }

    public function cardSave(CardRequest $request, string $stripeCustomer)
    {
        $token = Token::create([
                    'card' => [
                        'number' => $request->number,
                        'exp_month' => $request->exp_month,
                        'exp_year' => $request->exp_year,
                        'cvc' => $request->cvc,
                        'address_zip' => $request->zip
                    ],
                ]);

        $card = Customer::createSource(
                    $stripeCustomer,
                    ['source' => $token->id]
                );
        return $card;
    }

    public function deleteSource($stripeCustomer, $cardId)
    {
        Customer::deleteSource(
            $stripeCustomer,
            $cardId
        );
        return true;
    }

    public function getCardCustomerWise(string $stripeCustomer)
    {
        $methods = Customer::allPaymentMethods($stripeCustomer, ['type' => 'card']);
        return $methods;
    }

    public function stripePaymentURL($payload, $orderId)
    {
        $stripeGateway  = PaymentGateway::where('name', 'stripe')->first();
        $stripe_secret_key = optional(json_decode($stripeGateway->config))->secret_key ?? '';
        Stripe::setApiKey($stripe_secret_key);

        $productData = $payload['product_data'] ?? null;
        if (is_string($productData) && $productData !== '') {
            $productData = ['name' => $productData];
        } elseif (is_array($productData)) {
            $productData = array_key_exists('name', $productData)
                ? $productData
                : ['name' => $productData[0] ?? 'Laundry Product'];
        } else {
            $productData = ['name' => 'Laundry Product'];
        }

        try{
            $response = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($payload['currency'] ?? 'usd'),
                        'product_data' => $productData,
                        'unit_amount' => (int) ($payload['amount'] * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.success', $orderId) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel', $orderId),
            ]);

            return $response->url;
        } catch (\Exception $e) {
            throw new \Exception("Stripe Payment Initialization Failed: " . $e->getMessage());
        }
    }
}
