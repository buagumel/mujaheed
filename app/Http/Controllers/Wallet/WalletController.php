<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentService;
use App\Services\TransactionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    protected PaymentService $paymentService;
    protected TransactionService $transactionService;

    public function __construct(PaymentService $paymentService, TransactionService $transactionService)
    {
        $this->paymentService = $paymentService;
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $transactions = $this->transactionService->getUserWalletTransactions($user, 15);

        return view('wallet.index', compact('user', 'wallet', 'transactions'));
    }

    public function showFund()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $paymentMode = env('PAYMENT_PROVIDER', 'mock');

        return view('wallet.fund', compact('user', 'wallet', 'paymentMode'));
    }

    public function initiateFunding(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:500000'],
            'gateway' => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        try {
            $paymentTx = $this->paymentService->initializeFunding(
                $user,
                (float) $request->amount,
                $request->gateway
            );

            if (!empty($paymentTx->payment_url)) {
                return redirect()->away($paymentTx->payment_url);
            }

            return back()->withErrors(['amount' => 'Failed to initialize payment gateway.']);
        } catch (Exception $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }
    }

    public function mockCheckout(Request $request, string $reference)
    {
        $payment = PaymentTransaction::where('reference', $reference)->firstOrFail();

        if ($payment->status === 'successful') {
            return redirect()->route('wallet.index')->with('info', 'This payment has already been completed.');
        }

        return view('wallet.mock_checkout', compact('payment'));
    }

    public function completeMockCheckout(Request $request, string $reference)
    {
        $payment = PaymentTransaction::where('reference', $reference)->firstOrFail();

        if ($request->action === 'cancel') {
            $payment->update(['status' => 'failed']);
            return redirect()->route('wallet.fund')->with('error', 'Payment was cancelled.');
        }

        try {
            $this->paymentService->verifyAndCreditWallet($reference);
            return redirect()->route('wallet.index')
                ->with('success', '₦' . number_format($payment->amount, 2) . ' credited to your wallet successfully!')
                ->with('transaction_ref', $payment->reference)
                ->with('transaction_amount', (float) $payment->amount);
        } catch (Exception $e) {
            return redirect()->route('wallet.fund')->with('error', 'Funding verification failed: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (!$reference) {
            return redirect()->route('wallet.index')->with('error', 'No transaction reference found.');
        }

        try {
            $payment = $this->paymentService->verifyAndCreditWallet($reference);

            if ($payment->status === 'successful') {
                return redirect()->route('wallet.index')
                    ->with('success', '₦' . number_format($payment->amount, 2) . ' credited to your wallet successfully!')
                    ->with('transaction_ref', $payment->reference)
                    ->with('transaction_amount', (float) $payment->amount);
            }

            return redirect()->route('wallet.index')->with('error', 'Payment was not successful.');
        } catch (Exception $e) {
            return redirect()->route('wallet.index')->with('error', 'Verification error: ' . $e->getMessage());
        }
    }

    public function webhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $payload = $request->all();

        $result = $this->paymentService->handleWebhook($payload, $signature);

        return response()->json(['status' => $result['status']], $result['code'] ?? 200);
    }
}
