<?php

namespace App\Http\Controllers\Electricity;

use App\Http\Controllers\Controller;
use App\Models\ElectricityProvider;
use App\Services\ElectricityService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElectricityController extends Controller
{
    protected ElectricityService $electricityService;

    public function __construct(ElectricityService $electricityService)
    {
        $this->electricityService = $electricityService;
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $providers = ElectricityProvider::where('status', 'active')->get();

        return view('electricity.index', compact('user', 'wallet', 'providers'));
    }

    public function verifyMeter(Request $request)
    {
        $request->validate([
            'disco' => ['required', 'string'],
            'meter_number' => ['required', 'string', 'min:6'],
            'type' => ['required', 'in:prepaid,postpaid'],
        ]);

        $result = $this->electricityService->verifyMeter(
            $request->disco,
            $request->meter_number,
            $request->type
        );

        return response()->json($result);
    }

    public function pay(Request $request)
    {
        $request->validate([
            'disco' => ['required', 'string'],
            'meter_number' => ['required', 'string', 'min:6'],
            'meter_type' => ['required', 'in:prepaid,postpaid'],
            'amount' => ['required', 'numeric', 'min:500', 'max:100000'],
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
            $transaction = $this->electricityService->pay(
                $user,
                $request->disco,
                $request->meter_number,
                $request->meter_type,
                (float) $request->amount,
                $request->phone,
                $request->pin,
                $request->customer_name
            );

            return redirect()->route('transactions.show', $transaction->reference)
                ->with('success', "Electricity payment of ₦" . number_format($request->amount, 2) . " was processed successfully!")
                ->with('transaction_ref', $transaction->reference)
                ->with('transaction_amount', (float) $transaction->amount)
                ->with('receipt_url', route('transactions.receipt', $transaction->reference));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
