<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NetworkSetting;
use App\Services\AirtimeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAirtimeController extends Controller
{
    protected AirtimeService $airtimeService;

    public function __construct(AirtimeService $airtimeService)
    {
        $this->airtimeService = $airtimeService;
    }

    public function networks(): JsonResponse
    {
        $networks = NetworkSetting::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'data' => $networks,
        ]);
    }

    public function purchase(Request $request): JsonResponse
    {
        $request->validate([
            'network' => ['required', 'string', 'in:MTN,AIRTEL,GLO,9MOBILE'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'amount' => ['required', 'numeric', 'min:50', 'max:50000'],
            'pin' => ['required', 'digits:4'],
        ]);

        $user = $request->user();

        try {
            $transaction = $this->airtimeService->purchase(
                $user,
                $request->network,
                $request->phone,
                (float) $request->amount,
                $request->pin
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Airtime purchase successful.',
                'data' => $transaction,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
