@extends('layouts.admin')

@section('title', 'VTU Provider Management')

@section('content')
<style>
    .provider-container {
        max-width: 1100px;
        margin: 0 auto;
        padding-bottom: 40px;
    }
    .provider-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(145, 158, 171, 0.16);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .provider-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    @media (max-width: 768px) {
        .provider-container { padding: 0 12px 40px; }
        .provider-card { padding: 16px; border-radius: 12px; }
        .provider-grid { grid-template-columns: 1fr !important; }
    }
</style>

<div class="provider-container">
    <div style="margin-bottom:20px;">
        <h2 style="font-size:1.5rem; font-weight:800; color:var(--palette-text-primary);">VTU API Drivers & Gateways</h2>
        <p style="font-size:0.875rem; color:var(--palette-text-secondary);">Manage active VTU provider driver and configure secret credentials for BilalsadaSub, Alrahuz, N3TData, and Superjara.</p>
    </div>

    @if(session('success'))
        <div style="background:rgba(34, 197, 94, 0.12); color:#15803d; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:600; font-size:0.9rem; display:flex; align-items:center; gap:10px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.providers.update') }}" method="POST">
        @csrf

        <!-- ACTIVE PROVIDER SELECTION -->
        <div class="provider-card" style="background:var(--palette-background-neutral, #f4f6f8); border:1px solid rgba(145, 158, 171, 0.24);">
            <label style="font-weight:800; font-size:1rem; display:block; margin-bottom:8px; color:var(--palette-text-primary);">
                <i class="fa-solid fa-tower-cell" style="color:var(--palette-primary-main, #1877f2); margin-right:6px;"></i> Active Primary VTU Provider Driver
            </label>
            <select name="active_vtu_provider" class="form-select" style="max-width:400px; font-weight:700; font-size:0.95rem; padding:12px;">
                <option value="bilalsada" {{ $activeProvider === 'bilalsada' ? 'selected' : '' }}>BilalsadaSub (Live Production)</option>
                <option value="alrahuz" {{ $activeProvider === 'alrahuz' ? 'selected' : '' }}>AlrahuzData (Live Production)</option>
                <option value="n3tdata" {{ $activeProvider === 'n3tdata' ? 'selected' : '' }}>N3TData</option>
                <option value="superjara" {{ $activeProvider === 'superjara' ? 'selected' : '' }}>Superjara</option>
                <option value="mock" {{ $activeProvider === 'mock' ? 'selected' : '' }}>Mock / Test Sandbox Mode</option>
            </select>
            <p style="font-size:0.825rem; color:var(--palette-text-secondary); margin-top:8px;">All automated airtime, data bundle, cable TV, and electricity purchases from the user dashboard will be fulfilled through this provider.</p>
        </div>

        <h3 style="font-size:1.125rem; font-weight:800; margin:24px 0 16px; color:var(--palette-text-primary);">API Providers & Credentials</h3>

        <div class="provider-grid">
            <!-- Bilalsada -->
            <div class="provider-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                    <div>
                        <div style="font-weight:800; font-size:1rem; color:var(--palette-text-primary);">BilalsadaSub</div>
                        <label style="display:inline-flex; align-items:center; gap:6px; margin-top:4px; cursor:pointer;">
                            <input type="checkbox" name="provider_bilalsada_status" value="enabled" {{ $providers['bilalsada']['status'] === 'enabled' ? 'checked' : '' }}>
                            <span style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary);">Provider Enabled</span>
                        </label>
                    </div>
                    @if($activeProvider === 'bilalsada')
                        <span class="badge badge-success" style="background:rgba(34, 197, 94, 0.15); color:#15803d; font-weight:700;">PRIMARY DRIVER</span>
                    @endif
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label" style="font-size:0.8rem; font-weight:700;">Base URL</label>
                    <input type="text" value="{{ $providers['bilalsada']['base_url'] }}" readonly class="form-control" style="background:#f9f9f9; font-size:0.85rem;">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size:0.8rem; font-weight:700;">API Token Key</label>
                    <input type="password" name="bilalsada_api_key" value="{{ $providers['bilalsada']['api_key'] }}" class="form-control" style="font-family:monospace; font-size:0.85rem;">
                </div>
            </div>

            <!-- Alrahuz -->
            <div class="provider-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                    <div>
                        <div style="font-weight:800; font-size:1rem; color:var(--palette-text-primary);">AlrahuzData</div>
                        <label style="display:inline-flex; align-items:center; gap:6px; margin-top:4px; cursor:pointer;">
                            <input type="checkbox" name="provider_alrahuz_status" value="enabled" {{ $providers['alrahuz']['status'] === 'enabled' ? 'checked' : '' }}>
                            <span style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary);">Provider Enabled</span>
                        </label>
                    </div>
                    @if($activeProvider === 'alrahuz')
                        <span class="badge badge-success" style="background:rgba(34, 197, 94, 0.15); color:#15803d; font-weight:700;">PRIMARY DRIVER</span>
                    @endif
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label" style="font-size:0.8rem; font-weight:700;">Base URL</label>
                    <input type="text" value="{{ $providers['alrahuz']['base_url'] }}" readonly class="form-control" style="background:#f9f9f9; font-size:0.85rem;">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size:0.8rem; font-weight:700;">API Token Key</label>
                    <input type="password" name="alrahuz_api_key" value="{{ $providers['alrahuz']['api_key'] }}" class="form-control" style="font-family:monospace; font-size:0.85rem;">
                </div>
            </div>

            <!-- N3TData -->
            <div class="provider-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                    <div>
                        <div style="font-weight:800; font-size:1rem; color:var(--palette-text-primary);">N3TData</div>
                        <label style="display:inline-flex; align-items:center; gap:6px; margin-top:4px; cursor:pointer;">
                            <input type="checkbox" name="provider_n3tdata_status" value="enabled" {{ $providers['n3tdata']['status'] === 'enabled' ? 'checked' : '' }}>
                            <span style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary);">Provider Enabled</span>
                        </label>
                    </div>
                    @if($activeProvider === 'n3tdata')
                        <span class="badge badge-success" style="background:rgba(34, 197, 94, 0.15); color:#15803d; font-weight:700;">PRIMARY DRIVER</span>
                    @endif
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label" style="font-size:0.8rem; font-weight:700;">Base URL</label>
                    <input type="text" value="{{ $providers['n3tdata']['base_url'] }}" readonly class="form-control" style="background:#f9f9f9; font-size:0.85rem;">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size:0.8rem; font-weight:700;">API Token Key</label>
                    <input type="password" name="n3tdata_api_key" value="{{ $providers['n3tdata']['api_key'] }}" class="form-control" style="font-family:monospace; font-size:0.85rem;" placeholder="Enter Token Key">
                </div>
            </div>

            <!-- Superjara -->
            <div class="provider-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                    <div>
                        <div style="font-weight:800; font-size:1rem; color:var(--palette-text-primary);">Superjara</div>
                        <label style="display:inline-flex; align-items:center; gap:6px; margin-top:4px; cursor:pointer;">
                            <input type="checkbox" name="provider_superjara_status" value="enabled" {{ $providers['superjara']['status'] === 'enabled' ? 'checked' : '' }}>
                            <span style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary);">Provider Enabled</span>
                        </label>
                    </div>
                    @if($activeProvider === 'superjara')
                        <span class="badge badge-success" style="background:rgba(34, 197, 94, 0.15); color:#15803d; font-weight:700;">PRIMARY DRIVER</span>
                    @endif
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label" style="font-size:0.8rem; font-weight:700;">Base URL</label>
                    <input type="text" value="{{ $providers['superjara']['base_url'] }}" readonly class="form-control" style="background:#f9f9f9; font-size:0.85rem;">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size:0.8rem; font-weight:700;">API Token Key</label>
                    <input type="password" name="superjara_api_key" value="{{ $providers['superjara']['api_key'] }}" class="form-control" style="font-family:monospace; font-size:0.85rem;" placeholder="Enter Token Key">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="padding:12px 28px; font-weight:700; border-radius:10px;">
            <i class="fa-solid fa-floppy-disk" style="margin-right:8px;"></i> Save Provider Configurations
        </button>
    </form>
</div>
@endsection
