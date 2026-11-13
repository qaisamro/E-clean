<?php

namespace App\Services;

use GuzzleHttp\Client;
use RuntimeException;

class OrangePayService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client([
            'connect_timeout' => 5,
            'timeout' => 15,
        ]);
        $this->apiKey = config('services.orange_pay.api_key');
        $this->baseUrl = rtrim((string) config('services.orange_pay.base_url'), '/');
    }

    public function createPayment($amount, $currency, $description)
    {
        if (!is_string($this->apiKey) || trim($this->apiKey) === '') {
            throw new RuntimeException('OrangePay is not configured.');
        }

        $response = $this->client->post($this->baseUrl . '/payments', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
