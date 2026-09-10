<?php

namespace App\Services;

use App\Models\DataPlan;
use App\Models\User;
use App\Models\VtuTransaction;
use App\Services\NotificationService;
use App\Services\VTU\VTUService;
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Str;

class DataService
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

    public function purchase(User $user, int $dataPlanId, string $phone, string $pin): VtuTransaction
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Validate PIN
        if (!$user->verifyPin($pin)) {
            if ($user->isPinLocked()) {
                throw new Exception('Transaction PIN is locked due to too many failed attempts. Try again later.');
            }
            throw new Exception('Invalid transaction PIN.');
        }

        // Validate Plan
        $plan = DataPlan::where('id', $dataPlanId)->where('status', 'active')->first();
        if (!$plan) {
            throw new Exception('Selected data plan is not available.');
        }

        $amountToPay = (float) $plan->selling_price;
        $reference = 'DAT-' . strtoupper(Str::random(14));

        // 1. Debit wallet atomically
        $walletTx = $this->walletService->debit(
            $user,
            $amountToPay,
            "Data bundle purchase: {$plan->network} {$plan->name} ({$phone})",
            $reference
        );

        // 2. Create pending transaction
        $vtuTx = VtuTransaction::create([
            'user_id' => $user->id,
            'wallet_transaction_id' => $walletTx->id,
            'reference' => $reference,
            'service_type' => 'data',
            'provider' => $plan->network,
            'plan_code' => $plan->code,
            'plan_name' => $plan->name,
            'recipient' => $phone,
            'amount' => $amountToPay,
            'cost_price' => $plan->provider_price,
            'status' => 'pending',
        ]);

        // 3. Call VTU Provider
        try {
            \Illuminate\Support\Facades\Log::info("VTU API Dispatch Request [Data]: Network={$plan->network}, Plan={$plan->name}, Code={$plan->code}, Recipient={$phone}, Ref={$reference}");
            $providerResult = $this->vtuService->purchaseData($plan->network, $plan->code, $phone, $reference);

            if ($providerResult['success'] && $providerResult['status'] === 'successful') {
                \Illuminate\Support\Facades\Log::info("VTU API Dispatch Success [Data]: Ref={$reference}, ProviderRef=" . ($providerResult['provider_reference'] ?? 'N/A'));
                $vtuTx->update([
                    'status' => 'successful',
                    'provider_reference' => $providerResult['provider_reference'] ?? null,
                    'response_payload' => $providerResult['raw_response'] ?? null,
                ]);

                $this->notificationService->send(
                    $user,
                    'Data Bundle Purchased Successfully',
                    "{$plan->network} {$plan->name} to {$phone} was successful.",
                    'vtu',
                    route('transactions.show', $vtuTx->reference)
                );
            } else {
                $errorMessage = $providerResult['message'] ?? 'Data delivery failed.';
                \Illuminate\Support\Facades\Log::error("VTU API Dispatch Failed [Data]: Ref={$reference}, Error={$errorMessage}");
                $vtuTx->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'response_payload' => $providerResult['raw_response'] ?? null,
                ]);

                $this->walletService->refund(
                    $user,
                    $amountToPay,
                    "Refund: Failed data purchase ({$plan->network} {$plan->name}) for {$phone}",
                    $reference
                );

                $this->notificationService->send(
                    $user,
                    'Data Purchase Failed',
                    "Your data purchase for {$phone} failed and ₦{$amountToPay} has been refunded.",
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
                    "Refund: Failed data purchase ({$plan->network} {$plan->name}) for {$phone}",
                    $reference
                );
            }
            throw $e;
        }

        return $vtuTx;
    }
}
