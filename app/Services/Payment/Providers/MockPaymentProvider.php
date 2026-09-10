<?php

namespace App\Services\Payment\Providers;

use App\Models\User;
use App\Services\Payment\PaymentProviderInterface;
use Illuminate\Support\Str;

class MockPaymentProvider implements PaymentProviderInterface
{
    public function initializePayment(User $user, float $amount, string $reference, string $callbackUrl): array
    {
        $mockUrl = route('wallet.fund.mock.checkout', ['reference' => $reference]);

        return [
            'success' => true,
            'payment_url' => $mockUrl,
            'reference' => $reference,
            'message' => 'Mock payment gateway initialized.',
        ];
    }

    public function verifyPayment(string $reference): array
    {
        return [
            'success' => true,
            'status' => 'successful',
            'amount' => 0.00, // Will be matched with database transaction
            'channel' => 'mock_card',
            'provider_reference' => 'MOCK-PAY-' . strtoupper(Str::random(12)),
            'raw_response' => [
                'status' => 'success',
                'gateway' => 'mock',
                'reference' => $reference,
            ],
        ];
    }

    public function handleWebhook(array $payload, ?string $signatureHeader = null): array
    {
        return [
            'verified' => true,
            'event' => 'charge.success',
            'reference' => $payload['data']['reference'] ?? ($payload['reference'] ?? ''),
            'amount' => (float) ($payload['data']['amount'] ?? ($payload['amount'] ?? 0)),
            'status' => 'successful',
        ];
    }
}
