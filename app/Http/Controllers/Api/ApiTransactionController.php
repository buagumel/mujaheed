<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VtuTransaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiTransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filters = [
            'status' => $request->query('status'),
            'service_type' => $request->query('service_type'),
            'search' => $request->query('search'),
        ];

        $transactions = $this->transactionService->getUserVtuTransactions($user, $filters, 20);

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
        ]);
    }

    public function show(Request $request, string $reference): JsonResponse
    {
        $user = $request->user();
        $transaction = VtuTransaction::where('reference', $reference)
            ->where('user_id', $user->id)
            ->first();

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaction,
        ]);
    }
}
