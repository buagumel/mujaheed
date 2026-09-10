<?php

namespace App\Services\Payment;

use App\Models\User;

interface PaymentProviderInterface
{
    /**
     * Initialize payment
     *
     * @param User $user
     * @param float $amount
     * @param string $reference
     * @param string $callbackUrl
     * @return array ['success' => bool, 'payment_url' => string, 'reference' => string, 'message' => string]
     */
    public function initializePayment(User $user, float $amount, string $reference, string $callbackUrl): array;

    /**
     * Verify payment status server-side
     *
     * @param string $reference
     * @return array ['success' => bool, 'status' => string, 'amount' => float, 'channel' => string, 'provider_reference' => string, 'raw_response' => array]
     */
    public function verifyPayment(string $reference): array;

    /**
     * Handle and verify Webhook
     *
     * @param array $payload
     * @param string|null $signatureHeader
     * @return array ['verified' => bool, 'event' => string, 'reference' => string, 'amount' => float, 'status' => string]
     */
    public function handleWebhook(array $payload, ?string $signatureHeader = null): array;
}
