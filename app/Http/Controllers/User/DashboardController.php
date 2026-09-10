<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NetworkSetting;
use App\Models\VtuTransaction;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();

        $totalSpent = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'successful')
            ->sum('amount');

        $totalFunded = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'successful')
            ->sum('amount');

        $recentTransactions = VtuTransaction::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $networks = NetworkSetting::where('status', 'active')->get();

        return view('dashboard.index', compact(
            'user',
            'wallet',
            'totalSpent',
            'totalFunded',
            'recentTransactions',
            'networks'
        ));
    }
}
