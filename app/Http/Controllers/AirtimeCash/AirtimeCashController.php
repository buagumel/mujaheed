<?php

namespace App\Http\Controllers\AirtimeCash;

use App\Http\Controllers\Controller;
use App\Models\AirtimeCashRequest;
use App\Services\AirtimeCashService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AirtimeCashController extends Controller
{
    protected AirtimeCashService $airtimeCashService;

    public function __construct(AirtimeCashService $airtimeCashService)
    {
        $this->airtimeCashService = $airtimeCashService;
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();

        $rates = [
            'MTN' => AirtimeCashService::getRate('MTN'),
            'AIRTEL' => AirtimeCashService::getRate('AIRTEL'),
            'GLO' => AirtimeCashService::getRate('GLO'),
            '9MOBILE' => AirtimeCashService::getRate('9MOBILE'),
        ];

        $receivers = [
            'MTN' => AirtimeCashService::getReceiverPhone('MTN'),
            'AIRTEL' => AirtimeCashService::getReceiverPhone('AIRTEL'),
            'GLO' => AirtimeCashService::getReceiverPhone('GLO'),
            '9MOBILE' => AirtimeCashService::getReceiverPhone('9MOBILE'),
        ];

        $requests = $user->airtimeCashRequests()->latest()->take(10)->get();

        return view('airtime_cash.index', compact('user', 'wallet', 'rates', 'receivers', 'requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'network' => ['required', 'string', 'in:MTN,AIRTEL,GLO,9MOBILE'],
            'amount' => ['required', 'numeric', 'min:500', 'max:50000'],
            'sender_phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'payout_method' => ['required', 'string', 'in:wallet,bank'],
            'bank_name' => ['nullable', 'required_if:payout_method,bank', 'string'],
            'bank_account_number' => ['nullable', 'required_if:payout_method,bank', 'string', 'digits:10'],
            'bank_account_name' => ['nullable', 'required_if:payout_method,bank', 'string'],
        ]);

        $user = Auth::user();

        try {
            $req = $this->airtimeCashService->submitRequest(
                $user,
                $request->network,
                (float) $request->amount,
                $request->sender_phone,
                $request->payout_method,
                $request->bank_name,
                $request->bank_account_number,
                $request->bank_account_name
            );

            return redirect()->route('airtime_cash.show', $req->reference)
                ->with('success', "Airtime to cash request of ₦" . number_format($req->amount, 2) . " submitted! Please transfer the airtime using the displayed instructions.")
                ->with('transaction_ref', $req->reference);
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(string $reference)
    {
        $user = Auth::user();
        $request = $user->airtimeCashRequests()->where('reference', $reference)->firstOrFail();

        $ussdCode = AirtimeCashService::getUssdInstruction($request->network, (float)$request->amount, $request->receiver_phone);

        return view('airtime_cash.show', compact('user', 'request', 'ussdCode'));
    }
}
