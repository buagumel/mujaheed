<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VtuTransaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalUsers = User::where('role', 'customer')->count();
        $activeUsers = User::where('role', 'customer')->where('status', 'active')->count();
        $totalWalletBalance = Wallet::sum('balance');

        $todayRevenue = VtuTransaction::where('status', 'successful')
            ->whereDate('created_at', today())
            ->sum('amount');

        $todayTransactions = VtuTransaction::whereDate('created_at', today())->count();
        $successfulTransactions = VtuTransaction::where('status', 'successful')->count();
        $failedTransactions = VtuTransaction::where('status', 'failed')->count();
        $pendingTransactions = VtuTransaction::where('status', 'pending')->count();

        $recentTransactions = VtuTransaction::with('user')->latest()->take(8)->get();
        $recentUsers = User::where('role', 'customer')->latest()->take(5)->get();

        // Chart data: Monthly revenue for the last 6 months
        $dateExpr = DB::connection()->getDriverName() === 'sqlite'
            ? 'strftime("%Y-%m", created_at)'
            : 'DATE_FORMAT(created_at, "%Y-%m")';

        $monthlyRevenue = VtuTransaction::where('status', 'successful')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("{$dateExpr} as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Service distribution counts
        $serviceDistribution = VtuTransaction::select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type')
            ->pluck('count', 'service_type')
            ->toArray();

        return view('admin.dashboard', compact(
            'user',
            'totalUsers',
            'activeUsers',
            'totalWalletBalance',
            'todayRevenue',
            'todayTransactions',
            'successfulTransactions',
            'failedTransactions',
            'pendingTransactions',
            'recentTransactions',
            'recentUsers',
            'monthlyRevenue',
            'serviceDistribution'
        ));
    }
}
