<?php

namespace App\Services;

use App\Models\PaymentGateway;
use APP\Models\WebSetting;
use Stripe;

class StripePayService
{
    public function paymentProcess($request, $config)
    {
        $webSetting = WebSetting::first();

        $paymentGateway = PaymentGateway::where('name', $request->payment_method)->first();
        $config  = json_decode($paymentGateway->config);

        Stripe\Stripe::setApiKey($config->secret_key);
        $currency = strtoupper(WebSetting::first()->currency_code ?? 'USD');
        $result = Stripe\Charge::create([
            "amount"  => $request->paid_amount * 100,
            "currency" => $webSetting?->currency ?? 'USD',
            "source" => $request->token_id,
            "description" => $request->description ?? '',
        ]);

        if(($result['status'] == 'succeeded')):
            return true;
        else:
            return false;
        endif;
    }
}

