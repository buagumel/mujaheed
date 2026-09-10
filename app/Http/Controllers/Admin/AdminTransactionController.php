<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\VtuTransaction;
use App\Services\NotificationService;
use App\Services\VTU\VTUService;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTransactionController extends Controller
{
    protected VTUService $vtuService;
    protected WalletService $walletService;
    protected NotificationService $notificationService;

    public function __construct(
        VTUService $vtuService,
        WalletService $walletService,
        NotificationService $notificationService
    ) {
        $this->vtuService = $vtuService;
        $this->walletService = $walletService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = VtuTransaction::with('user')->latest();

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('recipient', 'like', "%{$search}%")
                  ->orWhere('provider_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(VtuTransaction $transaction)
    {
        return view('admin.transactions.show', compact('transaction'));
    }

    public function retryStatus(VtuTransaction $transaction)
    {
        $result = $this->vtuService->checkStatus($transaction->reference, $transaction->provider_reference);

        if (($result['status'] ?? '') === 'successful') {
            $transaction->update(['status' => 'successful']);
            return back()->with('success', 'Transaction status confirmed successful from provider.');
        }

        return back()->with('info', 'Provider returned status: ' . ($result['status'] ?? 'unknown'));
    }

    public function refund(Request $request, VtuTransaction $transaction)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        if ($transaction->status === 'reversed' || $transaction->status === 'refunded') {
            return back()->with('error', 'This transaction has already been refunded.');
        }

        $admin = Auth::user();
        $user = $transaction->user;
        $refundAmount = (float) $transaction->amount;

        try {
            $this->walletService->refund(
                $user,
                $refundAmount,
                "Admin Refund: {$transaction->service_type} transaction {$transaction->reference}. Reason: {$request->reason}",
                $transaction->reference
            );

            $transaction->update([
                'status' => 'reversed',
                'error_message' => "Refunded by admin: {$request->reason}",
            ]);

            $this->notificationService->send(
                $user,
                'Transaction Refunded',
                "₦" . number_format($refundAmount, 2) . " has been refunded for transaction {$transaction->reference}.",
                'wallet',
                route('transactions.show', $transaction->reference)
            );

            AuditLog::record(
                'transaction_refund',
                "Admin {$admin->email} refunded ₦{$refundAmount} for transaction {$transaction->reference}. Reason: {$request->reason}",
                $user->id,
                ['admin_id' => $admin->id, 'transaction_id' => $transaction->id, 'reason' => $request->reason]
            );

            return back()->with('success', "Transaction {$transaction->reference} has been refunded to the user's wallet.");
        } catch (Exception $e) {
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }
}
