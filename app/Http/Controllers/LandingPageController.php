<?php

namespace App\Http\Controllers;

use App\Models\CablePlan;
use App\Models\DataPlan;
use App\Models\ElectricityProvider;
use App\Models\ExamPackage;
use App\Models\Faq;
use App\Models\NetworkSetting;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    public function index()
    {
        // 1. Dynamic Platform & Branding Settings
        $platformName = SystemSetting::get('platform_name', 'BJ Data Sub');
        $platformTagline = SystemSetting::get('platform_tagline', 'INSTANT VTU & BILL PAYMENTS');
        $primaryColor = SystemSetting::get('primary_color', '#800020');
        $secondaryColor = SystemSetting::get('secondary_color', '#4A0714');
        $accentColor = SystemSetting::get('accent_color', '#9B1B30');
        $logoImage = SystemSetting::get('logo_image', null);
        $favicon = SystemSetting::get('favicon', null);
        $logoDisplayMode = SystemSetting::get('logo_display_mode', 'image_and_text');

        // 2. Dynamic Contact & Support Info
        $supportEmail = SystemSetting::get('support_email', 'support@vtuexpress.ng');
        $contactEmail = SystemSetting::get('contact_email', $supportEmail);
        $supportPhone = SystemSetting::get('support_phone', '+234 800 123 4567');
        $supportWhatsapp = SystemSetting::get('support_whatsapp', '+234 812 345 6789');
        $businessAddress = SystemSetting::get('business_address', 'Lagos, Nigeria');
        $supportHours = SystemSetting::get('support_hours', '24/7 Mon - Sun');

        // 3. Dynamic Social Media Links
        $socialLinks = array_filter([
            'whatsapp' => !empty($supportWhatsapp) ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $supportWhatsapp) : null,
            'facebook' => SystemSetting::get('social_facebook', ''),
            'twitter' => SystemSetting::get('social_twitter', ''),
            'instagram' => SystemSetting::get('social_instagram', ''),
            'tiktok' => SystemSetting::get('social_tiktok', ''),
            'telegram' => SystemSetting::get('social_telegram', ''),
        ]);

        // 4. Dynamic Mobile App Download Links
        $appInfo = [
            'title' => SystemSetting::get('app_download_title', "Take {$platformName} Everywhere You Go"),
            'description' => SystemSetting::get('app_download_description', 'Manage your wallet, recharge airtime & data, pay utility bills, and track transactions seamlessly on Android and iOS.'),
            'android_apk_url' => SystemSetting::get('android_download_url', asset('apps/bjdatasub.apk')),
            'google_play_url' => SystemSetting::get('google_play_url', ''),
            'app_store_url' => SystemSetting::get('ios_app_store_url', ''),
        ];

        // 5. Dynamic Hero Section Copy
        $hero = [
            'badge' => SystemSetting::get('hero_badge', 'Automated 24/7 Telecom Infrastructure'),
            'title' => SystemSetting::get('hero_title', 'Instant Airtime, Cheap Data & Everyday Bills in Seconds'),
            'subtitle' => SystemSetting::get('hero_subtitle', 'Experience blazing-fast automated VTU delivery. Buy mobile data bundles, recharge airtime, generate electricity prepaid tokens, and renew cable TV subscriptions with instant 24/7 auto-funding.'),
            'cta_text' => SystemSetting::get('hero_cta_text', 'Create Free Account'),
            'cta_url' => SystemSetting::get('hero_cta_url', route('register')),
            'secondary_text' => SystemSetting::get('hero_secondary_text', 'Download Mobile App'),
            'secondary_url' => SystemSetting::get('hero_secondary_url', '#download-app'),
        ];

        // 6. Dynamic About Section Copy
        $about = [
            'title' => SystemSetting::get('about_title', "Why Choose {$platformName}?"),
            'description' => SystemSetting::get('about_description', "We are a trusted digital telecom distribution platform built with direct API gateways to major Nigerian telecom networks and utility providers. Every transaction is processed automatically within 5 seconds with bank-grade security."),
        ];

        // 7. Dynamic SEO Metadata
        $seo = [
            'meta_title' => SystemSetting::get('meta_title', "{$platformName} - Instant Airtime, Cheap Data & Utility Bill Payments in Nigeria"),
            'meta_description' => SystemSetting::get('meta_description', "Buy cheap SME data bundles, airtime discounts, electricity tokens & cable TV subscriptions in Nigeria with automated instant delivery."),
            'meta_keywords' => SystemSetting::get('meta_keywords', 'vtu, data bundles, cheap data nigeria, mtn sme data, airtime topup, electricity bills, payrant virtual account, bj data sub'),
        ];

        // 8. Dynamic Service Statuses from Database
        $networks = NetworkSetting::all()->keyBy('network');
        $services = [
            'airtime' => [
                'enabled' => $networks->where('status', 'active')->count() > 0,
                'name' => 'Airtime Top-up',
                'description' => 'Instant recharge for MTN, Airtel, Glo, and 9mobile with up to 4.5% cashback.',
                'icon' => 'fa-solid fa-phone-volume',
            ],
            'data' => [
                'enabled' => DataPlan::where('status', 'active')->count() > 0,
                'name' => 'Cheap Data Bundles',
                'description' => 'SME, Gifting, and Corporate data plans delivered in 5 seconds with 30-day validity.',
                'icon' => 'fa-solid fa-wifi',
            ],
            'electricity' => [
                'enabled' => ElectricityProvider::where('status', 'active')->count() > 0,
                'name' => 'Electricity Bills',
                'description' => 'Generate prepaid meter tokens & pay postpaid bills for IKEDC, EKEDC, AEDC, IBEDC & more.',
                'icon' => 'fa-solid fa-bolt',
            ],
            'cable' => [
                'enabled' => CablePlan::where('status', 'active')->count() > 0,
                'name' => 'Cable TV Subscriptions',
                'description' => 'Instant package renewal for DSTV, GOTV, and Startimes without viewing interruption.',
                'icon' => 'fa-solid fa-tv',
            ],
            'exam' => [
                'enabled' => ExamPackage::where('status', 'active')->count() > 0,
                'name' => 'Exam Result PINs',
                'description' => 'Instant purchase of WAEC, NECO, and NABTEB result checker PINs & tokens.',
                'icon' => 'fa-solid fa-graduation-cap',
            ],
            'airtime_cash' => [
                'enabled' => SystemSetting::get('airtime_cash_status', 'active') === 'active',
                'name' => 'Airtime to Cash',
                'description' => 'Convert excess airtime from any network back into cash deposited straight into your bank.',
                'icon' => 'fa-solid fa-arrow-right-arrow-left',
            ],
        ];

        // 9. Dynamic FAQs from Database
        $faqs = Faq::active()->get();

        // 10. Pricing & Wholesale Tiers
        $tiers = [
            'reseller_discount' => SystemSetting::get('reseller_airtime_discount', '3.50'),
            'vip_discount' => SystemSetting::get('vip_airtime_discount', '4.50'),
            'reseller_fee' => SystemSetting::get('reseller_upgrade_fee', '1500.00'),
            'vip_fee' => SystemSetting::get('vip_upgrade_fee', '3500.00'),
            'referral_bonus' => SystemSetting::get('referral_flat_bonus', '100.00'),
        ];

        // 11. Data Plans for Live Pricing Showcase
        $popularDataPlans = DataPlan::where('status', 'active')
            ->orderBy('selling_price', 'asc')
            ->get()
            ->groupBy('network');

        return view('landing.index', compact(
            'platformName',
            'platformTagline',
            'primaryColor',
            'secondaryColor',
            'accentColor',
            'logoImage',
            'favicon',
            'logoDisplayMode',
            'supportEmail',
            'contactEmail',
            'supportPhone',
            'supportWhatsapp',
            'businessAddress',
            'supportHours',
            'socialLinks',
            'appInfo',
            'hero',
            'about',
            'seo',
            'services',
            'networks',
            'faqs',
            'tiers',
            'popularDataPlans'
        ));
    }

    public function privacy()
    {
        $platformName = SystemSetting::get('platform_name', 'BJ Data Sub');
        $supportEmail = SystemSetting::get('support_email', 'support@vtuexpress.ng');
        return view('landing.privacy', compact('platformName', 'supportEmail'));
    }

    public function terms()
    {
        $platformName = SystemSetting::get('platform_name', 'BJ Data Sub');
        $supportEmail = SystemSetting::get('support_email', 'support@vtuexpress.ng');
        return view('landing.terms', compact('platformName', 'supportEmail'));
    }
}
