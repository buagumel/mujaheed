<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Services\TransactionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiWalletController extends Controller
{
    protected PaymentService $paymentService;
    protected TransactionService $transactionService;

    public function __construct(PaymentService $paymentService, TransactionService $transactionService)
    {
        $this->paymentService = $paymentService;
        $this->transactionService = $transactionService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();
        $virtualAccounts = $user->getOrCreateVirtualAccounts();

        return response()->json([
            'status' => 'success',
            'data' => [
                'wallet' => $wallet,
                'virtual_accounts' => $virtualAccounts,
            ],
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $user = $request->user();
        $transactions = $this->transactionService->getUserWalletTransactions($user, 20);

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
        ]);
    }

    public function fund(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:500000'],
            'gateway' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        try {
            $paymentTx = $this->paymentService->initializeFunding(
                $user,
                (float) $request->amount,
                $request->gateway
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Payment initialized.',
                'data' => $paymentTx,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
