<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                return back()->withErrors(['email' => "Your account is {$user->status}. Please contact support."]);
            }

            // Check if email is verified
            if (!$user->email_verified_at) {
                $this->otpService->generateAndSend($user->email, 'email_verification', $user);
                return redirect()->route('verification.notice')->with('warning', 'Please verify your email address to continue.');
            }

            $request->session()->regenerate();

            AuditLog::record('login', "User {$user->email} logged in successfully.", $user->id);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $allowRegistration = SystemSetting::get('allow_registration', 'open') === 'open';
        $closedMessage = SystemSetting::get('registration_closed_message', 'New user registrations are currently closed. Please check back later or contact customer support.');
        $ref = $request->query('ref');

        return view('auth.register', compact('ref', 'allowRegistration', 'closedMessage'));
    }

    public function register(Request $request)
    {
        if (SystemSetting::get('allow_registration', 'open') === 'closed') {
            $closedMessage = SystemSetting::get('registration_closed_message', 'New user registrations are currently closed. Please check back later or contact customer support.');
            return back()->withErrors(['email' => $closedMessage])->withInput();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
            'terms' => ['accepted'],
        ], [
            'phone.regex' => 'Please provide a valid Nigerian phone number (e.g. 08012345678).',
            'referral_code.exists' => 'The entered referral code is invalid.',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($phone, '234')) {
            $phone = '0' . substr($phone, 3);
        }

        // Check referrer
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
            'email_verified_at' => null, // Requires verification
        ]);

        // Auto-create wallet, referral code, and dedicated virtual bank accounts
        $user->getOrCreateWallet();
        $user->getOrCreateReferralCode();
        $user->getOrCreateVirtualAccounts();

        AuditLog::record('register', "User {$user->email} registered an account. Verification OTP sent.", $user->id);

        Auth::login($user);

        // Generate and dispatch verification OTP
        $this->otpService->generateAndSend($user->email, 'email_verification', $user);

        return redirect()->route('verification.notice')->with('success', 'Account created! A 6-digit verification code has been sent to your email.');
    }

    public function showVerifyEmail()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->email_verified_at) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify_email');
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if ($this->otpService->verify($user->email, 'email_verification', $request->code)) {
            $user->update(['email_verified_at' => now()]);
            AuditLog::record('email_verified', "User {$user->email} verified email address successfully.", $user->id);

            return redirect()->route('dashboard')->with('success', 'Email verified successfully! Welcome to your dashboard.');
        }

        return back()->withErrors(['code' => 'Invalid or expired 6-digit verification code. Please check your email or request a new code.']);
    }

    public function resendVerificationOtp(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->otpService->generateAndSend($user->email, 'email_verification', $user);

        return back()->with('success', 'A fresh 6-digit verification code has been sent to your email.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if ($user) {
            $otp = $this->otpService->generateAndSend($user->email, 'password_reset', $user);
            session(['password_reset_email' => $user->email]);
        } else {
            session(['password_reset_email' => strtolower($request->email)]);
        }

        $smtpConfigured = !empty(SystemSetting::get('mail_username')) && !empty(SystemSetting::get('mail_password'));

        if (!$smtpConfigured) {
            return redirect()->route('password.reset.form')->with('warning', 'OTP generated! (Note: SMTP email server is not configured in Admin Settings yet. Please configure SMTP in Admin -> Settings -> SMTP to receive emails).');
        }

        return redirect()->route('password.reset.form')->with('success', 'A 6-digit verification OTP code has been sent to your email.');
    }

    public function showResetPassword()
    {
        $email = session('password_reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.reset_password', compact('email'));
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User account not found.']);
        }

        if (!$this->otpService->verify($user->email, 'password_reset', $request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired 6-digit verification code.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        session()->forget('password_reset_email');

        AuditLog::record('password_reset', "User {$user->email} reset password using Email OTP.", $user->id);

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! You can now log in.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::record('logout', "User " . Auth::user()->email . " logged out.", Auth::id());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
