<?php

namespace App\Http\Controllers\UserTier;

use App\Http\Controllers\Controller;
use App\Models\DataPlan;
use App\Models\ExamPackage;
use App\Models\NetworkSetting;
use App\Services\TierUpgradeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTierController extends Controller
{
    protected TierUpgradeService $upgradeService;

    public function __construct(TierUpgradeService $upgradeService)
    {
        $this->upgradeService = $upgradeService;
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();

        $dataPlans = DataPlan::where('status', 'active')->take(6)->get();
        $examPackages = ExamPackage::where('status', 'active')->get();
        $networks = NetworkSetting::where('status', 'active')->get();

        return view('tier.index', compact('user', 'wallet', 'dataPlans', 'examPackages', 'networks'));
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'tier' => ['required', 'string', 'in:reseller,vip'],
            'pin' => ['required', 'digits:4'],
        ]);

        $user = Auth::user();

        if (!$user->hasTransactionPin()) {
            return back()->withErrors(['pin' => 'Please set a 4-digit transaction PIN first.']);
        }

        try {
            $this->upgradeService->upgrade($user, $request->tier, $request->pin);

            return redirect()->route('tier.index')
                ->with('success', "🎉 Your account has been upgraded to {$user->getTierDisplayName()} successfully!");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
