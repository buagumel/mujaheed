<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ElectricityProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminElectricityController extends Controller
{
    public function index()
    {
        $providers = ElectricityProvider::all();
        return view('admin.electricity.index', compact('providers'));
    }

    public function update(Request $request, ElectricityProvider $provider)
    {
        $validated = $request->validate([
            'convenience_fee' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['required', 'numeric', 'min:100'],
            'max_amount' => ['required', 'numeric', 'min:1000'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $provider->update($validated);

        AuditLog::record(
            'electricity_provider_updated',
            "Admin updated electricity provider: {$provider->name}.",
            Auth::id(),
            $validated
        );

        return back()->with('success', "{$provider->name} settings updated successfully.");
    }
}
