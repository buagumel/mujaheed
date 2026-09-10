<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Wallet;
use App\Services\NotificationService;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWalletController extends Controller
{
    protected WalletService $walletService;
    protected NotificationService $notificationService;

    public function __construct(WalletService $walletService, NotificationService $notificationService)
    {
        $this->walletService = $walletService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Wallet::with('user')->latest('updated_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $wallets = $query->paginate(15)->withQueryString();
        $totalBalance = Wallet::sum('balance');

        return view('admin.wallets.index', compact('wallets', 'totalBalance'));
    }

    public function adjust(Request $request, Wallet $wallet)
    {
        $request->validate([
            'action_type' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:1'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $user = $wallet->user;
        $admin = Auth::user();
        $amount = (float) $request->amount;
        $reason = $request->reason;

        try {
            if ($request->action_type === 'credit') {
                $this->walletService->credit(
                    $user,
                    $amount,
                    "Admin Credit Adjustment: {$reason} (Admin: {$admin->name})",
                    null,
                    ['admin_id' => $admin->id, 'reason' => $reason]
                );

                $this->notificationService->send(
                    $user,
                    'Wallet Adjusted by Admin',
                    "₦" . number_format($amount, 2) . " has been credited to your wallet. Reason: {$reason}",
                    'wallet'
                );
            } else {
                $this->walletService->debit(
                    $user,
                    $amount,
                    "Admin Debit Adjustment: {$reason} (Admin: {$admin->name})",
                    null,
                    ['admin_id' => $admin->id, 'reason' => $reason]
                );

                $this->notificationService->send(
                    $user,
                    'Wallet Adjusted by Admin',
                    "₦" . number_format($amount, 2) . " has been deducted from your wallet. Reason: {$reason}",
                    'wallet'
                );
            }

            AuditLog::record(
                'wallet_adjustment',
                "Admin {$admin->email} performed {$request->action_type} of ₦{$amount} on user {$user->email}. Reason: {$reason}",
                $user->id,
                ['admin_id' => $admin->id, 'action_type' => $request->action_type, 'amount' => $amount, 'reason' => $reason]
            );

            return back()->with('success', "Wallet {$request->action_type} adjustment of ₦" . number_format($amount, 2) . " applied successfully.");
        } catch (Exception $e) {
            return back()->with('error', 'Wallet adjustment failed: ' . $e->getMessage());
        }
    }
}
