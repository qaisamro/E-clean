<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Support\StripeCheckoutSessionVerifier;
use Tests\TestCase;

class StripeCheckoutSessionVerifierTest extends TestCase
{
    private function order(): Order
    {
        $order = new Order();
        $order->id = 42;
        $order->total_amount = '19.95';

        return $order;
    }

    private function paidSession(array $overrides = []): object
    {
        return (object) array_replace([
            'status' => 'complete',
            'payment_status' => 'paid',
            'client_reference_id' => '42',
            'amount_total' => 1995,
            'currency' => 'usd',
            'metadata' => (object) [
                'order_id' => '42',
                'amount_minor' => '1995',
                'currency' => 'usd',
            ],
        ], $overrides);
    }

    public function test_accepts_paid_session_matching_order_amount_and_currency(): void
    {
        $verifier = new StripeCheckoutSessionVerifier();

        $this->assertTrue($verifier->matchesOrder($this->paidSession(), $this->order()));
    }

    public function test_accepts_a_legacy_session_only_for_its_original_order_url(): void
    {
        $session = $this->paidSession([
            'client_reference_id' => null,
            'success_url' => route('stripe.success', ['order' => 42]) . '?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => (object) [],
        ]);
        $verifier = new StripeCheckoutSessionVerifier();

        $this->assertTrue($verifier->matchesOrder($session, $this->order()));
        $session->success_url = route('stripe.success', ['order' => 43]) . '?session_id={CHECKOUT_SESSION_ID}';
        $this->assertFalse($verifier->matchesOrder($session, $this->order()));
    }

    public function test_rejects_unpaid_or_incomplete_sessions(): void
    {
        $verifier = new StripeCheckoutSessionVerifier();

        $this->assertFalse($verifier->matchesOrder(
            $this->paidSession(['payment_status' => 'unpaid']),
            $this->order()
        ));
        $this->assertFalse($verifier->matchesOrder(
            $this->paidSession(['status' => 'open']),
            $this->order()
        ));
    }

    public function test_rejects_a_session_for_another_order(): void
    {
        $verifier = new StripeCheckoutSessionVerifier();

        $this->assertFalse($verifier->matchesOrder(
            $this->paidSession(['client_reference_id' => '43']),
            $this->order()
        ));
    }

    public function test_rejects_amount_or_currency_mismatches(): void
    {
        $verifier = new StripeCheckoutSessionVerifier();

        $this->assertFalse($verifier->matchesOrder(
            $this->paidSession(['amount_total' => 1000]),
            $this->order()
        ));

        $this->assertFalse($verifier->matchesOrder(
            $this->paidSession([
                'metadata' => (object) [
                    'order_id' => '42',
                    'amount_minor' => '1995',
                    'currency' => 'eur',
                ],
            ]),
            $this->order()
        ));
    }
}
