<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $supportEmail = SystemSetting::get('support_email', 'support@vtuexpress.ng');
        $supportPhone = SystemSetting::get('support_phone', '+234 800 123 4567');
        $supportWhatsapp = SystemSetting::get('support_whatsapp', '+234 812 345 6789');

        return view('support.index', compact('user', 'supportEmail', 'supportPhone', 'supportWhatsapp'));
    }
}
