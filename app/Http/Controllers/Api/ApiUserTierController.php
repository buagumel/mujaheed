<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\TierUpgradeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiUserTierController extends Controller
{
    protected TierUpgradeService $tierUpgradeService;

    public function __construct(TierUpgradeService $tierUpgradeService)
    {
        $this->tierUpgradeService = $tierUpgradeService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'current_tier' => $user->tier,
                'tier_name' => $user->getTierDisplayName(),
                'fees' => [
                    'reseller' => TierUpgradeService::getUpgradeFee('reseller'),
                    'vip' => TierUpgradeService::getUpgradeFee('vip'),
                ],
                'discounts' => [
                    'standard' => '2.0% Airtime Cashback',
                    'reseller' => SystemSetting::get('reseller_airtime_discount', '3.5') . '% Airtime Cashback + Wholesale Data',
                    'vip' => SystemSetting::get('vip_airtime_discount', '4.5') . '% Airtime Cashback + Maximum Wholesale Discounts',
                ],
            ],
        ]);
    }

    public function upgrade(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tier' => ['required', 'string', 'in:reseller,vip'],
            'pin' => ['required', 'string', 'digits:4'],
        ]);

        $user = $request->user();

        try {
            $this->tierUpgradeService->upgrade($user, $validated['tier'], $validated['pin']);

            return response()->json([
                'status' => 'success',
                'message' => "Congratulations! Your account has been upgraded to {$user->fresh()->getTierDisplayName()}.",
                'data' => [
                    'tier' => $user->fresh()->tier,
                    'wallet_balance' => (float) $user->fresh()->wallet->balance,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
