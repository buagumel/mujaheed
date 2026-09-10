@extends('layouts.admin')

@section('title', 'Platform Settings & Controls')

@section('content')
<style>
    /* Minimalist Settings Layout Styles */
    .settings-container {
        max-width: 1100px;
        margin: 0 auto;
        padding-bottom: 40px;
    }

    .settings-nav {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 6px;
        background: var(--palette-background-neutral, #f4f6f8);
        border-radius: 12px;
        margin-bottom: 24px;
        scrollbar-width: none;
    }
    .settings-nav::-webkit-scrollbar { display: none; }

    .settings-nav-btn {
        padding: 10px 16px;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: var(--palette-text-secondary, #637381);
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .settings-nav-btn:hover {
        color: var(--palette-text-primary, #1c252e);
        background: rgba(145, 158, 171, 0.08);
    }
    .settings-nav-btn.active {
        background: #ffffff;
        color: var(--palette-primary-main, #1877f2);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .settings-section {
        display: none;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(145, 158, 171, 0.16);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .settings-section.active {
        display: block;
        animation: fadeIn 0.25s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--palette-text-primary, #1c252e);
        margin-bottom: 6px;
    }
    .section-subtitle {
        font-size: 0.85rem;
        color: var(--palette-text-secondary, #637381);
        margin-bottom: 24px;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .form-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    @media (max-width: 768px) {
        .settings-container { padding: 0 12px 40px; }
        .settings-section { padding: 16px; border-radius: 12px; }
        .form-grid-2, .form-grid-3, .form-grid-4 {
            grid-template-columns: 1fr !important;
            gap: 16px;
        }
        .preset-buttons {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    .sticky-actions {
        position: sticky;
        bottom: 20px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 14px 20px;
        border-radius: 12px;
        border: 1px solid rgba(145, 158, 171, 0.2);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 99;
    }
</style>

<div class="settings-container">
    <div style="margin-bottom:20px;">
        <h2 style="font-size:1.5rem; font-weight:800; color:var(--palette-text-primary);">System Configuration</h2>
        <p style="font-size:0.875rem; color:var(--palette-text-secondary);">Manage global settings, API integrations, theme customization, and feature toggles.</p>
    </div>

    @if(session('success'))
        <div style="background:rgba(34, 197, 94, 0.12); color:#15803d; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:600; font-size:0.9rem; display:flex; align-items:center; gap:10px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Minimalist Tab Navigation -->
    <div class="settings-nav">
        <button type="button" class="settings-nav-btn active" onclick="switchTab('branding')">
            <i class="fa-solid fa-palette"></i> Branding & Theme
        </button>
        <button type="button" class="settings-nav-btn" onclick="switchTab('general')">
            <i class="fa-solid fa-sliders"></i> General & Support
        </button>
        <button type="button" class="settings-nav-btn" onclick="switchTab('smtp')">
            <i class="fa-solid fa-envelope"></i> SMTP & Email
        </button>
        <button type="button" class="settings-nav-btn" onclick="switchTab('payrant')">
            <i class="fa-solid fa-building-columns"></i> Virtual Accounts
        </button>
        <button type="button" class="settings-nav-btn" onclick="switchTab('tiers')">
            <i class="fa-solid fa-crown"></i> Pricing Tiers
        </button>
        <button type="button" class="settings-nav-btn" onclick="switchTab('referral')">
            <i class="fa-solid fa-gift"></i> Referrals
        </button>
        <button type="button" class="settings-nav-btn" onclick="switchTab('swap')">
            <i class="fa-solid fa-arrow-right-arrow-left"></i> Airtime Swap
        </button>
        <button type="button" class="settings-nav-btn" onclick="switchTab('landing')">
            <i class="fa-solid fa-globe"></i> Landing Page
        </button>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" id="settingsForm">
        @csrf

        <!-- TAB 1: BRANDING & THEME -->
        <div id="tab-branding" class="settings-section active">
            <div class="section-title">Logo & Theme Branding</div>
            <div class="section-subtitle">Customize color scheme, logo graphics, favicon, and header preview.</div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label" style="font-weight:700;">Color Presets</label>
                <div class="preset-buttons" style="display:flex; flex-wrap:wrap; gap:10px;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="setThemeColors('#1877F2', '#8E33FF')"><span style="width:12px; height:12px; border-radius:50%; background:#1877F2; display:inline-block;"></span> Material Blue</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setThemeColors('#00A76F', '#FFAB00')"><span style="width:12px; height:12px; border-radius:50%; background:#00A76F; display:inline-block;"></span> Emerald Green</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setThemeColors('#8E33FF', '#00B8D9')"><span style="width:12px; height:12px; border-radius:50%; background:#8E33FF; display:inline-block;"></span> Royal Purple</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setThemeColors('#FF5630', '#FFAB00')"><span style="width:12px; height:12px; border-radius:50%; background:#FF5630; display:inline-block;"></span> Vibrant Orange</button>
                </div>
            </div>

            <div class="form-grid-2" style="margin-bottom:20px;">
                <div style="border:1px solid rgba(145, 158, 171, 0.2); border-radius:12px; padding:16px;">
                    <label class="form-label" for="primary_color" style="font-weight:700;">Primary Brand Color</label>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <input type="color" id="primary_picker" value="{{ $settings['primary_color'] }}" oninput="updatePrimaryColor(this.value)" style="width:40px; height:40px; border:none; cursor:pointer;">
                        <input type="text" id="primary_color" name="primary_color" class="form-control" value="{{ old('primary_color', $settings['primary_color']) }}" oninput="updatePrimaryColor(this.value)" required>
                    </div>
                </div>

                <div style="border:1px solid rgba(145, 158, 171, 0.2); border-radius:12px; padding:16px;">
                    <label class="form-label" for="secondary_color" style="font-weight:700;">Secondary Accent Color</label>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <input type="color" id="secondary_picker" value="{{ $settings['secondary_color'] }}" oninput="updateSecondaryColor(this.value)" style="width:40px; height:40px; border:none; cursor:pointer;">
                        <input type="text" id="secondary_color" name="secondary_color" class="form-control" value="{{ old('secondary_color', $settings['secondary_color']) }}" oninput="updateSecondaryColor(this.value)" required>
                    </div>
                </div>
            </div>

            <div class="form-grid-2" style="margin-bottom:20px;">
                <div style="border:1px solid rgba(145, 158, 171, 0.2); border-radius:12px; padding:16px;">
                    <label class="form-label" for="logo_image" style="font-weight:700;">Logo Graphic</label>
                    <input type="file" id="logo_image" name="logo_image" class="form-control" accept="image/*">
                    @if($settings['logo_image'])
                        <div style="margin-top:10px; display:flex; align-items:center; justify-content:space-between;">
                            <img src="{{ asset('storage/' . $settings['logo_image']) }}" style="height:32px; object-fit:contain;">
                            <label style="font-size:0.75rem; color:#dc2626; cursor:pointer;"><input type="checkbox" name="remove_logo" value="1"> Remove</label>
                        </div>
                    @endif
                </div>

                <div style="border:1px solid rgba(145, 158, 171, 0.2); border-radius:12px; padding:16px;">
                    <label class="form-label" for="favicon" style="font-weight:700;">Favicon Icon</label>
                    <input type="file" id="favicon" name="favicon" class="form-control" accept="image/*">
                    @if($settings['favicon'])
                        <div style="margin-top:10px; display:flex; align-items:center; justify-content:space-between;">
                            <img src="{{ asset('storage/' . $settings['favicon']) }}" style="width:24px; height:24px; object-fit:contain;">
                            <label style="font-size:0.75rem; color:#dc2626; cursor:pointer;"><input type="checkbox" name="remove_favicon" value="1"> Remove</label>
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="logo_display_mode" style="font-weight:700;">Display Mode</label>
                <select name="logo_display_mode" class="form-select">
                    <option value="image_and_text" {{ $settings['logo_display_mode'] === 'image_and_text' ? 'selected' : '' }}>Logo Image + Platform Name</option>
                    <option value="image_only" {{ $settings['logo_display_mode'] === 'image_only' ? 'selected' : '' }}>Image Only</option>
                    <option value="text_only" {{ $settings['logo_display_mode'] === 'text_only' ? 'selected' : '' }}>Text & Icon Only</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="logo_height" style="font-weight:700;">Logo Height (px)</label>
                <input type="number" id="logo_height" name="logo_height" class="form-control" min="20" max="80" value="{{ $settings['logo_height'] }}" required>
            </div>
        </div>

        <!-- TAB 2: GENERAL & SUPPORT -->
        <div id="tab-general" class="settings-section">
            <div class="section-title">General Platform & Contact Details</div>
            <div class="section-subtitle">Basic business profile, currency, and customer support channels.</div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="platform_name">Platform Name</label>
                    <input type="text" id="platform_name" name="platform_name" class="form-control" value="{{ old('platform_name', $settings['platform_name']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="platform_tagline">Tagline</label>
                    <input type="text" id="platform_tagline" name="platform_tagline" class="form-control" value="{{ old('platform_tagline', $settings['platform_tagline']) }}">
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-group">
                    <label class="form-label" for="support_email">Support Email</label>
                    <input type="email" id="support_email" name="support_email" class="form-control" value="{{ old('support_email', $settings['support_email']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="support_phone">Support Phone</label>
                    <input type="text" id="support_phone" name="support_phone" class="form-control" value="{{ old('support_phone', $settings['support_phone']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="support_whatsapp">WhatsApp Line</label>
                    <input type="text" id="support_whatsapp" name="support_whatsapp" class="form-control" value="{{ old('support_whatsapp', $settings['support_whatsapp']) }}">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="currency">Currency Code</label>
                    <input type="text" id="currency" name="currency" class="form-control" value="{{ old('currency', $settings['currency']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="currency_symbol">Currency Symbol</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="allow_registration">Registration Status</label>
                <select name="allow_registration" class="form-select">
                    <option value="open" {{ $settings['allow_registration'] === 'open' ? 'selected' : '' }}>Open (New users can register)</option>
                    <option value="closed" {{ $settings['allow_registration'] === 'closed' ? 'selected' : '' }}>Closed (Registrations paused)</option>
                </select>
            </div>
        </div>

        <!-- TAB 3: SMTP & EMAIL -->
        <div id="tab-smtp" class="settings-section">
            <div class="section-title">SMTP Mail Configuration</div>
            <div class="section-subtitle">Configure outbound transactional emails and verification OTP dispatch.</div>

            <div style="margin-bottom:16px;">
                <button type="button" class="btn btn-outline btn-sm" onclick="applyGmailPreset()"><i class="fa-brands fa-google"></i> Apply Gmail Preset</button>
            </div>

            <input type="hidden" name="mail_mailer" value="smtp">

            <div class="form-grid-3">
                <div class="form-group">
                    <label class="form-label" for="mail_host">SMTP Host</label>
                    <input type="text" id="mail_host" name="mail_host" class="form-control" value="{{ old('mail_host', $settings['mail_host']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="mail_port">SMTP Port</label>
                    <input type="number" id="mail_port" name="mail_port" class="form-control" value="{{ old('mail_port', $settings['mail_port']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="mail_encryption">Encryption</label>
                    <select id="mail_encryption" name="mail_encryption" class="form-select">
                        <option value="tls" {{ $settings['mail_encryption'] === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $settings['mail_encryption'] === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ $settings['mail_encryption'] === 'none' ? 'selected' : '' }}>None</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="mail_username">SMTP Username</label>
                    <input type="text" id="mail_username" name="mail_username" class="form-control" value="{{ old('mail_username', $settings['mail_username']) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="mail_password">SMTP Password</label>
                    <input type="password" id="mail_password" name="mail_password" class="form-control" value="{{ old('mail_password', $settings['mail_password']) }}">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="mail_from_address">From Address</label>
                    <input type="email" id="mail_from_address" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="mail_from_name">From Sender Name</label>
                    <input type="text" id="mail_from_name" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" required>
                </div>
            </div>
        </div>

        <!-- TAB 4: PAYRANT & VIRTUAL ACCOUNTS -->
        <div id="tab-payrant" class="settings-section">
            <div class="section-title">Dedicated Virtual Bank Accounts (Payrant)</div>
            <div class="section-subtitle">Automated user wallet funding via permanent PalmPay / Providus bank accounts.</div>

            <div style="background:var(--palette-background-neutral, #f4f6f8); padding:14px; border-radius:10px; margin-bottom:20px; font-size:0.85rem;">
                <div style="font-weight:700; margin-bottom:4px;">Payrant Webhook URL:</div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="text" readonly class="form-control" value="{{ url('/webhook/payrant') }}" style="font-family:monospace; font-size:0.8rem; background:#fff;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="navigator.clipboard.writeText('{{ url('/webhook/payrant') }}'); alert('Copied!');">Copy</button>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="payrant_status">Gateway Status</label>
                    <select name="payrant_status" class="form-select">
                        <option value="active" {{ $settings['payrant_status'] === 'active' ? 'selected' : '' }}>Enabled</option>
                        <option value="inactive" {{ $settings['payrant_status'] === 'inactive' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="payrant_environment">Environment</label>
                    <select name="payrant_environment" class="form-select">
                        <option value="live" {{ $settings['payrant_environment'] === 'live' ? 'selected' : '' }}>Live Production</option>
                        <option value="test" {{ $settings['payrant_environment'] === 'test' ? 'selected' : '' }}>Sandbox / Test</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="payrant_live_public_key">Live Public Key</label>
                    <input type="text" name="payrant_live_public_key" class="form-control" value="{{ old('payrant_live_public_key', $settings['payrant_live_public_key']) }}" style="font-family:monospace;">
                </div>
                <div class="form-group">
                    <label class="form-label" for="payrant_live_secret_key">Live Secret Key</label>
                    <input type="password" name="payrant_live_secret_key" class="form-control" value="{{ old('payrant_live_secret_key', $settings['payrant_live_secret_key']) }}" style="font-family:monospace;">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="payrant_webhook_secret">Webhook Secret Key</label>
                    <input type="password" name="payrant_webhook_secret" class="form-control" value="{{ old('payrant_webhook_secret', $settings['payrant_webhook_secret']) }}" style="font-family:monospace;">
                </div>
                <div class="form-group">
                    <label class="form-label" for="payrant_funding_fee">Deposit Fee (₦)</label>
                    <input type="number" name="payrant_funding_fee" class="form-control" value="{{ old('payrant_funding_fee', $settings['payrant_funding_fee']) }}" step="0.01">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="payrant_default_bvn">Default Verification BVN (Used for Virtual Account Generation)</label>
                <input type="text" id="payrant_default_bvn" name="payrant_default_bvn" class="form-control" value="{{ old('payrant_default_bvn', $settings['payrant_default_bvn']) }}" placeholder="22596036771" style="font-family:monospace; font-weight:700;">
                <span style="font-size:0.75rem; color:var(--palette-text-secondary); margin-top:4px; display:block;">
                    This verified 11-digit NIBSS BVN will be passed to Payrant API for instant live PalmPay virtual account issuance.
                </span>
            </div>
        </div>

        <!-- TAB 5: MEMBERSHIP TIERS -->
        <div id="tab-tiers" class="settings-section">
            <div class="section-title">Wholesale Tiers & Discounts</div>
            <div class="section-subtitle">Set upgrade fees and cashback discounts for Resellers and VIP Partners.</div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="reseller_upgrade_fee">Reseller Upgrade Fee (₦)</label>
                    <input type="number" name="reseller_upgrade_fee" class="form-control" value="{{ old('reseller_upgrade_fee', $settings['reseller_upgrade_fee']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="vip_upgrade_fee">VIP Upgrade Fee (₦)</label>
                    <input type="number" name="vip_upgrade_fee" class="form-control" value="{{ old('vip_upgrade_fee', $settings['vip_upgrade_fee']) }}" required>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="reseller_airtime_discount">Reseller Cashback (%)</label>
                    <input type="number" name="reseller_airtime_discount" class="form-control" value="{{ old('reseller_airtime_discount', $settings['reseller_airtime_discount']) }}" step="0.1" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="vip_airtime_discount">VIP Cashback (%)</label>
                    <input type="number" name="vip_airtime_discount" class="form-control" value="{{ old('vip_airtime_discount', $settings['vip_airtime_discount']) }}" step="0.1" required>
                </div>
            </div>
        </div>

        <!-- TAB 6: REFERRALS -->
        <div id="tab-referral" class="settings-section">
            <div class="section-title">Referral & Affiliate Program</div>
            <div class="section-subtitle">Reward users for inviting new customers to the platform.</div>

            <div class="form-grid-3">
                <div class="form-group">
                    <label class="form-label" for="referral_status">Referral System</label>
                    <select name="referral_status" class="form-select">
                        <option value="active" {{ $settings['referral_status'] === 'active' ? 'selected' : '' }}>Enabled</option>
                        <option value="inactive" {{ $settings['referral_status'] === 'inactive' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="referral_reward_type">Bonus Type</label>
                    <select name="referral_reward_type" class="form-select">
                        <option value="flat" {{ $settings['referral_reward_type'] === 'flat' ? 'selected' : '' }}>Flat Bonus (₦)</option>
                        <option value="percent" {{ $settings['referral_reward_type'] === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="referral_flat_bonus">Flat Bonus Amount (₦)</label>
                    <input type="number" name="referral_flat_bonus" class="form-control" value="{{ old('referral_flat_bonus', $settings['referral_flat_bonus']) }}" required>
                </div>
            </div>
        </div>

        <!-- TAB 7: AIRTIME SWAP -->
        <div id="tab-swap" class="settings-section">
            <div class="section-title">Airtime to Cash Rates</div>
            <div class="section-subtitle">Configure cash conversion percentages and receiving phone numbers.</div>

            <div class="form-group">
                <label class="form-label" for="airtime_cash_status">Airtime Swap Feature</label>
                <select name="airtime_cash_status" class="form-select">
                    <option value="active" {{ $settings['airtime_cash_status'] === 'active' ? 'selected' : '' }}>Enabled</option>
                    <option value="inactive" {{ $settings['airtime_cash_status'] === 'inactive' ? 'selected' : '' }}>Disabled</option>
                </select>
            </div>

            <div class="form-grid-4">
                <div class="form-group">
                    <label class="form-label">MTN Rate (%)</label>
                    <input type="number" name="airtime_cash_rate_mtn" class="form-control" value="{{ old('airtime_cash_rate_mtn', $settings['airtime_cash_rate_mtn']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Airtel Rate (%)</label>
                    <input type="number" name="airtime_cash_rate_airtel" class="form-control" value="{{ old('airtime_cash_rate_airtel', $settings['airtime_cash_rate_airtel']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Glo Rate (%)</label>
                    <input type="number" name="airtime_cash_rate_glo" class="form-control" value="{{ old('airtime_cash_rate_glo', $settings['airtime_cash_rate_glo']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">9mobile Rate (%)</label>
                    <input type="number" name="airtime_cash_rate_9mobile" class="form-control" value="{{ old('airtime_cash_rate_9mobile', $settings['airtime_cash_rate_9mobile']) }}" required>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">MTN Receiving SIM</label>
                    <input type="text" name="airtime_cash_receiver_mtn" class="form-control" value="{{ old('airtime_cash_receiver_mtn', $settings['airtime_cash_receiver_mtn']) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Airtel Receiving SIM</label>
                    <input type="text" name="airtime_cash_receiver_airtel" class="form-control" value="{{ old('airtime_cash_receiver_airtel', $settings['airtime_cash_receiver_airtel']) }}" required>
                </div>
            </div>
        </div>

        <!-- TAB 8: LANDING PAGE -->
        <div id="tab-landing" class="settings-section">
            <div class="section-title">Landing Page Headlines & Downloads</div>
            <div class="section-subtitle">Manage public homepage text, hero banner, and mobile app download links.</div>

            <div class="form-group">
                <label class="form-label" for="hero_title">Hero Headline</label>
                <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $settings['hero_title']) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="hero_subtitle">Hero Subtitle</label>
                <textarea name="hero_subtitle" class="form-control" rows="2" required>{{ old('hero_subtitle', $settings['hero_subtitle']) }}</textarea>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="google_play_url">Google Play Store URL</label>
                    <input type="text" name="google_play_url" class="form-control" value="{{ old('google_play_url', $settings['google_play_url']) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="ios_app_store_url">iOS App Store URL</label>
                    <input type="text" name="ios_app_store_url" class="form-control" value="{{ old('ios_app_store_url', $settings['ios_app_store_url']) }}">
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Action Bar -->
        <div class="sticky-actions">
            <div style="font-size:0.85rem; color:var(--palette-text-secondary); display:flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-shield-halved" style="color:#22c55e;"></i> System Changes Guarded
            </div>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-weight:700;">
                <i class="fa-solid fa-floppy-disk" style="margin-right:6px;"></i> Save Settings
            </button>
        </div>
    </form>
</div>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.settings-section').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.settings-nav-btn').forEach(el => el.classList.remove('active'));

        const targetTab = document.getElementById('tab-' + tabId);
        if (targetTab) targetTab.classList.add('active');

        event.currentTarget.classList.add('active');
    }

    function setThemeColors(primary, secondary) {
        document.getElementById('primary_color').value = primary;
        document.getElementById('primary_picker').value = primary;
        document.getElementById('secondary_color').value = secondary;
        document.getElementById('secondary_picker').value = secondary;
        updatePrimaryColor(primary);
        updateSecondaryColor(secondary);
    }

    function updatePrimaryColor(hex) {
        document.documentElement.style.setProperty('--palette-primary-main', hex);
    }

    function updateSecondaryColor(hex) {
        document.documentElement.style.setProperty('--palette-secondary-main', hex);
    }

    function applyGmailPreset() {
        document.getElementById('mail_host').value = 'smtp.gmail.com';
        document.getElementById('mail_port').value = '587';
        document.getElementById('mail_encryption').value = 'tls';
    }
</script>
@endsection
