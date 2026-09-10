<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class AdminProviderController extends Controller
{
    public function index()
    {
        $activeProvider = SystemSetting::get('active_vtu_provider', 'bilalsada');

        $providers = [
            'bilalsada' => [
                'name' => 'BilalsadaSub',
                'status' => SystemSetting::get('provider_bilalsada_status', 'enabled'),
                'base_url' => SystemSetting::get('bilalsada_base_url', 'https://bilalsadasub.com'),
                'api_key' => SystemSetting::get('bilalsada_api_key', '6b43b98e82ce4ec13e93cc2d761ee9b561a2343004d65c432b36d7434f24'),
            ],
            'alrahuz' => [
                'name' => 'AlrahuzData',
                'status' => SystemSetting::get('provider_alrahuz_status', 'enabled'),
                'base_url' => SystemSetting::get('alrahuz_base_url', 'https://alrahuzdata.com.ng'),
                'api_key' => SystemSetting::get('alrahuz_api_key', 'c9d7930f731c093e2ee81369d2aea34995a7b251'),
            ],
            'n3tdata' => [
                'name' => 'N3TData',
                'status' => SystemSetting::get('provider_n3tdata_status', 'enabled'),
                'base_url' => SystemSetting::get('n3tdata_base_url', 'https://n3tdata.com'),
                'api_key' => SystemSetting::get('n3tdata_api_key', ''),
            ],
            'superjara' => [
                'name' => 'Superjara',
                'status' => SystemSetting::get('provider_superjara_status', 'enabled'),
                'base_url' => SystemSetting::get('superjara_base_url', 'https://superjara.com'),
                'api_key' => SystemSetting::get('superjara_api_key', ''),
            ],
            'mock' => [
                'name' => 'Mock / Test Provider',
                'status' => SystemSetting::get('provider_mock_status', 'disabled'),
                'base_url' => 'Local Simulator',
                'api_key' => 'N/A',
            ]
        ];

        return view('admin.providers.index', compact('activeProvider', 'providers'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'active_vtu_provider' => 'required|string',
            'bilalsada_api_key' => 'nullable|string',
            'n3tdata_api_key' => 'nullable|string',
            'superjara_api_key' => 'nullable|string',
            'alrahuz_api_key' => 'nullable|string',
        ]);

        SystemSetting::set('active_vtu_provider', $request->active_vtu_provider);

        SystemSetting::set('provider_bilalsada_status', $request->has('provider_bilalsada_status') ? 'enabled' : 'disabled');
        SystemSetting::set('provider_alrahuz_status', $request->has('provider_alrahuz_status') ? 'enabled' : 'disabled');
        SystemSetting::set('provider_n3tdata_status', $request->has('provider_n3tdata_status') ? 'enabled' : 'disabled');
        SystemSetting::set('provider_superjara_status', $request->has('provider_superjara_status') ? 'enabled' : 'disabled');

        if ($request->filled('bilalsada_api_key')) SystemSetting::set('bilalsada_api_key', $request->bilalsada_api_key);
        if ($request->filled('n3tdata_api_key')) SystemSetting::set('n3tdata_api_key', $request->n3tdata_api_key);
        if ($request->filled('superjara_api_key')) SystemSetting::set('superjara_api_key', $request->superjara_api_key);
        if ($request->filled('alrahuz_api_key')) SystemSetting::set('alrahuz_api_key', $request->alrahuz_api_key);

        return redirect()->back()->with('success', 'VTU Provider Switches & API Credentials updated successfully!');
    }
}
