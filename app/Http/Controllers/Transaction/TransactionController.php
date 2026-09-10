<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\VtuTransaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = [
            'status' => $request->query('status'),
            'service_type' => $request->query('service_type'),
            'search' => $request->query('search'),
        ];

        $transactions = $this->transactionService->getUserVtuTransactions($user, $filters, 15);

        return view('transactions.index', compact('user', 'transactions', 'filters'));
    }

    public function show(string $reference)
    {
        $user = Auth::user();
        $transaction = VtuTransaction::where('reference', $reference)
            ->where(function ($q) use ($user) {
                if (!$user->isAdmin()) {
                    $q->where('user_id', $user->id);
                }
            })
            ->firstOrFail();

        return view('transactions.show', compact('user', 'transaction'));
    }

    public function receipt(string $reference)
    {
        $user = Auth::user();
        $transaction = VtuTransaction::where('reference', $reference)
            ->where(function ($q) use ($user) {
                if (!$user->isAdmin()) {
                    $q->where('user_id', $user->id);
                }
            })
            ->firstOrFail();

        return view('transactions.receipt', compact('user', 'transaction'));
    }
}
