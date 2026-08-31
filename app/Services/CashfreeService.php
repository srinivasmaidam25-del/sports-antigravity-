<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CashfreeService
{
    protected $clientId;
    protected $clientSecret;
    protected $environment;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.cashfree.client_id');
        $this->clientSecret = config('services.cashfree.client_secret');
        $this->environment = config('services.cashfree.environment', 'sandbox');

        $this->baseUrl = $this->environment === 'production'
            ? 'https://api.cashfree.com/pg'
            : 'https://sandbox.cashfree.com/pg';
    }

    /**
     * Check if the service has valid configurations.
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Create an order session on Cashfree.
     */
    public function createOrder(array $params): array
    {
        if (!$this->isConfigured()) {
            // Mock response for local development when credentials aren't set
            Log::info('Cashfree API Mocking: Creating order', $params);
            
            return [
                'order_id' => $params['order_id'],
                'payment_session_id' => 'mock_session_' . uniqid(),
                'payment_link' => route('payment.mock-checkout', ['order_id' => $params['order_id']]),
                'cf_order_id' => 'mock_cf_' . rand(100000, 999999),
            ];
        }

        try {
            $response = Http::withHeaders([
                'x-client-id' => $this->clientId,
                'x-client-secret' => $this->clientSecret,
                'x-api-version' => '2023-08-01',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/orders", [
                'order_id' => $params['order_id'],
                'order_amount' => $params['amount'],
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id' => $params['customer_id'],
                    'customer_phone' => $params['customer_phone'] ?? '9999999999',
                    'customer_email' => $params['customer_email'] ?? 'customer@thecrickethub.com',
                ],
                'order_meta' => [
                    'return_url' => route('payment.callback') . '?order_id={order_id}',
                    'notify_url' => route('payment.webhook'),
                ],
            ]);

            if ($response->failed()) {
                throw new Exception("Cashfree order creation failed: " . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Cashfree Create Order Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve order details from Cashfree to check status manually.
     */
    public function getOrder(string $orderId): array
    {
        if (!$this->isConfigured()) {
            return [
                'order_id' => $orderId,
                'order_status' => 'PAID', // Mock payment as paid for manual checks
            ];
        }

        $response = Http::withHeaders([
            'x-client-id' => $this->clientId,
            'x-client-secret' => $this->clientSecret,
            'x-api-version' => '2023-08-01',
        ])->get("{$this->baseUrl}/orders/{$orderId}");

        if ($response->failed()) {
            throw new Exception("Failed to fetch Cashfree order: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Validate webhook signature from Cashfree.
     */
    public function verifyWebhookSignature(string $signature, string $payload, string $timestamp): bool
    {
        if (!$this->isConfigured()) {
            return true; // Auto-pass for mock testing
        }

        // Cashfree signature verification formula:
        // Signature = Base64(HMACSHA256(timestamp + payload, client_secret))
        $data = $timestamp . $payload;
        $hash = hash_hmac('sha256', $data, $this->clientSecret, true);
        $expectedSignature = base64_encode($hash);

        return hash_equals($expectedSignature, $signature);
    }
}
