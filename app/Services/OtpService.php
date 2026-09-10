<?php

namespace App\Services;

use App\Models\EmailOtp;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Generate and dispatch a 6-digit OTP code to the given email
     */
    public function generateAndSend(string $email, string $purpose, ?User $user = null): EmailOtp
    {
        // Expire any existing unused OTPs for this email and purpose
        EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $code = (string) random_int(100000, 999999);

        $otp = EmailOtp::create([
            'user_id' => $user?->id,
            'email' => $email,
            'code' => $code,
            'purpose' => $purpose,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $platformName = SystemSetting::get('platform_name', 'VTU Express');

        $purposeTitles = [
            'email_verification' => 'Verify Your Email Address',
            'password_reset' => 'Password Reset Code',
            'password_change' => 'Confirm Password Change',
            'pin_reset' => 'Transaction PIN Reset OTP',
            'pin_change' => 'Transaction PIN Authorization',
        ];

        $title = $purposeTitles[$purpose] ?? 'Security Verification Code';

        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 540px; margin: 0 auto; padding: 24px; border: 1px solid #e0e0e0; border-radius: 12px;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h2 style='color: #1877F2; margin: 0;'>{$platformName}</h2>
                <p style='color: #637381; font-size: 14px; margin-top: 4px;'>{$title}</p>
            </div>
            <p style='font-size: 15px; color: #1C252E;'>Hello,</p>
            <p style='font-size: 15px; color: #1C252E;'>Use the 6-digit verification code below to complete your <strong>{$title}</strong> request.</p>
            <div style='text-align: center; margin: 28px 0;'>
                <span style='display: inline-block; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #1877F2; background: #F4F6F8; padding: 14px 28px; border-radius: 8px; border: 1px dashed #1877F2;'>{$code}</span>
            </div>
            <p style='font-size: 13px; color: #637381;'>This verification code will expire in <strong>10 minutes</strong>. If you did not initiate this request, please change your password immediately or contact our support team.</p>
            <hr style='border: none; border-top: 1px solid #eee; margin: 24px 0;' />
            <p style='font-size: 11px; color: #919EAB; text-align: center;'>© " . date('Y') . " {$platformName}. All rights reserved.</p>
        </div>";

        MailConfigService::sendRaw($email, "[{$platformName}] {$title} - {$code}", $html);

        return $otp;
    }

    /**
     * Verify the supplied OTP code
     */
    public function verify(string $email, string $purpose, string $code): bool
    {
        $otp = EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return false;
        }

        $otp->increment('attempts');

        if (!$otp->isValid($code)) {
            return false;
        }

        $otp->update(['used_at' => now()]);
        return true;
    }
}
