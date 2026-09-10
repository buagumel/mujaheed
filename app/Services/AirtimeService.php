<?php

namespace App\Services;

use App\Models\NetworkSetting;
use App\Models\User;
use App\Models\VtuTransaction;
use App\Services\NotificationService;
use App\Services\VTU\VTUService;
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AirtimeService
{
    protected WalletService $walletService;
    protected VTUService $vtuService;
    protected NotificationService $notificationService;

    public function __construct(
        WalletService $walletService,
        VTUService $vtuService,
        NotificationService $notificationService
    ) {
        $this->walletService = $walletService;
        $this->vtuService = $vtuService;
        $this->notificationService = $notificationService;
    }

    public function purchase(User $user, string $network, string $phone, float $amount, string $pin): VtuTransaction
    {
        $network = strtoupper(trim($network));
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Validate PIN
        if (!$user->verifyPin($pin)) {
            if ($user->isPinLocked()) {
                throw new Exception('Transaction PIN is locked due to too many failed attempts. Try again later.');
            }
            throw new Exception('Invalid transaction PIN.');
        }

        // Validate Network & Min/Max amounts
        $setting = NetworkSetting::where('network', $network)->first();
        $minAmount = $setting ? (float) $setting->airtime_min_amount : 50.00;
        $maxAmount = $setting ? (float) $setting->airtime_max_amount : 50000.00;
        $discountPercent = $setting ? (float) $setting->airtime_discount_percent : 2.00;

        if ($amount < $minAmount || $amount > $maxAmount) {
            throw new Exception("Airtime amount must be between ₦{$minAmount} and ₦{$maxAmount}.");
        }

        $discountAmount = round(($amount * $discountPercent) / 100, 2);
        $amountToPay = round($amount - $discountAmount, 2);

        $reference = 'AIR-' . strtoupper(Str::random(14));

        // 1. Debit wallet atomically
        $walletTx = $this->walletService->debit(
            $user,
            $amountToPay,
            "Airtime purchase of ₦{$amount} for {$network} ({$phone})",
            $reference
        );

        // 2. Create pending VTU transaction
        $vtuTx = VtuTransaction::create([
            'user_id' => $user->id,
            'wallet_transaction_id' => $walletTx->id,
            'reference' => $reference,
            'service_type' => 'airtime',
            'provider' => $network,
            'recipient' => $phone,
            'amount' => $amount,
            'discount_amount' => $discountAmount,
            'cost_price' => $amountToPay,
            'status' => 'pending',
        ]);

        // 3. Call VTU provider
        try {
            \Illuminate\Support\Facades\Log::info("VTU API Dispatch Request [Airtime]: Network={$network}, Recipient={$phone}, Amount=₦{$amount}, Ref={$reference}");
            $providerResult = $this->vtuService->purchaseAirtime($network, $phone, $amount, $reference);

            if ($providerResult['success'] && $providerResult['status'] === 'successful') {
                \Illuminate\Support\Facades\Log::info("VTU API Dispatch Success [Airtime]: Ref={$reference}, ProviderRef=" . ($providerResult['provider_reference'] ?? 'N/A'));
                $vtuTx->update([
                    'status' => 'successful',
                    'provider_reference' => $providerResult['provider_reference'] ?? null,
                    'cost_price' => $providerResult['cost_price'] ?? $amountToPay,
                    'response_payload' => $providerResult['raw_response'] ?? null,
                ]);

                $this->notificationService->send(
                    $user,
                    'Airtime Purchase Successful',
                    "₦{$amount} {$network} Airtime to {$phone} was successful.",
                    'vtu',
                    route('transactions.show', $vtuTx->reference)
                );
            } else {
                // Provider failed: Mark as failed and refund user
                $errorMessage = $providerResult['message'] ?? 'Provider error occurred.';
                \Illuminate\Support\Facades\Log::error("VTU API Dispatch Failed [Airtime]: Ref={$reference}, Error={$errorMessage}");
                $vtuTx->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'response_payload' => $providerResult['raw_response'] ?? null,
                ]);

                $this->walletService->refund(
                    $user,
                    $amountToPay,
                    "Refund: Failed airtime recharge of ₦{$amount} ({$network} {$phone})",
                    $reference
                );

                $this->notificationService->send(
                    $user,
                    'Airtime Purchase Failed',
                    "Your airtime recharge to {$phone} failed and ₦{$amountToPay} has been refunded to your wallet.",
                    'vtu',
                    route('transactions.show', $vtuTx->reference)
                );

                throw new Exception($errorMessage);
            }
        } catch (\Throwable $e) {
            if ($vtuTx->status === 'pending') {
                $vtuTx->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $this->walletService->refund(
                    $user,
                    $amountToPay,
                    "Refund: Failed airtime recharge of ₦{$amount} ({$network} {$phone})",
                    $reference
                );
            }
            throw $e;
        }

        return $vtuTx;
    }
}
