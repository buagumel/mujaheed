<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Services\MailConfigService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ApiProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/', 'unique:users,phone,' . $user->id],
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($phone, '234')) {
            $phone = '0' . substr($phone, 3);
        }

        $user->update([
            'name' => $validated['name'],
            'phone' => $phone,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'data' => [
                'user' => $user->fresh(),
            ],
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The provided current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.',
        ]);
    }

    public function setPin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pin' => ['required', 'string', 'digits:4', 'confirmed'],
        ]);

        $user = $request->user();
        $user->update([
            'transaction_pin' => Hash::make($validated['pin']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => '4-digit transaction PIN set successfully.',
        ]);
    }

    public function changePin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_pin' => ['required', 'string', 'digits:4'],
            'new_pin' => ['required', 'string', 'digits:4', 'confirmed', 'different:current_pin'],
        ]);

        $user = $request->user();

        if (!$user->verifyPin($validated['current_pin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'The current transaction PIN is incorrect.',
            ], 422);
        }

        $user->update([
            'transaction_pin' => Hash::make($validated['new_pin']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction PIN changed successfully.',
        ]);
    }

    public function sendPinResetOtp(Request $request): JsonResponse
    {
        $user = $request->user();
        $otp = (string) rand(100000, 999999);

        PasswordResetOtp::where('email', $user->email)->delete();
        PasswordResetOtp::create([
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15),
        ]);

        MailConfigService::sendPinResetOtp($user, $otp);

        return response()->json([
            'status' => 'success',
            'message' => 'A 6-digit verification code has been sent to your email.',
        ]);
    }

    public function resetPinWithOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'string', 'digits:6'],
            'pin' => ['required', 'string', 'digits:4', 'confirmed'],
        ]);

        $user = $request->user();

        $record = PasswordResetOtp::where('email', $user->email)
            ->where('otp', $validated['otp'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired OTP code.',
            ], 422);
        }

        $user->update([
            'transaction_pin' => Hash::make($validated['pin']),
        ]);

        $record->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction PIN reset successfully.',
        ]);
    }
}
