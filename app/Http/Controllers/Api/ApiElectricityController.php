<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ElectricityProvider;
use App\Services\ElectricityService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiElectricityController extends Controller
{
    protected ElectricityService $electricityService;

    public function __construct(ElectricityService $electricityService)
    {
        $this->electricityService = $electricityService;
    }

    public function providers(): JsonResponse
    {
        $providers = ElectricityProvider::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'data' => $providers,
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'disco' => ['required', 'string'],
            'meter_number' => ['required', 'string'],
            'type' => ['required', 'in:prepaid,postpaid'],
        ]);

        $result = $this->electricityService->verifyMeter(
            $request->disco,
            $request->meter_number,
            $request->type
        );

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'data' => $result,
        ]);
    }

    public function pay(Request $request): JsonResponse
    {
        $request->validate([
            'disco' => ['required', 'string'],
            'meter_number' => ['required', 'string'],
            'meter_type' => ['required', 'in:prepaid,postpaid'],
            'amount' => ['required', 'numeric', 'min:500', 'max:100000'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'pin' => ['required', 'digits:4'],
            'customer_name' => ['nullable', 'string'],
        ]);

        $user = $request->user();

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

            return response()->json([
                'status' => 'success',
                'message' => 'Electricity payment successful.',
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
