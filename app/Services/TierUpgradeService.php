<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TierUpgradeService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public static function getUpgradeFee(string $targetTier): float
    {
        return match (strtolower($targetTier)) {
            'reseller' => (float) \App\Models\SystemSetting::get('reseller_upgrade_fee', 1500.00),
            'vip' => (float) \App\Models\SystemSetting::get('vip_upgrade_fee', 3500.00),
            default => 0.00,
        };
    }

    public function upgrade(User $user, string $targetTier, string $pin): void
    {
        if (!$user->verifyPin($pin)) {
            throw new Exception("Invalid 4-digit transaction PIN.");
        }

        $targetTier = strtolower($targetTier);
        if (!in_array($targetTier, ['reseller', 'vip'])) {
            throw new Exception("Invalid tier selected.");
        }

        if ($user->tier === $targetTier) {
            throw new Exception("You are already on the {$user->getTierDisplayName()} package.");
        }

        if ($user->tier === 'vip' && $targetTier === 'reseller') {
            throw new Exception("You are already on the highest tier (VIP Partner).");
        }

        $fee = self::getUpgradeFee($targetTier);

        DB::transaction(function () use ($user, $targetTier, $fee) {
            $reference = 'UPGRADE-' . strtoupper(Str::random(10));

            // Debit Upgrade Fee from wallet
            if ($fee > 0) {
                $this->walletService->debit(
                    $user,
                    $fee,
                    "Account Upgrade to " . ucfirst($targetTier) . " Tier",
                    $reference,
                    ['type' => 'tier_upgrade']
                );
            }

            // Update user tier
            $user->update(['tier' => $targetTier]);

            // Notify user
            $user->appNotifications()->create([
                'title' => '🎉 Package Upgrade Successful!',
                'message' => "Congratulations! Your account has been upgraded to {$user->getTierDisplayName()}. You now enjoy wholesale pricing across all services.",
                'type' => 'success',
            ]);
        });
    }
}
