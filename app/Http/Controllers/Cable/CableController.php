<?php

namespace App\Http\Controllers\Cable;

use App\Http\Controllers\Controller;
use App\Models\CablePlan;
use App\Services\CableService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CableController extends Controller
{
    protected CableService $cableService;

    public function __construct(CableService $cableService)
    {
        $this->cableService = $cableService;
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $plans = CablePlan::where('status', 'active')->orderBy('selling_price')->get();

        return view('cable.index', compact('user', 'wallet', 'plans'));
    }

    public function getPlansByProvider(Request $request)
    {
        $provider = strtoupper($request->query('provider', 'DSTV'));
        $plans = CablePlan::where('provider', $provider)
            ->where('status', 'active')
            ->orderBy('selling_price')
            ->get();

        return response()->json($plans);
    }

    public function verifySmartcard(Request $request)
    {
        $request->validate([
            'provider' => ['required', 'string'],
            'smartcard_number' => ['required', 'string', 'min:6'],
        ]);

        $result = $this->cableService->verifySmartcard(
            $request->provider,
            $request->smartcard_number
        );

        return response()->json($result);
    }

    public function pay(Request $request)
    {
        $request->validate([
            'provider' => ['required', 'string'],
            'plan_id' => ['required', 'exists:cable_plans,id'],
            'smartcard_number' => ['required', 'string', 'min:6'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'pin' => ['required', 'digits:4'],
            'customer_name' => ['nullable', 'string'],
        ], [
            'pin.digits' => 'Transaction PIN must be 4 digits.',
        ]);

        $user = Auth::user();

        if (!$user->hasTransactionPin()) {
            return back()->withErrors(['pin' => 'You must set a 4-digit transaction PIN before making payments.'])->withInput();
        }

        try {
            $transaction = $this->cableService->pay(
                $user,
                (int) $request->plan_id,
                $request->smartcard_number,
                $request->phone,
                $request->pin,
                $request->customer_name
            );

            return redirect()->route('transactions.show', $transaction->reference)
                ->with('success', "Cable TV subscription renewed successfully!")
                ->with('transaction_ref', $transaction->reference)
                ->with('transaction_amount', (float) $transaction->amount)
                ->with('receipt_url', route('transactions.receipt', $transaction->reference));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
