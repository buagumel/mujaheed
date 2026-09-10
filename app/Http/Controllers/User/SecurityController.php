<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SecurityController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function index()
    {
        $user = Auth::user();
        return view('profile.security', compact('user'));
    }

    public function verifyPinAjax(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'digits:4'],
        ]);

        $user = Auth::user();

        if ($user->isPinLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction PIN is locked due to too many failed attempts. Try again later.',
            ], 200);
        }

        if (!$user->verifyPin($request->pin)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid transaction PIN. Please enter your correct 4-digit PIN.',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'PIN verified successfully.',
        ]);
    }

    /**
     * Send OTP for Password or PIN operations
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'purpose' => ['required', 'in:password_change,pin_reset,pin_change'],
        ]);

        $user = Auth::user();
        $this->otpService->generateAndSend($user->email, $request->purpose, $user);

        return response()->json([
            'success' => true,
            'message' => 'A 6-digit security OTP code has been sent to your registered email (' . substr($user->email, 0, 3) . '***' . substr($user->email, strpos($user->email, '@')) . ').',
        ]);
    }

    public function setPin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'digits:4', 'confirmed'],
        ], [
            'pin.digits' => 'Transaction PIN must be exactly 4 digits.',
            'pin.confirmed' => 'Transaction PIN confirmation does not match.',
        ]);

        $user = Auth::user();
        $user->setTransactionPin($request->pin);

        AuditLog::record('pin_set', 'User set initial transaction PIN.', $user->id);

        return back()->with('success', 'Transaction PIN has been set successfully!');
    }

    public function changePin(Request $request)
    {
        $request->validate([
            'current_pin' => ['required', 'digits:4'],
            'pin' => ['required', 'digits:4', 'confirmed', 'different:current_pin'],
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'pin.different' => 'New PIN must be different from your current PIN.',
            'otp.size' => 'Verification OTP must be 6 digits.',
        ]);

        $user = Auth::user();

        if (!$this->otpService->verify($user->email, 'pin_change', $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired 6-digit email OTP. Please request a new OTP code.']);
        }

        if (!$user->verifyPin($request->current_pin)) {
            return back()->withErrors(['current_pin' => 'Current transaction PIN is incorrect.']);
        }

        $user->setTransactionPin($request->pin);

        AuditLog::record('pin_change', 'User changed transaction PIN with OTP verification.', $user->id);

        return back()->with('success', 'Transaction PIN changed successfully with multi-layer verification!');
    }

    public function resetPinWithOtp(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'digits:4', 'confirmed'],
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'pin.digits' => 'New PIN must be 4 digits.',
            'otp.size' => 'Security OTP must be 6 digits.',
        ]);

        $user = Auth::user();

        if (!$this->otpService->verify($user->email, 'pin_reset', $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired 6-digit email OTP. Please request a new OTP.']);
        }

        $user->setTransactionPin($request->pin);

        AuditLog::record('pin_reset', 'User reset forgotten transaction PIN via Email OTP.', $user->id);

        return back()->with('success', 'Transaction PIN has been reset successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.size' => 'Security OTP must be 6 digits.',
        ]);

        $user = Auth::user();

        if (!$this->otpService->verify($user->email, 'password_change', $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired 6-digit email OTP. Please click "Get Security OTP" to request a new code.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::record('password_change', 'User changed account password with 2-layer OTP confirmation.', $user->id);

        return back()->with('success', 'Password updated successfully with security OTP verification!');
    }
}
