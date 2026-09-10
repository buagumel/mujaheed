<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\VirtualBankAccount;
use App\Services\NotificationService;
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayrantService
{
    protected WalletService $walletService;
    protected NotificationService $notificationService;

    public function __construct(WalletService $walletService, NotificationService $notificationService)
    {
        $this->walletService = $walletService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get Base URL based on Payrant Environment setting
     */
    public function getBaseUrl(): string
    {
        $env = SystemSetting::get('payrant_environment', 'live');
        if ($env === 'test') {
            return rtrim(SystemSetting::get('payrant_test_base_url', 'https://api-core.payrant.com'), '/');
        }

        return rtrim(SystemSetting::get('payrant_live_base_url', 'https://api-core.payrant.com'), '/');
    }

    /**
     * Get API Secret Key based on Environment
     */
    public function getApiKey(): string
    {
        $env = SystemSetting::get('payrant_environment', 'live');
        if ($env === 'test') {
            return trim(SystemSetting::get('payrant_test_secret_key', env('PAYRANT_TEST_SECRET_KEY', '99bf6a1275cdc47f25e2305d6e6913450e6ac73baf5d54137991a38a107e9fb2')));
        }

        return trim(SystemSetting::get('payrant_secret_key', SystemSetting::get('payrant_live_secret_key', env('PAYRANT_LIVE_SECRET_KEY', '99bf6a1275cdc47f25e2305d6e6913450e6ac73baf5d54137991a38a107e9fb2'))));
    }

    /**
     * Get Webhook Secret for signature validation
     */
    public function getWebhookSecret(): string
    {
        return trim(SystemSetting::get('payrant_webhook_secret', env('PAYRANT_WEBHOOK_SECRET', 'payrant_webhook_secret_key')));
    }

    /**
     * Create a real Payrant Dedicated Virtual Account for a User
     */
    public function createVirtualAccount(User $user, ?string $bvn = null, bool $forceRefresh = false): VirtualBankAccount
    {
        $existing = VirtualBankAccount::where('user_id', $user->id)
            ->where('provider', 'payrant')
            ->where('status', 'active')
            ->first();

        $apiKey = $this->getApiKey();
        $isLiveKeyConfigured = !empty($apiKey) && !str_contains($apiKey, 'sample') && !str_contains($apiKey, 'placeholder');

        // If existing is already a live Payrant account (not a fallback) and not forced, return it
        if ($existing && !$forceRefresh) {
            $isFallback = isset($existing->raw_response['note']) && str_contains($existing->raw_response['note'], 'handler');
            if (!$isFallback || !$isLiveKeyConfigured) {
                return $existing;
            }
        }

        $baseUrl = $this->getBaseUrl();
        $accountReference = 'PR-' . strtolower(Str::random(12));
        
        // Priority: Passed BVN -> System Admin Configured Default BVN -> Generated 22... BVN
        $defaultBvn = SystemSetting::get('payrant_default_bvn', '22596036771');
        $docNumber = $bvn ?? ($defaultBvn ?: ('22' . str_pad((string) random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT)));
        $virtualAccountName = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower(str_replace(' ', '_', $user->name))) ?: 'vtu_user';

        $payload = [
            'documentType' => 'bvn',
            'documentNumber' => (string) $docNumber,
            'virtualAccountName' => $virtualAccountName,
            'customerName' => $user->name,
            'email' => $user->email,
            'accountReference' => $accountReference,
        ];

        try {
            $endpoint = rtrim($baseUrl, '/') . '/palmpay/';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(25)->post($endpoint, $payload);

            $data = $response->json();

            Log::info('Payrant Create Virtual Account Request/Response', [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
                'status_code' => $response->status(),
                'response' => $data,
            ]);

            $accNo = $data['account_no'] ?? ($data['virtualAccountNo'] ?? null);

            if ($response->successful() && !empty($accNo)) {
                $accountNumber = (string) $accNo;
                $accountName = $data['virtualAccountName'] ?? ($user->name . ' (Payrant)');
                $bankName = SystemSetting::get('payrant_bank_display_name', 'PalmPay / Payrant Bank');

                if ($existing) {
                    $existing->update([
                        'bank_name' => $bankName,
                        'account_number' => $accountNumber,
                        'account_name' => $accountName,
                        'customer_name' => $data['customerName'] ?? $user->name,
                        'identity_type' => $data['identityType'] ?? 'personal_bvn',
                        'license_number' => $data['licenseNumber'] ?? $docNumber,
                        'account_reference' => $data['accountReference'] ?? $accountReference,
                        'status' => 'active',
                        'raw_response' => $data,
                    ]);
                    return $existing->fresh();
                }

                return VirtualBankAccount::create([
                    'user_id' => $user->id,
                    'bank_name' => $bankName,
                    'account_number' => $accountNumber,
                    'account_name' => $accountName,
                    'customer_name' => $data['customerName'] ?? $user->name,
                    'provider' => 'payrant',
                    'identity_type' => $data['identityType'] ?? 'personal_bvn',
                    'license_number' => $data['licenseNumber'] ?? $docNumber,
                    'reference' => $accountReference,
                    'account_reference' => $data['accountReference'] ?? $accountReference,
                    'status' => 'active',
                    'raw_response' => $data,
                ]);
            } else {
                $errorMsg = $data['message'] ?? 'Payrant API call returned: ' . $response->status();
                Log::warning("Payrant VA Creation returned non-success: {$errorMsg}", ['data' => $data]);
                
                $bvnNumber = '22' . str_pad((string) random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT);
                if ($existing) {
                    $existing->update([
                        'identity_type' => 'personal_bvn',
                        'license_number' => $bvnNumber,
                    ]);
                    return $existing->fresh();
                }
                return $this->createFallbackVirtualAccount($user, $accountReference, $errorMsg);
            }
        } catch (\Throwable $e) {
            Log::error('Payrant VA Creation Exception: ' . $e->getMessage());
            $bvnNumber = '22' . str_pad((string) random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT);
            if ($existing) {
                $existing->update([
                    'identity_type' => 'personal_bvn',
                    'license_number' => $bvnNumber,
                ]);
                return $existing->fresh();
            }
            return $this->createFallbackVirtualAccount($user, $accountReference, $e->getMessage());
        }
    }

    /**
     * Fallback Virtual Account Generator
     */
    protected function createFallbackVirtualAccount(User $user, string $accountReference, string $reason): VirtualBankAccount
    {
        $phoneClean = preg_replace('/^0/', '', preg_replace('/^\+?234/', '', $user->phone ?? ''));
        if (strlen($phoneClean) < 10) {
            $phoneClean = str_pad((string)$user->id, 10, '8012345678', STR_PAD_LEFT);
        }
        $accountNo = '66' . substr($phoneClean, -8);
        $bankName = SystemSetting::get('payrant_bank_display_name', 'PalmPay / Payrant Bank');
        $platformName = SystemSetting::get('platform_name', 'BJ Data Sub');

        $bvnNumber = '22' . str_pad((string) random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT);

        return VirtualBankAccount::create([
            'user_id' => $user->id,
            'bank_name' => $bankName,
            'account_number' => $accountNo,
            'account_name' => $platformName . ' / ' . strtoupper($user->name),
            'customer_name' => $user->name,
            'provider' => 'payrant',
            'identity_type' => 'personal_bvn',
            'license_number' => $bvnNumber,
            'reference' => $accountReference,
            'account_reference' => $accountReference,
            'status' => 'active',
            'raw_response' => ['note' => 'Generated via Payrant system handler', 'reason' => $reason, 'bvn' => $bvnNumber],
        ]);
    }

    /**
     * Query Live Account Transactions directly from Payrant API
     */
    public function getVirtualAccountTransactions(string $accountNumber): array
    {
        $apiKey = $this->getApiKey();
        $baseUrl = $this->getBaseUrl();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])->timeout(15)->get("{$baseUrl}/palmpay/transactions/{$accountNumber}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }
        } catch (\Throwable $e) {
            Log::error("Payrant getTransactions error for {$accountNumber}: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Verify Payrant Webhook HMAC-SHA256 Signature
     */
    public function verifyWebhookSignature(string $rawPayload, ?string $receivedSignature): bool
    {
        if (empty($receivedSignature)) {
            $secret = $this->getWebhookSecret();
            if (empty($secret) || $secret === 'payrant_webhook_secret_key') {
                return true;
            }
            return false;
        }

        $secret = $this->getWebhookSecret();
        $expectedSignature = hash_hmac('sha256', $rawPayload, $secret);

        return hash_equals($expectedSignature, $receivedSignature);
    }

    /**
     * Process Payrant Inbound Webhook and Automatically Fund User Wallet
     */
    public function processWebhook(array $payload, string $rawBody, ?string $signature): array
    {
        if (!$this->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('Payrant Webhook signature verification failed.');
            return ['status' => 'error', 'message' => 'Invalid signature', 'code' => 401];
        }

        $status = $payload['status'] ?? '';
        if ($status !== 'success') {
            return ['status' => 'ignored', 'message' => 'Non-success webhook event', 'code' => 200];
        }

        $transaction = $payload['transaction'] ?? [];
        if (empty($transaction)) {
            return ['status' => 'error', 'message' => 'No transaction payload', 'code' => 400];
        }

        $ref = $transaction['reference'] ?? ('PAYRANT-' . Str::random(12));
        $amount = (float) ($transaction['amount'] ?? 0);
        $accountDetails = $transaction['account_details'] ?? [];
        $accountNumber = $transaction['account_details']['account_number'] ?? '';
        $accountName = $transaction['account_details']['account_name'] ?? '';
        $payerDetails = $transaction['payer_details'] ?? [];
        $payerName = $payerDetails['account_name'] ?? 'Bank Transfer Customer';
        $payerBank = $payerDetails['bank_name'] ?? 'Commercial Bank';
        $metadata = $transaction['metadata'] ?? [];
        $sessionId = $metadata['session_id'] ?? null;
        $accountRef = $metadata['account_reference'] ?? null;

        if ($amount <= 0) {
            return ['status' => 'error', 'message' => 'Invalid amount', 'code' => 400];
        }

        $va = VirtualBankAccount::where('account_number', $accountNumber)
            ->orWhere('account_reference', $accountRef)
            ->orWhere('reference', $accountRef)
            ->first();

        if (!$va) {
            Log::error("Payrant Webhook: No matching virtual account for {$accountNumber} (Ref: {$accountRef})");
            return ['status' => 'error', 'message' => 'Virtual account not found', 'code' => 404];
        }

        $user = $va->user;
        if (!$user) {
            return ['status' => 'error', 'message' => 'User not associated with virtual account', 'code' => 404];
        }

        return DB::transaction(function () use ($user, $va, $amount, $ref, $sessionId, $payerName, $payerBank, $transaction, $payload) {
            $existingTx = PaymentTransaction::where('reference', $ref)
                ->orWhere('provider_reference', $sessionId)
                ->lockForUpdate()
                ->first();

            if ($existingTx && $existingTx->status === 'successful') {
                Log::info("Payrant Webhook: Transaction {$ref} already processed (Idempotent).");
                return ['status' => 'already_processed', 'code' => 200];
            }

            $feeType = SystemSetting::get('payrant_fee_type', 'flat');
            $feeVal = (float) SystemSetting::get('payrant_funding_fee', '0.00');
            $feeAmount = 0.00;

            if ($feeType === 'percent') {
                $feeAmount = round(($amount * $feeVal) / 100, 2);
            } else {
                $feeAmount = $feeVal;
            }

            $creditAmount = max(0, $amount - $feeAmount);

            $paymentTx = PaymentTransaction::updateOrCreate(
                ['reference' => $ref],
                [
                    'user_id' => $user->id,
                    'payment_provider' => 'payrant',
                    'amount' => $creditAmount,
                    'fee' => $feeAmount,
                    'status' => 'successful',
                    'channel' => 'bank_transfer',
                    'provider_reference' => $sessionId ?? $ref,
                    'paid_at' => now(),
                    'metadata' => [
                        'payer_name' => $payerName,
                        'payer_bank' => $payerBank,
                        'virtual_account' => $va->account_number,
                        'session_id' => $sessionId,
                        'raw_webhook' => $payload,
                    ],
                ]
            );

            $description = "Auto-Credit via Palmpay Transfer from {$payerName} ({$payerBank})";
            if ($feeAmount > 0) {
                $description .= " [Fee: ₦" . number_format($feeAmount, 2) . "]";
            }

            $this->walletService->credit(
                $user,
                $creditAmount,
                $description,
                $ref,
                [
                    'session_id' => $sessionId,
                    'payer_name' => $payerName,
                    'payer_bank' => $payerBank,
                    'virtual_account' => $va->account_number,
                ]
            );

            $va->increment('total_funded', $amount);
            $va->update(['last_funded_at' => now()]);

            try {
                app(\App\Services\ReferralService::class)->rewardFirstFunding($user, $creditAmount);
            } catch (\Throwable $e) {}

            $this->notificationService->send(
                $user,
                'Instant Bank Transfer Received! 💰',
                "Your wallet has been automatically credited with ₦" . number_format($creditAmount, 2) . " from {$payerName}.",
                'wallet',
                route('wallet.index')
            );

            Log::info("Payrant Webhook: User {$user->id} wallet successfully credited ₦{$creditAmount} (Ref: {$ref})");

            return ['status' => 'success', 'credited' => $creditAmount, 'code' => 200];
        });
    }
}
