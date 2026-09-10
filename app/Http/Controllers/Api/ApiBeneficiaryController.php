<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiBeneficiaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Beneficiary::where('user_id', $user->id)->latest();

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_type' => ['required', 'in:airtime,data,electricity,cable'],
            'identifier' => ['required', 'string', 'max:50'],
            'name_nickname' => ['nullable', 'string', 'max:100'],
            'network_or_disco' => ['nullable', 'string', 'max:50'],
        ]);

        $beneficiary = Beneficiary::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'service_type' => $validated['service_type'],
                'identifier' => $validated['identifier'],
            ],
            [
                'name_nickname' => $validated['name_nickname'] ?? null,
                'network_or_disco' => $validated['network_or_disco'] ?? null,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Beneficiary saved successfully.',
            'data' => $beneficiary,
        ]);
    }

    public function destroy(Request $request, Beneficiary $beneficiary): JsonResponse
    {
        if ($beneficiary->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $beneficiary->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Beneficiary deleted successfully.',
        ]);
    }
}
