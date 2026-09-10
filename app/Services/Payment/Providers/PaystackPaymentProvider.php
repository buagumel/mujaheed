<?php

namespace App\Services\Payment\Providers;

use App\Models\User;
use App\Services\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackPaymentProvider implements PaymentProviderInterface
{
    protected string $secretKey;
    protected string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key', env('PAYSTACK_SECRET_KEY', ''));
    }

    public function initializePayment(User $user, float $amount, string $reference, string $callbackUrl): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->post("{$this->baseUrl}/transaction/initialize", [
                    'email' => $user->email,
                    'amount' => (int) round($amount * 100), // In Kobo
                    'reference' => $reference,
                    'callback_url' => $callbackUrl,
                    'metadata' => [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                    ],
                ]);

            $json = $response->json();

            if ($response->successful() && ($json['status'] ?? false)) {
                return [
                    'success' => true,
                    'payment_url' => $json['data']['authorization_url'],
                    'reference' => $reference,
                    'message' => 'Paystack initialized successfully.',
                ];
            }

            return [
                'success' => false,
                'payment_url' => '',
                'reference' => $reference,
                'message' => $json['message'] ?? 'Failed to initialize Paystack transaction.',
            ];
        } catch (\Throwable $e) {
            Log::error('Paystack initialization error: ' . $e->getMessage());
            return [
                'success' => false,
                'payment_url' => '',
                'reference' => $reference,
                'message' => 'Unable to connect to Paystack payment gateway.',
            ];
        }
    }

    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/transaction/verify/{$reference}");

            $json = $response->json();

            if ($response->successful() && ($json['status'] ?? false)) {
                $data = $json['data'];
                $status = $data['status'] === 'success' ? 'successful' : ($data['status'] === 'failed' ? 'failed' : 'pending');

                return [
                    'success' => $status === 'successful',
                    'status' => $status,
                    'amount' => ((float) $data['amount']) / 100,
                    'channel' => $data['channel'] ?? 'card',
                    'provider_reference' => (string) ($data['id'] ?? ''),
                    'raw_response' => $data,
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'amount' => 0.0,
                'channel' => 'unknown',
                'provider_reference' => '',
                'raw_response' => $json,
            ];
        } catch (\Throwable $e) {
            Log::error('Paystack verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'failed',
                'amount' => 0.0,
                'channel' => 'unknown',
                'provider_reference' => '',
                'raw_response' => ['error' => $e->getMessage()],
            ];
        }
    }

    public function handleWebhook(array $payload, ?string $signatureHeader = null): array
    {
        // Verify HMAC signature
        if ($signatureHeader) {
            $expectedSignature = hash_hmac('sha512', file_get_contents('php://input'), $this->secretKey);
            if (!hash_equals($expectedSignature, $signatureHeader)) {
                return ['verified' => false, 'event' => '', 'reference' => '', 'amount' => 0, 'status' => 'unauthorized'];
            }
        }

        $event = $payload['event'] ?? '';
        $data = $payload['data'] ?? [];

        return [
            'verified' => true,
            'event' => $event,
            'reference' => $data['reference'] ?? '',
            'amount' => isset($data['amount']) ? ((float) $data['amount']) / 100 : 0.0,
            'status' => ($data['status'] ?? '') === 'success' ? 'successful' : 'failed',
        ];
    }
}
