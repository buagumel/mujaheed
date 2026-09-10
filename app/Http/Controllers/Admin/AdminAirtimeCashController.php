<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirtimeCashRequest;
use App\Services\AirtimeCashService;
use Exception;
use Illuminate\Http\Request;

class AdminAirtimeCashController extends Controller
{
    protected AirtimeCashService $airtimeCashService;

    public function __construct(AirtimeCashService $airtimeCashService)
    {
        $this->airtimeCashService = $airtimeCashService;
    }

    public function index(Request $request)
    {
        $query = AirtimeCashRequest::with('user')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->network) {
            $query->where('network', $request->network);
        }

        $requests = $query->paginate(15);
        $pendingCount = AirtimeCashRequest::where('status', 'pending')->count();
        $approvedCount = AirtimeCashRequest::where('status', 'approved')->count();

        $rates = [
            'MTN' => (float) \App\Models\SystemSetting::get('airtime_cash_rate_mtn', 80.00),
            'AIRTEL' => (float) \App\Models\SystemSetting::get('airtime_cash_rate_airtel', 75.00),
            'GLO' => (float) \App\Models\SystemSetting::get('airtime_cash_rate_glo', 70.00),
            '9MOBILE' => (float) \App\Models\SystemSetting::get('airtime_cash_rate_9mobile', 70.00),
        ];

        $receivers = [
            'MTN' => \App\Models\SystemSetting::get('airtime_cash_receiver_mtn', '08031234567'),
            'AIRTEL' => \App\Models\SystemSetting::get('airtime_cash_receiver_airtel', '08021234567'),
            'GLO' => \App\Models\SystemSetting::get('airtime_cash_receiver_glo', '08051234567'),
            '9MOBILE' => \App\Models\SystemSetting::get('airtime_cash_receiver_9mobile', '08091234567'),
        ];

        return view('admin.airtime_cash.index', compact('requests', 'pendingCount', 'approvedCount', 'rates', 'receivers'));
    }

    public function updateRates(Request $request)
    {
        $validated = $request->validate([
            'rate_mtn' => ['required', 'numeric', 'min:0', 'max:100'],
            'rate_airtel' => ['required', 'numeric', 'min:0', 'max:100'],
            'rate_glo' => ['required', 'numeric', 'min:0', 'max:100'],
            'rate_9mobile' => ['required', 'numeric', 'min:0', 'max:100'],
            'receiver_mtn' => ['required', 'string'],
            'receiver_airtel' => ['required', 'string'],
            'receiver_glo' => ['required', 'string'],
            'receiver_9mobile' => ['required', 'string'],
        ]);

        \App\Models\SystemSetting::set('airtime_cash_rate_mtn', $validated['rate_mtn']);
        \App\Models\SystemSetting::set('airtime_cash_rate_airtel', $validated['rate_airtel']);
        \App\Models\SystemSetting::set('airtime_cash_rate_glo', $validated['rate_glo']);
        \App\Models\SystemSetting::set('airtime_cash_rate_9mobile', $validated['rate_9mobile']);

        \App\Models\SystemSetting::set('airtime_cash_receiver_mtn', $validated['receiver_mtn']);
        \App\Models\SystemSetting::set('airtime_cash_receiver_airtel', $validated['receiver_airtel']);
        \App\Models\SystemSetting::set('airtime_cash_receiver_glo', $validated['receiver_glo']);
        \App\Models\SystemSetting::set('airtime_cash_receiver_9mobile', $validated['receiver_9mobile']);

        return back()->with('success', 'Airtime to Cash payout rates and receiving phone numbers updated successfully!');
    }

    public function approve(Request $request, int $id)
    {
        $airtimeRequest = AirtimeCashRequest::findOrFail($id);

        try {
            $this->airtimeCashService->approveRequest($airtimeRequest, $request->admin_note ?? 'Airtime verified and credited.');

            return back()->with('success', "Request {$airtimeRequest->reference} approved and customer credited successfully!");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, int $id)
    {
        $airtimeRequest = AirtimeCashRequest::findOrFail($id);

        try {
            $this->airtimeCashService->rejectRequest($airtimeRequest, $request->reason ?? 'Airtime transfer was not received.');

            return back()->with('success', "Request {$airtimeRequest->reference} was rejected.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
