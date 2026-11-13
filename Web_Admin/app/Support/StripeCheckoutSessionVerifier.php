<?php

namespace App\Support;

use App\Models\Order;
use App\Models\PaymentGateway;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeCheckoutSessionVerifier
{
    /**
     * Retrieve the checkout session from Stripe and verify that it is a paid
     * session for this exact order and amount.
     */
    public function verify(Order $order, string $sessionId): bool
    {
        if (!preg_match('/^cs_(test|live)_/', $sessionId)) {
            return false;
        }

        $gateway = PaymentGateway::where('name', 'stripe')->first();
        $secretKey = optional(json_decode(optional($gateway)->config))->secret_key;
        if (!is_string($secretKey) || $secretKey === '') {
            return false;
        }

        Stripe::setApiKey($secretKey);

        try {
            $session = Session::retrieve($sessionId);
        } catch (\Throwable $exception) {
            return false;
        }

        return $this->matchesOrder($session, $order);
    }

    /**
     * Keep the matching rules separate so they can be tested without making
     * network calls to Stripe.
     *
     * @param mixed $session
     */
    public function matchesOrder($session, Order $order): bool
    {
        $orderId = (string) $order->id;
        $clientReferenceId = (string) data_get($session, 'client_reference_id', '');
        $metadataOrderId = (string) data_get($session, 'metadata.order_id', '');
        $metadataAmount = data_get($session, 'metadata.amount_minor');
        $metadataCurrency = strtolower((string) data_get($session, 'metadata.currency', ''));
        $sessionAmount = data_get($session, 'amount_total');
        $sessionCurrency = strtolower((string) data_get($session, 'currency', ''));
        $expectedAmount = (int) round(((float) $order->total_amount) * 100);

        $hasNewOrderBinding = $clientReferenceId !== ''
            || $metadataOrderId !== ''
            || $metadataAmount !== null
            || $metadataCurrency !== '';

        $isBoundToOrder = $hasNewOrderBinding
            ? $clientReferenceId === $orderId
                && $metadataOrderId === $orderId
                && is_numeric($metadataAmount)
                && (int) $metadataAmount === $expectedAmount
                && $metadataCurrency !== ''
                && $sessionCurrency === $metadataCurrency
            : $this->legacySessionSuccessUrlMatchesOrder($session, $orderId);

        return $isBoundToOrder
            && data_get($session, 'status') === 'complete'
            && data_get($session, 'payment_status') === 'paid'
            && is_numeric($sessionAmount)
            && (int) $sessionAmount === $expectedAmount
            && $sessionCurrency !== '';
    }

    /**
     * Existing Checkout Sessions created before order metadata was added can
     * still be verified by their immutable Stripe success URL and exact total.
     */
    private function legacySessionSuccessUrlMatchesOrder($session, string $orderId): bool
    {
        $successUrl = data_get($session, 'success_url');
        if (!is_string($successUrl) || $successUrl === '') {
            return false;
        }

        $successPath = parse_url($successUrl, PHP_URL_PATH);
        $expectedPath = parse_url(
            route('stripe.success', ['order' => $orderId]),
            PHP_URL_PATH
        );

        return is_string($successPath)
            && is_string($expectedPath)
            && $successPath === $expectedPath;
    }
}
