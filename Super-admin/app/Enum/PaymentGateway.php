<?php

namespace App\Enum;

enum PaymentGateway: string
{
    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';
    case PAYSTACK = 'paystack';
    case PAYTAB = 'paytab';
    case RAZORPAY = 'razorpay';
    case CASH = 'cash';
}
