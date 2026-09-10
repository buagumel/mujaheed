<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    public function index()
    {
        $user = Auth::user();
        $user->getOrCreateReferralCode();

        $referralCode = $user->referral_code;
        $referralLink = url('/register?ref=' . $referralCode);
        $referredUsers = $user->referredUsers()->latest()->paginate(10);
        $commissions = $user->referralCommissions()->latest()->take(10)->get();

        $totalEarned = $user->referralCommissions()->sum('amount');
        $referralBalance = $user->referral_balance ?? 0.00;
        $totalReferralsCount = $user->referredUsers()->count();

        return view('referrals.index', compact(
            'user',
            'referralCode',
            'referralLink',
            'referredUsers',
            'commissions',
            'totalEarned',
            'referralBalance',
            'totalReferralsCount'
        ));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
        ]);

        $user = Auth::user();

        try {
            $this->referralService->transferToMainWallet($user, (float) $request->amount);

            return redirect()->route('referrals.index')
                ->with('success', "₦" . number_format($request->amount, 2) . " referral bonus transferred to your main wallet successfully!");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
