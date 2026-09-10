<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataPlan;
use App\Models\NetworkSetting;
use App\Services\DataService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiDataController extends Controller
{
    protected DataService $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function networks(): JsonResponse
    {
        $networks = NetworkSetting::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'data' => $networks,
        ]);
    }

    public function plans(Request $request): JsonResponse
    {
        $network = $request->query('network');
        $query = DataPlan::where('status', 'active')->orderBy('selling_price');

        if ($network) {
            $query->where('network', strtoupper($network));
        }

        $plans = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $plans,
        ]);
    }

    public function purchase(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => ['required', 'exists:data_plans,id'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'pin' => ['required', 'digits:4'],
        ]);

        $user = $request->user();

        try {
            $transaction = $this->dataService->purchase(
                $user,
                (int) $request->plan_id,
                $request->phone,
                $request->pin
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Data purchase successful.',
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
