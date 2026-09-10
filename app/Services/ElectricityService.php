<?php

namespace App\Services;

use App\Models\ElectricityProvider;
use App\Models\User;
use App\Models\VtuTransaction;
use App\Services\NotificationService;
use App\Services\VTU\VTUService;
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Str;

class ElectricityService
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

    public function verifyMeter(string $discoCode, string $meterNumber, string $type = 'prepaid'): array
    {
        return $this->vtuService->verifyMeter($discoCode, $meterNumber, $type);
    }

    public function pay(
        User $user,
        string $discoCode,
        string $meterNumber,
        string $type,
        float $amount,
        string $phone,
        string $pin,
        ?string $customerName = null
    ): VtuTransaction {
        $discoCode = strtoupper(trim($discoCode));
        $meterNumber = preg_replace('/[^0-9]/', '', $meterNumber);
        $type = strtolower($type);

        // Validate PIN
        if (!$user->verifyPin($pin)) {
            if ($user->isPinLocked()) {
                throw new Exception('Transaction PIN is locked due to too many failed attempts. Try again later.');
            }
            throw new Exception('Invalid transaction PIN.');
        }

        // Validate provider
        $provider = ElectricityProvider::where('code', $discoCode)->where('status', 'active')->first();
        if (!$provider) {
            throw new Exception('Selected electricity distribution company is unavailable.');
        }

        if ($amount < (float) $provider->min_amount || $amount > (float) $provider->max_amount) {
            throw new Exception("Amount must be between ₦{$provider->min_amount} and ₦{$provider->max_amount}.");
        }

        $fee = (float) $provider->convenience_fee;
        $totalToPay = $amount + $fee;
        $reference = 'ELC-' . strtoupper(Str::random(14));

        // 1. Debit wallet
        $walletTx = $this->walletService->debit(
            $user,
            $totalToPay,
            "Electricity payment for {$provider->name} (Meter: {$meterNumber})",
            $reference
        );

        // 2. Create pending transaction
        $vtuTx = VtuTransaction::create([
            'user_id' => $user->id,
            'wallet_transaction_id' => $walletTx->id,
            'reference' => $reference,
            'service_type' => 'electricity',
            'provider' => $discoCode,
            'recipient' => $meterNumber,
            'amount' => $amount,
            'fee' => $fee,
            'customer_name' => $customerName,
            'status' => 'pending',
        ]);

        // 3. Call VTU Provider
        try {
            $providerResult = $this->vtuService->payElectricity($discoCode, $meterNumber, $type, $amount, $phone, $reference);

            if ($providerResult['success'] && $providerResult['status'] === 'successful') {
                $vtuTx->update([
                    'status' => 'successful',
                    'token' => $providerResult['token'] ?? null,
                    'units' => $providerResult['units'] ?? null,
                    'provider_reference' => $providerResult['provider_reference'] ?? null,
                    'response_payload' => $providerResult['raw_response'] ?? null,
                ]);

                $tokenMsg = $vtuTx->token ? " Your token is: {$vtuTx->token}" : '';

                $this->notificationService->send(
                    $user,
                    'Electricity Bill Payment Successful',
                    "Payment of ₦" . number_format($amount, 2) . " for meter {$meterNumber} successful.{$tokenMsg}",
                    'vtu',
                    route('transactions.show', $vtuTx->reference)
                );
            } else {
                $errorMessage = $providerResult['message'] ?? 'Electricity token generation failed.';
                $vtuTx->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'response_payload' => $providerResult['raw_response'] ?? null,
                ]);

                $this->walletService->refund(
                    $user,
                    $totalToPay,
                    "Refund: Failed electricity payment ({$provider->name} Meter: {$meterNumber})",
                    $reference
                );

                $this->notificationService->send(
                    $user,
                    'Electricity Bill Payment Failed',
                    "Payment for meter {$meterNumber} failed and ₦{$totalToPay} has been refunded.",
                    'vtu',
                    route('transactions.show', $vtuTx->reference)
                );

                throw new Exception("Electricity payment failed: {$errorMessage}. Your wallet has been refunded.");
            }
        } catch (\Throwable $e) {
            if ($vtuTx->status === 'pending') {
                $vtuTx->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $this->walletService->refund(
                    $user,
                    $totalToPay,
                    "Refund: Failed electricity payment ({$provider->name} Meter: {$meterNumber})",
                    $reference
                );
            }
            throw $e;
        }

        return $vtuTx;
    }
}
