<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NetworkSetting;
use App\Models\VtuTransaction;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
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

        return response()->json([
            'status' => 'success',
            'data' => [
                'wallet' => $wallet,
                'total_spent' => (float) $totalSpent,
                'total_funded' => (float) $totalFunded,
                'recent_transactions' => $recentTransactions,
                'networks' => $networks,
            ],
        ]);
    }
}
