<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CablePlan;
use App\Services\CableService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiCableController extends Controller
{
    protected CableService $cableService;

    public function __construct(CableService $cableService)
    {
        $this->cableService = $cableService;
    }

    public function plans(Request $request): JsonResponse
    {
        $provider = $request->query('provider');
        $query = CablePlan::where('status', 'active')->orderBy('selling_price');

        if ($provider) {
            $query->where('provider', strtoupper($provider));
        }

        $plans = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $plans,
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => ['required', 'string'],
            'smartcard_number' => ['required', 'string'],
        ]);

        $result = $this->cableService->verifySmartcard(
            $request->provider,
            $request->smartcard_number
        );

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'data' => $result,
        ]);
    }

    public function pay(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => ['required', 'string'],
            'plan_id' => ['required', 'exists:cable_plans,id'],
            'smartcard_number' => ['required', 'string'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'pin' => ['required', 'digits:4'],
            'customer_name' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        try {
            $transaction = $this->cableService->pay(
                $user,
                (int) $request->plan_id,
                $request->smartcard_number,
                $request->phone,
                $request->pin,
                $request->customer_name
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Cable TV subscription renewed successfully.',
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
