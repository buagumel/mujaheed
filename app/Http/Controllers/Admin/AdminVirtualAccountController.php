<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Models\VirtualBankAccount;
use App\Services\Payment\PayrantService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminVirtualAccountController extends Controller
{
    protected PayrantService $payrantService;
    protected WalletService $walletService;

    public function __construct(PayrantService $payrantService, WalletService $walletService)
    {
        $this->payrantService = $payrantService;
        $this->walletService = $walletService;
    }

    public function index(Request $request)
    {
        $query = VirtualBankAccount::with(['user.wallet'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('account_number', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('account_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        $accounts = $query->paginate(20)->withQueryString();

        // Statistics
        $totalAccounts = VirtualBankAccount::count();
        $activeAccounts = VirtualBankAccount::where('status', 'active')->count();
        $totalFundedVolume = VirtualBankAccount::sum('total_funded');
        $recentFundings = PaymentTransaction::where('payment_provider', 'payrant')
            ->where('status', 'successful')
            ->whereDate('created_at', today())
            ->sum('amount');

        return view('admin.virtual_accounts.index', compact(
            'accounts',
            'totalAccounts',
            'activeAccounts',
            'totalFundedVolume',
            'recentFundings'
        ));
    }

    public function show(VirtualBankAccount $virtualAccount)
    {
        $virtualAccount->load(['user.wallet', 'user.vtuTransactions' => function ($q) {
            $q->latest()->take(10);
        }]);

        // Query live transactions from Payrant API
        $liveTransactions = $this->payrantService->getVirtualAccountTransactions($virtualAccount->account_number);

        // Recent automated credit transactions for this account
        $paymentTransactions = PaymentTransaction::where('user_id', $virtualAccount->user_id)
            ->where('payment_provider', 'payrant')
            ->latest()
            ->take(15)
            ->get();

        return view('admin.virtual_accounts.show', compact(
            'virtualAccount',
            'liveTransactions',
            'paymentTransactions'
        ));
    }

    public function regenerate(User $user)
    {
        try {
            $va = $this->payrantService->createVirtualAccount($user);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'regenerate_virtual_account',
                'description' => "Regenerated Payrant Virtual Account ({$va->account_number}) for user {$user->name} ({$user->email})",
                'ip_address' => request()->ip(),
            ]);

            return back()->with('success', "Dedicated Virtual Account generated/synced successfully: {$va->account_number} ({$va->bank_name})");
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to generate virtual account: ' . $e->getMessage());
        }
    }

    public function reconcile(Request $request, VirtualBankAccount $virtualAccount)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:50', 'max:1000000'],
            'session_id' => ['required', 'string', 'max:100'],
            'payer_name' => ['required', 'string', 'max:100'],
            'payer_bank' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $virtualAccount->user;
        $amount = (float) $validated['amount'];
        $sessionId = trim($validated['session_id']);
        $ref = 'RECON-' . strtoupper(Str::random(12));

        // Check if session ID or reference is already processed
        $existing = PaymentTransaction::where('provider_reference', $sessionId)->first();
        if ($existing && $existing->status === 'successful') {
            return back()->with('error', 'This bank transfer session ID has already been credited to user #' . $existing->user_id);
        }

        DB::transaction(function () use ($user, $virtualAccount, $amount, $sessionId, $ref, $validated) {
            PaymentTransaction::create([
                'user_id' => $user->id,
                'payment_provider' => 'payrant_manual_reconcile',
                'amount' => $amount,
                'fee' => 0.00,
                'status' => 'successful',
                'channel' => 'manual_bank_reconciliation',
                'provider_reference' => $sessionId,
                'paid_at' => now(),
                'metadata' => [
                    'admin_id' => Auth::id(),
                    'admin_name' => Auth::user()->name,
                    'payer_name' => $validated['payer_name'],
                    'payer_bank' => $validated['payer_bank'],
                    'virtual_account' => $virtualAccount->account_number,
                    'notes' => $validated['notes'] ?? '',
                ],
            ]);

            $this->walletService->credit(
                $user,
                $amount,
                "Manual Reconciliation for Bank Transfer from {$validated['payer_name']} ({$validated['payer_bank']})",
                $ref,
                [
                    'reconciled_by' => Auth::id(),
                    'session_id' => $sessionId,
                    'virtual_account' => $virtualAccount->account_number,
                ]
            );

            $virtualAccount->increment('total_funded', $amount);
            $virtualAccount->update(['last_funded_at' => now()]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'reconcile_virtual_account_payment',
                'description' => "Manually reconciled ₦{$amount} to {$user->name} ({$user->email}) for VA {$virtualAccount->account_number} [Session: {$sessionId}]",
                'ip_address' => request()->ip(),
            ]);
        });

        return back()->with('success', "Payment of ₦" . number_format($amount, 2) . " successfully credited to {$user->name}'s wallet!");
    }
}
