<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AirtimeCashRequest;
use App\Models\SystemSetting;
use App\Services\AirtimeCashService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAirtimeCashController extends Controller
{
    protected AirtimeCashService $airtimeCashService;

    public function __construct(AirtimeCashService $airtimeCashService)
    {
        $this->airtimeCashService = $airtimeCashService;
    }

    public function rates(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'rates' => [
                    'MTN' => AirtimeCashService::getRate('MTN'),
                    'AIRTEL' => AirtimeCashService::getRate('AIRTEL'),
                    'GLO' => AirtimeCashService::getRate('GLO'),
                    '9MOBILE' => AirtimeCashService::getRate('9MOBILE'),
                ],
                'receivers' => [
                    'MTN' => AirtimeCashService::getReceiverPhone('MTN'),
                    'AIRTEL' => AirtimeCashService::getReceiverPhone('AIRTEL'),
                    'GLO' => AirtimeCashService::getReceiverPhone('GLO'),
                    '9MOBILE' => AirtimeCashService::getReceiverPhone('9MOBILE'),
                ],
            ],
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'network' => ['required', 'string', 'in:MTN,AIRTEL,GLO,9MOBILE'],
            'amount' => ['required', 'numeric', 'min:500'],
            'sender_phone' => ['required', 'string'],
            'payout_method' => ['required', 'in:wallet,bank'],
            'bank_name' => ['nullable', 'required_if:payout_method,bank', 'string'],
            'bank_account_number' => ['nullable', 'required_if:payout_method,bank', 'string'],
            'bank_account_name' => ['nullable', 'required_if:payout_method,bank', 'string'],
        ]);

        $user = $request->user();

        try {
            $req = $this->airtimeCashService->submitRequest(
                $user,
                $validated['network'],
                (float) $validated['amount'],
                $validated['sender_phone'],
                $validated['payout_method'],
                $validated['bank_name'] ?? null,
                $validated['bank_account_number'] ?? null,
                $validated['bank_account_name'] ?? null
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Airtime conversion request submitted successfully.',
                'data' => [
                    'request' => $req,
                    'ussd_instruction' => AirtimeCashService::getUssdInstruction($req->network, $req->amount, $req->receiver_phone),
                ],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function history(Request $request): JsonResponse
    {
        $requests = $request->user()->airtimeCashRequests()->latest()->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $requests,
        ]);
    }
}
