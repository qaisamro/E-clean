<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentGateway::truncate();
        $paymentMethods = [
            [
                'title'             => 'Stripe',
                'name'              => 'stripe',
                'config'            => json_encode([
                    'secret_key'    => 'YOUR_STRIPE_SECRET_KEY',
                    'published_key' => 'YOUR_STRIPE_PUBLISHABLE_KEY',
                ]),
                'mode'              => 'test',
                'alias'             => 'stripe',
                'is_active'         => true,
            ],
            [
                'title'             => 'Razorpay',
                'name'              => 'razorpay',
                'config'            => json_encode([
                    'key'           => 'YOUR_RAZORPAY_KEY',
                    'secret'        => 'YOUR_RAZORPAY_SECRET',
                ]),
                'mode'              => 'test',
                'alias'             => 'razorpay',
                'is_active'         => true,
            ],
            [
                'title'             => 'Paystack',
                'name'              => 'paystack',
                'config'            => json_encode([
                    'public_key'    => 'YOUR_PAYSTACK_PUBLIC_KEY',
                    'secret_key'    => 'YOUR_PAYSTACK_SECRET_KEY',
                    'machant_email' => '',
                ]),
                'mode'              => 'test',
                'alias'             => 'paystack',
                'is_active'         => true,
            ],
        ];

        PaymentGateway::insert($paymentMethods);
    }
}
