<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    /**
     * Credit a user's wallet atomically.
     *
     * @param User $user
     * @param float|string $amount
     * @param string $description
     * @param string|null $reference
     * @param array|null $metadata
     * @param string $type
     * @return WalletTransaction
     * @throws Exception
     */
    public function credit(
        User $user,
        float|string $amount,
        string $description,
        ?string $reference = null,
        ?array $metadata = null,
        string $type = 'credit'
    ): WalletTransaction {
        $amount = (float) $amount;
        if ($amount <= 0) {
            throw new Exception('Credit amount must be greater than zero.');
        }

        $reference = $reference ?? 'CR-' . strtoupper(Str::random(16));

        return DB::transaction(function () use ($user, $amount, $description, $reference, $metadata, $type) {
            // Check for duplicate reference (idempotency)
            $existing = WalletTransaction::where('reference', $reference)->first();
            if ($existing) {
                return $existing;
            }

            // Lock wallet row for update to prevent race conditions
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0.00,
                    'currency' => 'NGN',
                    'status' => 'active',
                ]);
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();
            }

            if ($wallet->isFrozen()) {
                throw new Exception('Wallet is currently frozen. Contact support.');
            }

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter = $balanceBefore + $amount;

            $wallet->balance = $balanceAfter;
            $wallet->save();

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference' => $reference,
                'description' => $description,
                'status' => 'successful',
                'metadata' => $metadata,
            ]);

            return $transaction;
        });
    }

    /**
     * Debit a user's wallet atomically.
     *
     * @param User $user
     * @param float|string $amount
     * @param string $description
     * @param string|null $reference
     * @param array|null $metadata
     * @return WalletTransaction
     * @throws Exception
     */
    public function debit(
        User $user,
        float|string $amount,
        string $description,
        ?string $reference = null,
        ?array $metadata = null
    ): WalletTransaction {
        $amount = (float) $amount;
        if ($amount <= 0) {
            throw new Exception('Debit amount must be greater than zero.');
        }

        $reference = $reference ?? 'DR-' . strtoupper(Str::random(16));

        return DB::transaction(function () use ($user, $amount, $description, $reference, $metadata) {
            // Check for duplicate reference
            $existing = WalletTransaction::where('reference', $reference)->first();
            if ($existing) {
                return $existing;
            }

            // Lock wallet row for update
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet) {
                throw new Exception('Wallet not found for this user.');
            }

            if ($wallet->isFrozen()) {
                throw new Exception('Wallet is currently frozen. Contact support.');
            }

            $balanceBefore = (float) $wallet->balance;

            if ($balanceBefore < $amount) {
                throw new Exception('Insufficient wallet balance. Please fund your wallet.');
            }

            $balanceAfter = $balanceBefore - $amount;

            $wallet->balance = $balanceAfter;
            $wallet->save();

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference' => $reference,
                'description' => $description,
                'status' => 'successful',
                'metadata' => $metadata,
            ]);

            return $transaction;
        });
    }

    /**
     * Refund a debited transaction atomically.
     *
     * @param User $user
     * @param float|string $amount
     * @param string $description
     * @param string|null $originalReference
     * @return WalletTransaction
     * @throws Exception
     */
    public function refund(
        User $user,
        float|string $amount,
        string $description,
        ?string $originalReference = null
    ): WalletTransaction {
        $refundReference = 'RF-' . ($originalReference ?? strtoupper(Str::random(12)));

        return $this->credit(
            $user,
            $amount,
            $description,
            $refundReference,
            ['original_reference' => $originalReference],
            'refund'
        );
    }
}
