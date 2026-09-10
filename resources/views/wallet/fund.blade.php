@extends('layouts.app')

@section('title', 'Fund Wallet')

@section('content')
@php
    $virtualAccounts = $user->getOrCreateVirtualAccounts();
    $primaryAccount = $virtualAccounts->first();
@endphp

<div style="max-width: 540px; margin: 20px auto;">
    <!-- ULTRA-MINIMALIST FUND WALLET CARD -->
    <div style="background: #FFF; border-radius: 20px; padding: clamp(24px, 5vw, 36px); box-shadow: var(--shadow-card); border: 1px solid rgba(145, 158, 171, 0.16); text-align: center;">

        <div style="width: 52px; height: 52px; background: rgba(24, 119, 242, 0.08); border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <i class="fa-solid fa-building-columns" style="font-size: 1.4rem; color: var(--palette-primary-main);"></i>
        </div>

        <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--palette-text-primary); margin: 0;">
            Bank Transfer Top-up
        </h3>
        <p style="font-size: 0.85rem; color: var(--palette-text-secondary); margin: 6px 0 24px;">
            Transfer from any banking app to fund your wallet instantly.
        </p>

        @if($primaryAccount)
            <!-- Sleek Dark Account Pill -->
            <div style="background: linear-gradient(135deg, #181E25 0%, #11151A 100%); color: #FFF; border-radius: 16px; padding: 22px 20px; margin-bottom: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); text-align: left; position: relative; overflow: hidden;">
                <!-- Decorative Glow -->
                <div style="position: absolute; right: -20px; top: -20px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(211, 47, 47, 0.3) 0%, rgba(0,0,0,0) 70%); border-radius: 50%; pointer-events: none;"></div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; position: relative; z-index: 1;">
                    <span class="badge" style="background: rgba(34, 197, 94, 0.18); color: #4ADE80; font-weight: 700; font-size: 0.75rem; padding: 4px 8px;">
                        {{ $primaryAccount->bank_name }}
                    </span>
                    <span style="font-size: 0.75rem; color: rgba(255,255,255,0.5);">
                        Instant 24/7
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; position: relative; z-index: 1;">
                    <div style="font-family: monospace; font-size: clamp(1.4rem, 5vw, 1.8rem); font-weight: 800; letter-spacing: 2px; color: #FFF;">
                        {{ $primaryAccount->account_number }}
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="copyToClipboard('{{ $primaryAccount->account_number }}', this)" style="padding: 7px 16px; font-weight: 700; gap: 6px;">
                        <i class="fa-regular fa-copy"></i> Copy
                    </button>
                </div>

                <div style="font-size: 0.8125rem; color: rgba(255, 255, 255, 0.6); position: relative; z-index: 1;">
                    Account Name: <strong style="color: #FFF;">{{ $primaryAccount->account_name }}</strong>
                </div>
            </div>
        @else
            <div style="padding: 24px; color: var(--palette-text-secondary);">
                Generating your dedicated account...
            </div>
        @endif

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(145, 158, 171, 0.16); padding-top: 18px; flex-wrap: wrap; gap: 10px;">
            <div style="font-size: 0.8125rem; color: var(--palette-text-secondary);">
                Current Balance: <strong style="color: var(--palette-text-primary);">₦{{ number_format($wallet->balance, 2) }}</strong>
            </div>
            <a href="{{ route('wallet.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Back to Wallet
            </a>
        </div>

    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check" style="color:#4ADE80;"></i> Copied';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 2000);
    });
}
</script>
@endsection
