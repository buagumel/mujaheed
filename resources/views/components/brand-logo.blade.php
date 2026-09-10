@php
    $platformName = \App\Models\SystemSetting::get('platform_name', 'VTU Express');
    $platformTagline = \App\Models\SystemSetting::get('platform_tagline', 'INSTANT RECHARGE');
    $displayMode = \App\Models\SystemSetting::get('logo_display_mode', 'image_and_text');
    $logoImage = \App\Models\SystemSetting::get('logo_image', null);
    $logoHeight = (int) \App\Models\SystemSetting::get('logo_height', 38);
    $isAdmin = $isAdmin ?? false;
    $logoUrl = $logoImage ? (str_starts_with($logoImage, 'http') ? $logoImage : asset('storage/' . $logoImage)) : null;
@endphp

<div class="brand-logo-container" style="display:flex; align-items:center; gap:12px; max-width:100%;">
    @if($displayMode === 'image_only' && $logoUrl)
        <!-- Image Only Mode (When the logo graphic already contains the name/text) -->
        <img src="{{ $logoUrl }}" 
             alt="{{ $platformName }}" 
             style="height:{{ $logoHeight }}px; max-height:60px; max-width:100%; object-fit:contain; display:block;">
    @elseif($displayMode === 'text_only')
        <!-- Text Only Mode -->
        <div style="width:{{ $logoHeight }}px; height:{{ $logoHeight }}px; border-radius:10px; background:{{ $isAdmin ? 'var(--palette-grey-900)' : 'var(--palette-primary-main)' }}; color:#FFF; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.25rem; flex-shrink:0;">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <div style="min-width:0; overflow:hidden;">
            <div style="font-size:1.125rem; font-weight:800; color:var(--palette-text-primary); line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                {{ $platformName }}
            </div>
            @if($platformTagline)
                <div style="font-size:0.6875rem; color:{{ $isAdmin ? 'var(--palette-error-main)' : 'var(--palette-text-secondary)' }}; font-weight:700; letter-spacing:0.5px; text-transform:uppercase;">
                    {{ $isAdmin ? 'ADMIN PORTAL' : $platformTagline }}
                </div>
            @endif
        </div>
    @else
        <!-- Image and Text Mode (Default) -->
        @if($logoUrl)
            <img src="{{ $logoUrl }}" 
                 alt="{{ $platformName }}" 
                 style="height:{{ $logoHeight }}px; max-height:50px; max-width:{{ $logoHeight * 1.5 }}px; object-fit:contain; border-radius:8px; flex-shrink:0;">
        @else
            <div style="width:{{ $logoHeight }}px; height:{{ $logoHeight }}px; border-radius:10px; background:{{ $isAdmin ? 'var(--palette-grey-900)' : 'var(--palette-primary-main)' }}; color:#FFF; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.25rem; flex-shrink:0;">
                <i class="fa-solid {{ $isAdmin ? 'fa-screwdriver-wrench' : 'fa-bolt' }}"></i>
            </div>
        @endif
        <div style="min-width:0; overflow:hidden;">
            <div style="font-size:1.125rem; font-weight:800; color:var(--palette-text-primary); line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                {{ $platformName }}
            </div>
            @if($platformTagline)
                <div style="font-size:0.6875rem; color:{{ $isAdmin ? 'var(--palette-error-main)' : 'var(--palette-text-secondary)' }}; font-weight:700; letter-spacing:0.5px; text-transform:uppercase;">
                    {{ $isAdmin ? 'ADMIN PORTAL' : $platformTagline }}
                </div>
            @endif
        </div>
    @endif
</div>
