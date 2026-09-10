<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiCouponController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'service_type' => ['required', 'string'],
        ]);

        $user = $request->user();
        $code = strtoupper(trim($request->code));
        $amount = (float) $request->amount;
        $serviceType = strtolower(trim($request->service_type));

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid coupon code. Please check and try again.',
            ], 404);
        }

        $result = $coupon->isValidFor($user, $amount, $serviceType);

        if (!$result['valid']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], 422);
        }

        $finalPayable = max(0, $amount - $result['discount']);

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'data' => [
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => (float) $coupon->discount_value,
                'discount_applied' => $result['discount'],
                'original_amount' => $amount,
                'final_payable' => $finalPayable,
            ],
        ]);
    }
}
