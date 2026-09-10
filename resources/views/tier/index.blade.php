@extends('layouts.app')

@section('title', 'Package Tiers & Wholesale Upgrades')
@section('header_title', 'Membership Tiers & Wholesale Pricing')

@section('content')
<div style="max-width: 960px; margin: 0 auto;">
    <!-- Top Hero Banner -->
    <div class="mk-card" style="margin-bottom:28px; background:linear-gradient(135deg, #1C252E 0%, #141A21 100%); color:#FFF; padding:32px; border-radius:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
            <div>
                <span class="badge" style="background:var(--palette-primary-main); color:#FFF; font-weight:700; margin-bottom:8px;">
                    CURRENT TIER: {{ strtoupper($user->getTierDisplayName()) }}
                </span>
                <h2 style="font-size:1.75rem; font-weight:800; margin:4px 0 8px;">Wholesale Reseller Pricing Plans</h2>
                <p style="font-size:0.875rem; color:rgba(255,255,255,0.8); max-width:540px;">
                    Upgrade your account tier once and enjoy permanent discounted rates on MTN, Airtel, Glo, 9mobile data, airtime, and exam PINs.
                </p>
            </div>
            <div style="font-size:3.5rem; color:#FFD700; opacity:0.9;">
                <i class="fa-solid fa-crown"></i>
            </div>
        </div>
    </div>

    <!-- Pricing Comparison Cards Grid -->
    <div class="grid-3" style="margin-bottom:32px; gap:24px;">
        <!-- TIER 1: SMART EARNER (STANDARD) -->
        <div class="mk-card" style="padding:28px; border: {{ $user->tier === 'standard' ? '2px solid var(--palette-primary-main)' : '1px solid rgba(145, 158, 171, 0.2)' }}; position:relative;">
            @if($user->tier === 'standard')
                <span class="badge badge-primary" style="position:absolute; top:14px; right:14px; background:var(--palette-primary-main); color:#FFF;">Active Plan</span>
            @endif
            <div style="font-size:1.125rem; font-weight:800; color:var(--palette-text-primary); margin-bottom:4px;">Smart Earner</div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); margin-bottom:16px;">Standard consumer rates</div>

            <div style="font-size:1.875rem; font-weight:800; color:var(--palette-text-primary); margin-bottom:20px;">
                FREE <span style="font-size:0.75rem; color:var(--palette-text-secondary); font-weight:600;">/ Lifetime</span>
            </div>

            <ul style="list-style:none; padding-left:0; margin-bottom:24px; font-size:0.8125rem; color:var(--palette-text-secondary); display:flex; flex-direction:column; gap:10px;">
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> MTN 1GB SME @ ₦280</li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> Airtime 2% Discount</li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> WAEC PIN @ ₦3,800</li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> NECO Token @ ₦1,200</li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> 24/7 Automated Webhooks</li>
            </ul>

            <button type="button" class="btn btn-outline btn-block" disabled>
                {{ $user->tier === 'standard' ? 'Current Tier' : 'Included' }}
            </button>
        </div>

        <!-- TIER 2: RESELLER AGENT -->
        <div class="mk-card" style="padding:28px; border: {{ $user->tier === 'reseller' ? '2px solid var(--palette-primary-main)' : '1px solid rgba(145, 158, 171, 0.2)' }}; position:relative; background:linear-gradient(180deg, rgba(24, 119, 242, 0.03) 0%, #FFF 100%);">
            <span class="badge badge-warning" style="position:absolute; top:14px; right:14px;">POPULAR</span>
            <div style="font-size:1.125rem; font-weight:800; color:var(--palette-primary-dark); margin-bottom:4px;">Reseller Agent</div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); margin-bottom:16px;">Ideal for VTU shops & agents</div>

            <div style="font-size:1.875rem; font-weight:800; color:var(--palette-primary-main); margin-bottom:20px;">
                ₦1,500 <span style="font-size:0.75rem; color:var(--palette-text-secondary); font-weight:600;">/ One-time fee</span>
            </div>

            <ul style="list-style:none; padding-left:0; margin-bottom:24px; font-size:0.8125rem; color:var(--palette-text-secondary); display:flex; flex-direction:column; gap:10px;">
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> MTN 1GB SME @ <strong>₦258</strong></li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> Airtime <strong>3.5%</strong> Discount</li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> WAEC PIN @ <strong>₦3,500</strong></li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> NECO Token @ <strong>₦1,050</strong></li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> Priority Customer Support</li>
            </ul>

            @if($user->tier === 'reseller')
                <button type="button" class="btn btn-success btn-block" disabled>
                    <i class="fa-solid fa-check"></i> Current Active Plan
                </button>
            @elseif($user->tier === 'vip')
                <button type="button" class="btn btn-outline btn-block" disabled>Included in VIP</button>
            @else
                <button type="button" class="btn btn-primary btn-block" onclick="openUpgradeModal('reseller', 1500)">
                    <i class="fa-solid fa-arrow-up"></i> Upgrade to Reseller
                </button>
            @endif
        </div>

        <!-- TIER 3: VIP PARTNER -->
        <div class="mk-card" style="padding:28px; border: {{ $user->tier === 'vip' ? '2px solid #FFD700' : '1px solid rgba(145, 158, 171, 0.2)' }}; position:relative; background:linear-gradient(180deg, rgba(255, 215, 0, 0.05) 0%, #FFF 100%);">
            <span class="badge" style="position:absolute; top:14px; right:14px; background:#FFD700; color:#1C252E; font-weight:800;">MAX SAVINGS</span>
            <div style="font-size:1.125rem; font-weight:800; color:#B78103; margin-bottom:4px;">VIP Partner</div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); margin-bottom:16px;">Lowest wholesale rate guaranteed</div>

            <div style="font-size:1.875rem; font-weight:800; color:#B78103; margin-bottom:20px;">
                ₦3,500 <span style="font-size:0.75rem; color:var(--palette-text-secondary); font-weight:600;">/ One-time fee</span>
            </div>

            <ul style="list-style:none; padding-left:0; margin-bottom:24px; font-size:0.8125rem; color:var(--palette-text-secondary); display:flex; flex-direction:column; gap:10px;">
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> MTN 1GB SME @ <strong>₦250 (Lowest)</strong></li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> Airtime <strong>4.5%</strong> Discount</li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> WAEC PIN @ <strong>₦3,400</strong></li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> NECO Token @ <strong>₦980</strong></li>
                <li><i class="fa-solid fa-check" style="color:var(--palette-success-main); margin-right:8px;"></i> Dedicated VIP Account Manager</li>
            </ul>

            @if($user->tier === 'vip')
                <button type="button" class="btn btn-success btn-block" disabled>
                    <i class="fa-solid fa-check"></i> Current Active Plan
                </button>
            @else
                <button type="button" class="btn btn-block btn-lg" style="background:#FFD700; color:#1C252E; font-weight:800;" onclick="openUpgradeModal('vip', 3500)">
                    <i class="fa-solid fa-crown"></i> Upgrade to VIP
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Modal for Upgrade Authorization -->
<div id="upgradeModal" class="mk-popup-overlay" style="display:none;">
    <div class="mk-popup-container">
        <button type="button" class="mk-popup-close-btn" onclick="document.getElementById('upgradeModal').style.display='none'">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div style="width:64px; height:64px; border-radius:50%; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin:0 auto 16px;">
            <i class="fa-solid fa-crown"></i>
        </div>

        <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:4px;" id="upgradeModalTitle">Upgrade Account Tier</h3>
        <p style="font-size:0.8125rem; color:var(--palette-text-secondary); margin-bottom:20px;">
            Fee: <strong style="color:var(--palette-primary-main); font-size:1.125rem;" id="upgradeModalFee">₦1,500.00</strong> will be deducted from your wallet balance.
        </p>

        <form method="POST" action="{{ route('tier.upgrade') }}" data-action-loader="true" data-loader-title="Upgrading Membership Tier..." data-loader-subtitle="Unlocking wholesale reseller rates...">
            @csrf
            <input type="hidden" name="tier" id="upgradeTargetTier">

            <button type="button" class="btn btn-primary btn-block btn-lg" style="margin-top:16px;" onclick="document.getElementById('upgradeModal').style.display='none'; openPinModal(this.form);">
                <i class="fa-solid fa-check"></i> Authorize & Upgrade Now
            </button>
        </form>
    </div>
</div>

<script>
function openUpgradeModal(tier, fee) {
    document.getElementById('upgradeTargetTier').value = tier;
    document.getElementById('upgradeModalTitle').innerText = 'Upgrade to ' + (tier === 'vip' ? 'VIP Partner' : 'Reseller Agent');
    document.getElementById('upgradeModalFee').innerText = '₦' + fee.toLocaleString() + '.00';
    document.getElementById('upgradeModal').style.display = 'flex';
}
</script>
@endsection
