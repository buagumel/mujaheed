<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamPackage;
use App\Services\ExamPinService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiExamPinController extends Controller
{
    protected ExamPinService $examPinService;

    public function __construct(ExamPinService $examPinService)
    {
        $this->examPinService = $examPinService;
    }

    public function packages(Request $request): JsonResponse
    {
        $user = $request->user();
        $packages = ExamPackage::where('status', 'active')->get()->map(function ($pkg) use ($user) {
            return [
                'id' => $pkg->id,
                'code' => $pkg->code,
                'name' => $pkg->name,
                'price' => $pkg->getPriceForTier($user->tier),
                'standard_price' => (float) $pkg->standard_price,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $packages,
        ]);
    }

    public function purchase(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_code' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'pin' => ['required', 'string', 'digits:4'],
        ]);

        $user = $request->user();

        try {
            $transaction = $this->examPinService->purchase(
                $user,
                $validated['exam_code'],
                (int) $validated['quantity'],
                $validated['pin']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Exam PINs generated and delivered successfully.',
                'data' => [
                    'transaction' => $transaction,
                    'pins' => $transaction->pins_data,
                    'wallet_balance' => (float) $user->fresh()->wallet->balance,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
