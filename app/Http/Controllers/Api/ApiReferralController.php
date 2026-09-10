<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\ReferralService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiReferralController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $code = $user->getOrCreateReferralCode();

        return response()->json([
            'status' => 'success',
            'data' => [
                'referral_code' => $code,
                'referral_link' => url('/register?ref=' . $code),
                'referral_balance' => (float) ($user->referral_balance ?? 0.00),
                'total_earned' => (float) $user->referralCommissions()->sum('amount'),
                'total_referred' => $user->referredUsers()->count(),
                'min_withdrawal' => (float) SystemSetting::get('referral_min_withdrawal', 100.00),
                'commissions' => $user->referralCommissions()->latest()->take(10)->get(),
            ],
        ]);
    }

    public function withdraw(Request $request): JsonResponse
    {
        $min = (float) SystemSetting::get('referral_min_withdrawal', 100.00);
        $validated = $request->validate([
            'amount' => ['required', 'numeric', "min:{$min}"],
        ]);

        $user = $request->user();

        try {
            $this->referralService->transferToMainWallet($user, (float) $validated['amount']);

            return response()->json([
                'status' => 'success',
                'message' => 'Referral bonus transferred to your main wallet successfully.',
                'data' => [
                    'referral_balance' => (float) $user->fresh()->referral_balance,
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
