<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;

class ApiAppConfigController extends Controller
{
    public function config(): JsonResponse
    {
        $logoImage = SystemSetting::get('logo_image');
        $logoUrl = $logoImage ? asset('storage/' . $logoImage) : null;

        return response()->json([
            'status' => 'success',
            'data' => [
                'platform_name' => SystemSetting::get('platform_name', 'VTU Express Nigeria'),
                'platform_tagline' => SystemSetting::get('platform_tagline', 'INSTANT RECHARGE'),
                'primary_color' => SystemSetting::get('primary_color', '#1877F2'),
                'secondary_color' => SystemSetting::get('secondary_color', '#8E33FF'),
                'logo_url' => $logoUrl,
                'support_email' => SystemSetting::get('support_email', 'support@vtuexpress.ng'),
                'support_phone' => SystemSetting::get('support_phone', '+234 800 123 4567'),
                'support_whatsapp' => SystemSetting::get('support_whatsapp', '+234 812 345 6789'),
                'currency' => SystemSetting::get('currency', 'NGN'),
                'currency_symbol' => SystemSetting::get('currency_symbol', '₦'),
                'maintenance_mode' => SystemSetting::get('maintenance_mode', 'off') === 'on',
                'services' => [
                    'airtime' => true,
                    'data' => true,
                    'electricity' => true,
                    'cable' => true,
                    'exam_pins' => true,
                    'airtime_cash' => SystemSetting::get('airtime_cash_status', 'active') === 'active',
                    'referrals' => SystemSetting::get('referral_status', 'active') === 'active',
                    'dva' => SystemSetting::get('dva_status', 'active') === 'active',
                ],
            ],
        ]);
    }
}
