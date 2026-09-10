<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\DataPlan;
use App\Services\DataService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataController extends Controller
{
    protected DataService $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $plans = DataPlan::where('status', 'active')->orderBy('selling_price')->get();

        return view('data.index', compact('user', 'wallet', 'plans'));
    }

    public function getPlansByNetwork(Request $request)
    {
        $network = strtoupper($request->query('network', 'MTN'));
        $plans = DataPlan::where('network', $network)
            ->where('status', 'active')
            ->orderBy('selling_price')
            ->get();

        return response()->json($plans);
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:data_plans,id'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'pin' => ['required', 'digits:4'],
        ], [
            'phone.regex' => 'Please enter a valid 11-digit Nigerian phone number.',
            'pin.digits' => 'Transaction PIN must be 4 digits.',
        ]);

        $user = Auth::user();

        if (!$user->hasTransactionPin()) {
            return back()->withErrors(['pin' => 'You must set a 4-digit transaction PIN before purchasing data.'])->withInput();
        }

        try {
            $transaction = $this->dataService->purchase(
                $user,
                (int) $request->plan_id,
                $request->phone,
                $request->pin
            );

            return redirect()->route('transactions.show', $transaction->reference)
                ->with('success', "Data bundle successfully sent to {$request->phone}!")
                ->with('transaction_ref', $transaction->reference)
                ->with('transaction_amount', (float) $transaction->amount)
                ->with('receipt_url', route('transactions.receipt', $transaction->reference));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->with('error', $e->getMessage())->withInput();
        }
    }
}
