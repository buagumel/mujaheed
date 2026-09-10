<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\OtpService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ApiAuthController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function register(Request $request): JsonResponse
    {
        if (SystemSetting::get('allow_registration', 'open') === 'closed') {
            $closedMessage = SystemSetting::get('registration_closed_message', 'New user registrations are currently closed. Please contact customer support.');
            return response()->json([
                'status' => 'error',
                'message' => $closedMessage,
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($phone, '234')) {
            $phone = '0' . substr($phone, 3);
        }

        $referrerId = null;
        if (!empty($validated['referral_code'])) {
            $referrer = User::where('referral_code', strtoupper(trim($validated['referral_code'])))->first();
            if ($referrer) {
                $referrerId = $referrer->id;
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone' => $phone,
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => 'active',
            'tier' => 'standard',
            'referred_by_id' => $referrerId,
            'email_verified_at' => now(), // Instant activation for API/mobile
        ]);

        $wallet = $user->getOrCreateWallet();
        $user->getOrCreateReferralCode();
        $user->getOrCreateVirtualAccounts();
        $token = $user->createToken('mobile_app_auth')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful.',
            'data' => [
                'user' => $user,
                'wallet' => $wallet,
                'token' => $token,
                'has_pin' => false,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The provided credentials do not match our records.',
            ], 401);
        }

        if (!$user->isActive()) {
            return response()->json([
                'status' => 'error',
                'message' => "Your account is {$user->status}. Please contact support.",
            ], 403);
        }

        $token = $user->createToken('mobile_app_auth')->plainTextToken;
        $wallet = $user->getOrCreateWallet();
        $user->getOrCreateReferralCode();
        $user->getOrCreateVirtualAccounts();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'data' => [
                'user' => $user,
                'wallet' => $wallet,
                'token' => $token,
                'has_pin' => $user->hasTransactionPin(),
            ],
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if ($user) {
            $this->otpService->generateAndSend($user->email, 'password_reset', $user);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'If an account exists with this email, a 6-digit password reset code has been sent.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User account not found.',
            ], 404);
        }

        if (!$this->otpService->verify($user->email, 'password_reset', $request->code)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired 6-digit verification code.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Your password has been reset successfully. You can now log in.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'wallet' => $wallet,
                'has_pin' => $user->hasTransactionPin(),
            ],
        ]);
    }
}
