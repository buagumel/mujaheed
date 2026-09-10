<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Models\ExamPackage;
use App\Models\ExamPinTransaction;
use App\Services\ExamPinService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamPinController extends Controller
{
    protected ExamPinService $examPinService;

    public function __construct(ExamPinService $examPinService)
    {
        $this->examPinService = $examPinService;
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $packages = ExamPackage::where('status', 'active')->get();
        $recentTransactions = $user->examPinTransactions()->latest()->take(5)->get();

        return view('exam.index', compact('user', 'wallet', 'packages', 'recentTransactions'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'exam_code' => ['required', 'string', 'exists:exam_packages,code'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'pin' => ['required', 'digits:4'],
        ]);

        $user = Auth::user();

        if (!$user->hasTransactionPin()) {
            return back()->withErrors(['pin' => 'Please set a 4-digit transaction PIN before purchasing exam PINs.'])->withInput();
        }

        try {
            $transaction = $this->examPinService->purchase(
                $user,
                $request->exam_code,
                (int) $request->quantity,
                $request->pin
            );

            return redirect()->route('exam.show', $transaction->reference)
                ->with('success', "{$transaction->quantity}x {$transaction->exam_code} Exam PIN(s) generated successfully!")
                ->with('transaction_ref', $transaction->reference)
                ->with('transaction_amount', (float) $transaction->total_amount)
                ->with('receipt_url', route('exam.receipt', $transaction->reference));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(string $reference)
    {
        $user = Auth::user();
        $transaction = $user->examPinTransactions()->where('reference', $reference)->firstOrFail();

        return view('exam.show', compact('user', 'transaction'));
    }

    public function receipt(string $reference)
    {
        $user = Auth::user();
        $transaction = $user->examPinTransactions()->where('reference', $reference)->firstOrFail();

        return view('exam.receipt', compact('user', 'transaction'));
    }
}
