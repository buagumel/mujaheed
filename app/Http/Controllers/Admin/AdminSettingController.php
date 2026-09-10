<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Services\MailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'platform_name' => SystemSetting::get('platform_name', 'VTU Express Nigeria'),
            'platform_tagline' => SystemSetting::get('platform_tagline', 'INSTANT RECHARGE'),
            'primary_color' => SystemSetting::get('primary_color', '#1877F2'),
            'secondary_color' => SystemSetting::get('secondary_color', '#8E33FF'),
            'logo_display_mode' => SystemSetting::get('logo_display_mode', 'image_and_text'),
            'logo_image' => SystemSetting::get('logo_image', null),
            'favicon' => SystemSetting::get('favicon', null),
            'logo_height' => SystemSetting::get('logo_height', '38'),
            'support_email' => SystemSetting::get('support_email', 'support@vtuexpress.ng'),
            'support_phone' => SystemSetting::get('support_phone', '+234 800 123 4567'),
            'support_whatsapp' => SystemSetting::get('support_whatsapp', '+234 812 345 6789'),
            'currency' => SystemSetting::get('currency', 'NGN'),
            'currency_symbol' => SystemSetting::get('currency_symbol', '₦'),
            'maintenance_mode' => SystemSetting::get('maintenance_mode', 'off'),
            'allow_registration' => SystemSetting::get('allow_registration', 'open'),
            'registration_closed_message' => SystemSetting::get('registration_closed_message', 'New user registrations are currently closed. Please check back later or contact customer support.'),

            // SMTP & Email Settings
            'mail_mailer' => SystemSetting::get('mail_mailer', 'smtp'),
            'mail_host' => SystemSetting::get('mail_host', 'smtp.gmail.com'),
            'mail_port' => SystemSetting::get('mail_port', '587'),
            'mail_username' => SystemSetting::get('mail_username', ''),
            'mail_password' => SystemSetting::get('mail_password', ''),
            'mail_encryption' => SystemSetting::get('mail_encryption', 'tls'),
            'mail_from_address' => SystemSetting::get('mail_from_address', 'noreply@vtuexpress.ng'),
            'mail_from_name' => SystemSetting::get('mail_from_name', 'VTU Express Nigeria'),

            // Payrant Payment Gateway & Dedicated Virtual Accounts
            'payrant_status' => SystemSetting::get('payrant_status', 'active'),
            'payrant_environment' => SystemSetting::get('payrant_environment', 'live'),
            'payrant_live_base_url' => SystemSetting::get('payrant_live_base_url', 'https://api.payrant.com/v1'),
            'payrant_test_base_url' => SystemSetting::get('payrant_test_base_url', 'https://api-test.payrant.com/v1'),
            'payrant_live_public_key' => SystemSetting::get('payrant_live_public_key', ''),
            'payrant_live_secret_key' => SystemSetting::get('payrant_live_secret_key', ''),
            'payrant_test_public_key' => SystemSetting::get('payrant_test_public_key', ''),
            'payrant_test_secret_key' => SystemSetting::get('payrant_test_secret_key', ''),
            'payrant_webhook_secret' => SystemSetting::get('payrant_webhook_secret', 'payrant_webhook_secret_key'),
            'payrant_bank_display_name' => SystemSetting::get('payrant_bank_display_name', 'PalmPay / Payrant Bank'),
            'payrant_fee_type' => SystemSetting::get('payrant_fee_type', 'flat'),
            'payrant_funding_fee' => SystemSetting::get('payrant_funding_fee', '0.00'),
            'payrant_default_bvn' => SystemSetting::get('payrant_default_bvn', '22596036771'),

            // Dedicated Virtual Bank Accounts Settings
            'dva_status' => SystemSetting::get('dva_status', 'active'),
            'dva_bank_1_name' => SystemSetting::get('dva_bank_1_name', 'PalmPay / Payrant'),
            'dva_bank_2_name' => SystemSetting::get('dva_bank_2_name', 'Wema Bank'),
            'dva_bank_3_name' => SystemSetting::get('dva_bank_3_name', 'Moniepoint MFB'),
            'dva_fee_percent' => SystemSetting::get('dva_fee_percent', '0.00'),

            // Membership Tiers & Wholesale Upgrades
            'reseller_upgrade_fee' => SystemSetting::get('reseller_upgrade_fee', '1500.00'),
            'vip_upgrade_fee' => SystemSetting::get('vip_upgrade_fee', '3500.00'),
            'reseller_airtime_discount' => SystemSetting::get('reseller_airtime_discount', '3.50'),
            'vip_airtime_discount' => SystemSetting::get('vip_airtime_discount', '4.50'),

            // Referral Program Settings
            'referral_status' => SystemSetting::get('referral_status', 'active'),
            'referral_reward_type' => SystemSetting::get('referral_reward_type', 'flat'),
            'referral_flat_bonus' => SystemSetting::get('referral_flat_bonus', '100.00'),
            'referral_percent_bonus' => SystemSetting::get('referral_percent_bonus', '2.00'),
            'referral_min_withdrawal' => SystemSetting::get('referral_min_withdrawal', '100.00'),

            // Airtime to Cash Settings
            'airtime_cash_status' => SystemSetting::get('airtime_cash_status', 'active'),
            'airtime_cash_rate_mtn' => SystemSetting::get('airtime_cash_rate_mtn', '80.00'),
            'airtime_cash_rate_airtel' => SystemSetting::get('airtime_cash_rate_airtel', '75.00'),
            'airtime_cash_rate_glo' => SystemSetting::get('airtime_cash_rate_glo', '70.00'),
            'airtime_cash_rate_9mobile' => SystemSetting::get('airtime_cash_rate_9mobile', '70.00'),
            'airtime_cash_receiver_mtn' => SystemSetting::get('airtime_cash_receiver_mtn', '08031234567'),
            'airtime_cash_receiver_airtel' => SystemSetting::get('airtime_cash_receiver_airtel', '08021234567'),
            'airtime_cash_receiver_glo' => SystemSetting::get('airtime_cash_receiver_glo', '08051234567'),
            'airtime_cash_receiver_9mobile' => SystemSetting::get('airtime_cash_receiver_9mobile', '08091234567'),

            // Contact & Landing Page Settings
            'contact_email' => SystemSetting::get('contact_email', 'support@vtuexpress.ng'),
            'business_address' => SystemSetting::get('business_address', 'Lagos, Nigeria'),
            'support_hours' => SystemSetting::get('support_hours', '24/7 Mon - Sun'),
            'social_facebook' => SystemSetting::get('social_facebook', ''),
            'social_twitter' => SystemSetting::get('social_twitter', ''),
            'social_instagram' => SystemSetting::get('social_instagram', ''),
            'social_tiktok' => SystemSetting::get('social_tiktok', ''),
            'social_telegram' => SystemSetting::get('social_telegram', ''),
            'app_download_title' => SystemSetting::get('app_download_title', 'Take BJ Data Sub Everywhere You Go'),
            'app_download_description' => SystemSetting::get('app_download_description', 'Manage your wallet, recharge airtime & data, pay utility bills, and track transactions seamlessly on Android and iOS.'),
            'android_download_url' => SystemSetting::get('android_download_url', asset('apps/bjdatasub.apk')),
            'google_play_url' => SystemSetting::get('google_play_url', ''),
            'ios_app_store_url' => SystemSetting::get('ios_app_store_url', ''),
            'hero_badge' => SystemSetting::get('hero_badge', 'Automated 24/7 Telecom Infrastructure'),
            'hero_title' => SystemSetting::get('hero_title', 'Instant Airtime, Cheap Data & Everyday Bills in Seconds'),
            'hero_subtitle' => SystemSetting::get('hero_subtitle', 'Experience blazing-fast automated VTU delivery. Buy mobile data bundles, recharge airtime, generate electricity prepaid tokens, and renew cable TV subscriptions with instant 24/7 auto-funding.'),
            'hero_cta_text' => SystemSetting::get('hero_cta_text', 'Create Free Account'),
            'hero_cta_url' => SystemSetting::get('hero_cta_url', route('register')),
            'hero_secondary_text' => SystemSetting::get('hero_secondary_text', 'Download Mobile App'),
            'hero_secondary_url' => SystemSetting::get('hero_secondary_url', '#download-app'),
            'about_title' => SystemSetting::get('about_title', 'Why Choose BJ Data Sub?'),
            'about_description' => SystemSetting::get('about_description', 'We are a trusted digital telecom distribution platform built with direct API gateways to major Nigerian telecom networks and utility providers.'),
            'meta_title' => SystemSetting::get('meta_title', 'BJ Data Sub - Instant Airtime, Cheap Data & Utility Bill Payments in Nigeria'),
            'meta_description' => SystemSetting::get('meta_description', 'Buy cheap SME data bundles, airtime discounts, electricity tokens & cable TV subscriptions in Nigeria with automated instant delivery.'),
            'meta_keywords' => SystemSetting::get('meta_keywords', 'vtu, data bundles, cheap data nigeria, mtn sme data, airtime topup, electricity bills, payrant virtual account, bj data sub'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => ['required', 'string', 'max:100'],
            'platform_tagline' => ['nullable', 'string', 'max:100'],
            'primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'logo_display_mode' => ['required', 'in:image_only,text_only,image_and_text'],
            'logo_height' => ['required', 'numeric', 'min:20', 'max:100'],
            'logo_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:3072'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,svg,webp', 'max:1024'],
            'support_email' => ['required', 'email'],
            'support_phone' => ['required', 'string'],
            'support_whatsapp' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'max:5'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'maintenance_mode' => ['sometimes', 'required', 'in:on,off'],
            'allow_registration' => ['sometimes', 'required', 'in:open,closed'],
            'registration_closed_message' => ['nullable', 'string', 'max:500'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],

            // Email & SMTP Fields
            'mail_mailer' => ['required', 'string'],
            'mail_host' => ['required', 'string'],
            'mail_port' => ['required', 'numeric'],
            'mail_username' => ['nullable', 'string'],
            'mail_password' => ['nullable', 'string'],
            'mail_encryption' => ['required', 'in:tls,ssl,none'],
            'mail_from_address' => ['required', 'email'],
            'mail_from_name' => ['required', 'string'],

            // Payrant Gateway Settings
            'payrant_status' => ['sometimes', 'required', 'in:active,inactive'],
            'payrant_environment' => ['sometimes', 'required', 'in:live,test'],
            'payrant_live_base_url' => ['sometimes', 'required', 'url'],
            'payrant_test_base_url' => ['sometimes', 'required', 'url'],
            'payrant_live_public_key' => ['nullable', 'string'],
            'payrant_live_secret_key' => ['nullable', 'string'],
            'payrant_test_public_key' => ['nullable', 'string'],
            'payrant_test_secret_key' => ['nullable', 'string'],
            'payrant_webhook_secret' => ['sometimes', 'required', 'string'],
            'payrant_bank_display_name' => ['sometimes', 'required', 'string'],
            'payrant_fee_type' => ['sometimes', 'required', 'in:flat,percent'],
            'payrant_funding_fee' => ['sometimes', 'required', 'numeric', 'min:0'],

            // Dedicated Virtual Accounts
            'dva_status' => ['sometimes', 'required', 'in:active,inactive'],
            'dva_bank_1_name' => ['sometimes', 'required', 'string'],
            'dva_bank_2_name' => ['sometimes', 'required', 'string'],
            'dva_bank_3_name' => ['sometimes', 'required', 'string'],
            'dva_fee_percent' => ['sometimes', 'required', 'numeric', 'min:0'],

            // Tiers
            'reseller_upgrade_fee' => ['sometimes', 'required', 'numeric', 'min:0'],
            'vip_upgrade_fee' => ['sometimes', 'required', 'numeric', 'min:0'],
            'reseller_airtime_discount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'vip_airtime_discount' => ['sometimes', 'required', 'numeric', 'min:0'],

            // Referrals
            'referral_status' => ['sometimes', 'required', 'in:active,inactive'],
            'referral_reward_type' => ['sometimes', 'required', 'in:flat,percent'],
            'referral_flat_bonus' => ['sometimes', 'required', 'numeric', 'min:0'],
            'referral_percent_bonus' => ['sometimes', 'required', 'numeric', 'min:0'],
            'referral_min_withdrawal' => ['sometimes', 'required', 'numeric', 'min:50'],

            // Airtime to Cash
            'airtime_cash_status' => ['sometimes', 'required', 'in:active,inactive'],
            'airtime_cash_rate_mtn' => ['sometimes', 'required', 'numeric', 'min:10', 'max:100'],
            'airtime_cash_rate_airtel' => ['sometimes', 'required', 'numeric', 'min:10', 'max:100'],
            'airtime_cash_rate_glo' => ['sometimes', 'required', 'numeric', 'min:10', 'max:100'],
            'airtime_cash_rate_9mobile' => ['sometimes', 'required', 'numeric', 'min:10', 'max:100'],
            'airtime_cash_receiver_mtn' => ['sometimes', 'required', 'string'],
            'airtime_cash_receiver_airtel' => ['sometimes', 'required', 'string'],
            'airtime_cash_receiver_glo' => ['sometimes', 'required', 'string'],
            'airtime_cash_receiver_9mobile' => ['sometimes', 'required', 'string'],

            // Contact & Landing Page Fields
            'contact_email' => ['nullable', 'email'],
            'business_address' => ['nullable', 'string', 'max:255'],
            'support_hours' => ['nullable', 'string', 'max:100'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'social_twitter' => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'social_tiktok' => ['nullable', 'string', 'max:255'],
            'social_telegram' => ['nullable', 'string', 'max:255'],
            'app_download_title' => ['nullable', 'string', 'max:150'],
            'app_download_description' => ['nullable', 'string', 'max:500'],
            'android_download_url' => ['nullable', 'string', 'max:255'],
            'google_play_url' => ['nullable', 'string', 'max:255'],
            'ios_app_store_url' => ['nullable', 'string', 'max:255'],
            'hero_badge' => ['nullable', 'string', 'max:100'],
            'hero_title' => ['nullable', 'string', 'max:150'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'hero_cta_text' => ['nullable', 'string', 'max:50'],
            'hero_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_secondary_text' => ['nullable', 'string', 'max:50'],
            'hero_secondary_url' => ['nullable', 'string', 'max:255'],
            'about_title' => ['nullable', 'string', 'max:150'],
            'about_description' => ['nullable', 'string', 'max:1000'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
        ]);

        // Clean & sanitize App Password (remove any spaces automatically)
        if (!empty($validated['mail_password'])) {
            $validated['mail_password'] = str_replace(' ', '', $validated['mail_password']);
        }

        // Auto-correct if Gmail is selected but localhost is kept
        if (str_ends_with(strtolower($validated['mail_username'] ?? ''), '@gmail.com')) {
            if ($validated['mail_host'] === '127.0.0.1' || $validated['mail_host'] === 'localhost') {
                $validated['mail_host'] = 'smtp.gmail.com';
                $validated['mail_port'] = ($validated['mail_encryption'] === 'ssl') ? 465 : 587;
            }
        }

        // Handle Logo Removal
        if ($request->boolean('remove_logo')) {
            $oldLogo = SystemSetting::get('logo_image');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            SystemSetting::set('logo_image', null, 'branding');
        }

        // Handle Favicon Removal
        if ($request->boolean('remove_favicon')) {
            $oldFavicon = SystemSetting::get('favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            SystemSetting::set('favicon', null, 'branding');
        }

        // Handle Logo Upload
        if ($request->hasFile('logo_image')) {
            $path = $request->file('logo_image')->store('branding', 'public');
            SystemSetting::set('logo_image', $path, 'branding');
        }

        // Handle Favicon Upload
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('branding', 'public');
            SystemSetting::set('favicon', $path, 'branding');
        }

        // Save all platform settings
        $keysToSave = [
            'platform_name',
            'platform_tagline',
            'primary_color',
            'secondary_color',
            'logo_display_mode',
            'logo_height',
            'support_email',
            'support_phone',
            'support_whatsapp',
            'currency',
            'currency_symbol',
            'maintenance_mode',
            'allow_registration',
            'registration_closed_message',
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
            'payrant_status',
            'payrant_environment',
            'payrant_live_base_url',
            'payrant_test_base_url',
            'payrant_live_public_key',
            'payrant_live_secret_key',
            'payrant_test_public_key',
            'payrant_test_secret_key',
            'payrant_webhook_secret',
            'payrant_bank_display_name',
            'payrant_fee_type',
            'payrant_funding_fee',
            'dva_status',
            'dva_bank_1_name',
            'dva_bank_2_name',
            'dva_bank_3_name',
            'dva_fee_percent',
            'reseller_upgrade_fee',
            'vip_upgrade_fee',
            'reseller_airtime_discount',
            'vip_airtime_discount',
            'referral_status',
            'referral_reward_type',
            'referral_flat_bonus',
            'referral_percent_bonus',
            'referral_min_withdrawal',
            'airtime_cash_status',
            'airtime_cash_rate_mtn',
            'airtime_cash_rate_airtel',
            'airtime_cash_rate_glo',
            'airtime_cash_rate_9mobile',
            'airtime_cash_receiver_mtn',
            'airtime_cash_receiver_airtel',
            'airtime_cash_receiver_glo',
            'airtime_cash_receiver_9mobile',
            'contact_email',
            'business_address',
            'support_hours',
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_tiktok',
            'social_telegram',
            'app_download_title',
            'app_download_description',
            'android_download_url',
            'google_play_url',
            'ios_app_store_url',
            'hero_badge',
            'hero_title',
            'hero_subtitle',
            'hero_cta_text',
            'hero_cta_url',
            'hero_secondary_text',
            'hero_secondary_url',
            'about_title',
            'about_description',
            'meta_title',
            'meta_description',
            'meta_keywords',
        ];

        foreach ($keysToSave as $key) {
            if (isset($validated[$key])) {
                SystemSetting::set($key, $validated[$key]);
            }
        }

        // Reapply mail config
        MailConfigService::applySettings();

        AuditLog::record(
            'settings_updated',
            'Admin updated platform branding, ecosystem features, and system configurations.',
            Auth::id(),
            $request->except(['logo_image', 'favicon', 'mail_password', '_token'])
        );

        return back()->with('success', 'All system settings and feature configurations saved successfully.');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $platformName = SystemSetting::get('platform_name', 'VTU Express');
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 24px; border: 1px solid #e0e0e0; border-radius: 12px; text-align: center;'>
            <h2 style='color: #22C55E; margin: 0;'>✓ SMTP Connected!</h2>
            <p style='color: #1C252E; font-size: 15px; margin-top: 12px;'>Your email and SMTP configuration for <strong>{$platformName}</strong> is working perfectly.</p>
            <p style='color: #637381; font-size: 12px;'>Timestamp: " . now()->toRfc850String() . "</p>
        </div>";

        $result = MailConfigService::sendWithDiagnostic($request->test_email, "[{$platformName}] Test Email Successful", $html);

        if ($result['success']) {
            return back()->with('success', "Test email sent successfully to {$request->test_email}! Check your inbox/spam folder.");
        }

        return back()->withErrors(['test_email' => 'Failed to send test email. Error: ' . $result['error']]);
    }
}
