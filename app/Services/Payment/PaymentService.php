<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Payment\Providers\MockPaymentProvider;
use App\Services\Payment\Providers\PaystackPaymentProvider;
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    protected PaymentProviderInterface $provider;
    protected WalletService $walletService;
    protected NotificationService $notificationService;

    public function __construct(WalletService $walletService, NotificationService $notificationService)
    {
        $this->walletService = $walletService;
        $this->notificationService = $notificationService;

        $driver = config('services.payment_provider', env('PAYMENT_PROVIDER', 'mock'));

        $this->provider = match (strtolower($driver)) {
            'paystack' => new PaystackPaymentProvider(),
            default => new MockPaymentProvider(),
        };
    }

    public function initializeFunding(User $user, float $amount, ?string $provider = null): PaymentTransaction
    {
        if ($amount < 100) {
            throw new Exception('Minimum wallet funding amount is ₦100.');
        }

        if ($amount > 500000) {
            throw new Exception('Maximum wallet funding amount per transaction is ₦500,000.');
        }

        $reference = 'PAY-' . strtoupper(Str::random(16));
        $providerName = $provider ?? env('PAYMENT_PROVIDER', 'mock');
        $callbackUrl = route('wallet.fund.callback', ['reference' => $reference]);

        $initResult = $this->provider->initializePayment($user, $amount, $reference, $callbackUrl);

        $paymentTransaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'reference' => $reference,
            'payment_provider' => $providerName,
            'amount' => $amount,
            'fee' => 0.00,
            'status' => 'pending',
            'payment_url' => $initResult['payment_url'] ?? '',
            'metadata' => [
                'callback_url' => $callbackUrl,
                'init_message' => $initResult['message'] ?? '',
            ],
        ]);

        return $paymentTransaction;
    }

    public function verifyAndCreditWallet(string $reference): PaymentTransaction
    {
        return DB::transaction(function () use ($reference) {
            $payment = PaymentTransaction::where('reference', $reference)->lockForUpdate()->first();

            if (!$payment) {
                throw new Exception('Payment record not found.');
            }

            if ($payment->status === 'successful') {
                return $payment; // Idempotency check: Already credited
            }

            $user = $payment->user;
            $verifyResult = $this->provider->verifyPayment($reference);

            if ($verifyResult['success'] && $verifyResult['status'] === 'successful') {
                $payment->update([
                    'status' => 'successful',
                    'channel' => $verifyResult['channel'] ?? 'card',
                    'provider_reference' => $verifyResult['provider_reference'] ?? '',
                    'paid_at' => now(),
                    'metadata' => array_merge($payment->metadata ?? [], ['verify_response' => $verifyResult['raw_response'] ?? []]),
                ]);

                // Credit the user's wallet atomically with idempotency
                $this->walletService->credit(
                    $user,
                    $payment->amount,
                    "Wallet Funding via {$payment->payment_provider} (Ref: {$payment->reference})",
                    $payment->reference,
                    ['payment_transaction_id' => $payment->id]
                );

                // Auto reward upline referral bonus if eligible
                try {
                    app(\App\Services\ReferralService::class)->rewardFirstFunding($user, (float) $payment->amount);
                } catch (\Throwable $e) {
                    // Fail silently so customer funding is not interrupted
                }

                // Send notification
                $this->notificationService->send(
                    $user,
                    'Wallet Funded Successfully',
                    "Your wallet has been credited with ₦" . number_format($payment->amount, 2) . ".",
                    'wallet',
                    route('wallet.index')
                );
            } else {
                $payment->update([
                    'status' => 'failed',
                    'metadata' => array_merge($payment->metadata ?? [], ['verify_response' => $verifyResult['raw_response'] ?? []]),
                ]);
            }

            return $payment;
        });
    }

    public function handleWebhook(array $payload, ?string $signatureHeader = null): array
    {
        $parsed = $this->provider->handleWebhook($payload, $signatureHeader);

        if (!$parsed['verified']) {
            return ['status' => 'unauthorized', 'code' => 401];
        }

        if ($parsed['event'] === 'charge.success' || $parsed['status'] === 'successful') {
            $reference = $parsed['reference'];
            if ($reference) {
                $this->verifyAndCreditWallet($reference);
            }
        }

        return ['status' => 'ok', 'code' => 200];
    }
}
