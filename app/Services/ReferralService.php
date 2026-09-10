<?php

namespace App\Services;

use App\Models\ReferralCommission;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function rewardFirstFunding(User $fundedUser, float $fundedAmount): void
    {
        if (!$fundedUser->referred_by_id) {
            return;
        }

        $upline = User::find($fundedUser->referred_by_id);
        if (!$upline) {
            return;
        }

        // Check if bonus already given for this referred user
        $alreadyRewarded = ReferralCommission::where('user_id', $upline->id)
            ->where('referred_user_id', $fundedUser->id)
            ->exists();

        if ($alreadyRewarded) {
            return;
        }

        if (\App\Models\SystemSetting::get('referral_status', 'active') !== 'active') {
            return;
        }

        // Calculate dynamic reward
        $rewardType = \App\Models\SystemSetting::get('referral_reward_type', 'flat');
        $flatBonus = (float) \App\Models\SystemSetting::get('referral_flat_bonus', 100.00);
        $percentBonus = (float) \App\Models\SystemSetting::get('referral_percent_bonus', 2.00);

        if ($rewardType === 'percent') {
            $bonus = round(($fundedAmount * ($percentBonus / 100)), 2);
        } else {
            $bonus = $flatBonus;
        }

        if ($bonus <= 0) {
            $bonus = 100.00;
        }

        DB::transaction(function () use ($upline, $fundedUser, $bonus) {
            $upline->increment('referral_balance', $bonus);

            ReferralCommission::create([
                'user_id' => $upline->id,
                'referred_user_id' => $fundedUser->id,
                'amount' => $bonus,
                'description' => "Referral commission from {$fundedUser->name}'s first wallet funding",
                'status' => 'credited',
            ]);

            $upline->appNotifications()->create([
                'title' => '🎁 Referral Bonus Received!',
                'message' => "You earned ₦" . number_format($bonus, 2) . " referral bonus from {$fundedUser->name}'s wallet funding.",
                'type' => 'success',
            ]);
        });
    }

    public function transferToMainWallet(User $user, float $amount): void
    {
        $minWithdrawal = (float) \App\Models\SystemSetting::get('referral_min_withdrawal', 100.00);
        if ($amount < $minWithdrawal) {
            throw new Exception("Minimum referral withdrawal amount is ₦" . number_format($minWithdrawal, 2));
        }

        if ($user->referral_balance < $amount) {
            throw new Exception("Insufficient referral balance.");
        }

        DB::transaction(function () use ($user, $amount) {
            $reference = 'REF-WITHDRAW-' . strtoupper(Str::random(8));

            $user->decrement('referral_balance', $amount);

            $this->walletService->credit(
                $user,
                $amount,
                "Transfer from Referral Balance to Main Wallet",
                $reference,
                ['type' => 'referral_payout']
            );

            $user->appNotifications()->create([
                'title' => 'Referral Bonus Transferred',
                'message' => "₦" . number_format($amount, 2) . " has been credited to your available wallet balance.",
                'type' => 'success',
            ]);
        });
    }
}
