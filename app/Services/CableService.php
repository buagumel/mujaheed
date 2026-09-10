<?php

namespace App\Services;

use App\Models\CablePlan;
use App\Models\User;
use App\Models\VtuTransaction;
use App\Services\NotificationService;
use App\Services\VTU\VTUService;
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Str;

class CableService
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

    public function verifySmartcard(string $provider, string $smartcardNumber): array
    {
        return $this->vtuService->verifySmartcard($provider, $smartcardNumber);
    }

    public function pay(
        User $user,
        int $cablePlanId,
        string $smartcardNumber,
        string $phone,
        string $pin,
        ?string $customerName = null
    ): VtuTransaction {
        $smartcardNumber = preg_replace('/[^0-9]/', '', $smartcardNumber);

        // Validate PIN
        if (!$user->verifyPin($pin)) {
            if ($user->isPinLocked()) {
                throw new Exception('Transaction PIN is locked due to too many failed attempts. Try again later.');
            }
            throw new Exception('Invalid transaction PIN.');
        }

        // Validate Plan
        $plan = CablePlan::where('id', $cablePlanId)->where('status', 'active')->first();
        if (!$plan) {
            throw new Exception('Selected cable package is currently unavailable.');
        }

        $amountToPay = (float) $plan->selling_price;
        $reference = 'CAB-' . strtoupper(Str::random(14));

        // 1. Debit wallet
        $walletTx = $this->walletService->debit(
            $user,
            $amountToPay,
            "Cable TV recharge: {$plan->provider} {$plan->name} (IUC: {$smartcardNumber})",
            $reference
        );

        // 2. Create pending transaction
        $vtuTx = VtuTransaction::create([
            'user_id' => $user->id,
            'wallet_transaction_id' => $walletTx->id,
            'reference' => $reference,
            'service_type' => 'cable',
            'provider' => $plan->provider,
            'plan_code' => $plan->code,
            'plan_name' => $plan->name,
            'recipient' => $smartcardNumber,
            'customer_name' => $customerName,
            'amount' => $amountToPay,
            'cost_price' => $plan->provider_price,
            'status' => 'pending',
        ]);

        // 3. Call VTU Provider
        try {
            $providerResult = $this->vtuService->payCable($plan->provider, $plan->code, $smartcardNumber, $phone, $reference);

            if ($providerResult['success'] && $providerResult['status'] === 'successful') {
                $vtuTx->update([
                    'status' => 'successful',
                    'provider_reference' => $providerResult['provider_reference'] ?? null,
                    'response_payload' => $providerResult['raw_response'] ?? null,
                ]);

                $this->notificationService->send(
                    $user,
                    'Cable TV Subscription Successful',
                    "{$plan->provider} {$plan->name} subscription on IUC {$smartcardNumber} was activated successfully.",
                    'vtu',
                    route('transactions.show', $vtuTx->reference)
                );
            } else {
                $errorMessage = $providerResult['message'] ?? 'Cable activation failed.';
                $vtuTx->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'response_payload' => $providerResult['raw_response'] ?? null,
                ]);

                $this->walletService->refund(
                    $user,
                    $amountToPay,
                    "Refund: Failed cable subscription ({$plan->provider} {$plan->name}) for IUC {$smartcardNumber}",
                    $reference
                );

                $this->notificationService->send(
                    $user,
                    'Cable TV Subscription Failed',
                    "Subscription for IUC {$smartcardNumber} failed and ₦{$amountToPay} has been refunded.",
                    'vtu',
                    route('transactions.show', $vtuTx->reference)
                );

                throw new Exception("Cable subscription failed: {$errorMessage}. Your wallet has been refunded.");
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
                    "Refund: Failed cable subscription ({$plan->provider} {$plan->name}) for IUC {$smartcardNumber}",
                    $reference
                );
            }
            throw $e;
        }

        return $vtuTx;
    }
}
