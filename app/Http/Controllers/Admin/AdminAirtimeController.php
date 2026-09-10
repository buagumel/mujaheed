<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\NetworkSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAirtimeController extends Controller
{
    public function index()
    {
        $networks = NetworkSetting::all();
        return view('admin.airtime.index', compact('networks'));
    }

    public function update(Request $request, NetworkSetting $network)
    {
        $validated = $request->validate([
            'airtime_discount_percent' => ['required', 'numeric', 'min:0', 'max:50'],
            'airtime_min_amount' => ['required', 'numeric', 'min:10'],
            'airtime_max_amount' => ['required', 'numeric', 'min:100'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $network->update($validated);

        AuditLog::record(
            'airtime_pricing_updated',
            "Admin updated airtime settings for {$network->network}.",
            Auth::id(),
            $validated
        );

        return back()->with('success', "{$network->network} airtime settings updated successfully.");
    }
}
