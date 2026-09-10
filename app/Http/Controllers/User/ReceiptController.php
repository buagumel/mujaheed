<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\VtuTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function show(Request $request, string $reference)
    {
        $transaction = VtuTransaction::with('user')
            ->where('reference', $reference)
            ->firstOrFail();

        // If user is authenticated, ensure they own the transaction or are admin
        if (Auth::check()) {
            if (Auth::user()->role !== 'admin' && Auth::id() !== $transaction->user_id) {
                abort(403, 'Unauthorized access to this receipt.');
            }
        }

        $platformName = SystemSetting::get('platform_name', 'BJ Data Sub');
        $supportPhone = SystemSetting::get('support_phone', '+234 800 123 4567');
        $supportEmail = SystemSetting::get('support_email', 'support@bjdatasub.ng');
        $logo = SystemSetting::get('logo', null);

        return view('user.transactions.receipt', compact(
            'transaction',
            'platformName',
            'supportPhone',
            'supportEmail',
            'logo'
        ));
    }
}
