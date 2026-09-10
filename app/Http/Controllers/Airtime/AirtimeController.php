<?php

namespace App\Http\Controllers\Airtime;

use App\Http\Controllers\Controller;
use App\Models\NetworkSetting;
use App\Services\AirtimeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AirtimeController extends Controller
{
    protected AirtimeService $airtimeService;

    public function __construct(AirtimeService $airtimeService)
    {
        $this->airtimeService = $airtimeService;
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $networks = NetworkSetting::where('status', 'active')->get();

        return view('airtime.index', compact('user', 'wallet', 'networks'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'network' => ['required', 'string', 'in:MTN,AIRTEL,GLO,9MOBILE'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'amount' => ['required', 'numeric', 'min:50', 'max:50000'],
            'pin' => ['required', 'digits:4'],
        ], [
            'phone.regex' => 'Please enter a valid 11-digit Nigerian phone number.',
            'pin.digits' => 'Transaction PIN must be 4 digits.',
        ]);

        $user = Auth::user();

        if (!$user->hasTransactionPin()) {
            return back()->withErrors(['pin' => 'You must set a 4-digit transaction PIN before making purchases.'])->withInput();
        }

        try {
            $transaction = $this->airtimeService->purchase(
                $user,
                $request->network,
                $request->phone,
                (float) $request->amount,
                $request->pin
            );

            return redirect()->route('transactions.show', $transaction->reference)
                ->with('success', "Airtime recharge of ₦" . number_format($request->amount, 2) . " to {$request->phone} was successful!")
                ->with('transaction_ref', $transaction->reference)
                ->with('transaction_amount', (float) $request->amount)
                ->with('receipt_url', route('transactions.receipt', $transaction->reference));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->with('error', $e->getMessage())->withInput();
        }
    }
}
