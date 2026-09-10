@extends('layouts.app')

@section('title', 'Transfer Instructions')
@section('header_title', 'Airtime Transfer Instructions')

@section('content')
<div style="max-width:640px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('airtime_cash.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to Airtime to Cash
        </a>
    </div>

    <!-- Instructions Card -->
    <div class="mk-card" style="padding:28px;">
        <div style="text-align:center; padding-bottom:20px; border-bottom:1px solid rgba(145, 158, 171, 0.16); margin-bottom:24px;">
            <span class="badge {{ $request->status === 'approved' ? 'badge-success' : ($request->status === 'pending' ? 'badge-warning' : 'badge-error') }}" style="font-size:0.8125rem; padding:6px 14px; margin-bottom:12px;">
                STATUS: {{ strtoupper($request->status) }}
            </span>
            <h2 style="font-size:1.375rem; font-weight:800; color:var(--palette-text-primary); margin-bottom:4px;">
                Transfer ₦{{ number_format($request->amount, 2) }} {{ $request->network }} Airtime
            </h2>
            <p style="font-size:0.8125rem; color:var(--palette-text-secondary);">
                Order Reference: <strong style="font-family:monospace;">{{ $request->reference }}</strong>
            </p>
        </div>

        <!-- Transfer Instructions Box -->
        <div style="background:rgba(24, 119, 242, 0.05); border:1px solid rgba(24, 119, 242, 0.2); border-radius:12px; padding:20px; margin-bottom:24px;">
            <div style="font-size:0.8125rem; font-weight:700; color:var(--palette-primary-darker); margin-bottom:8px;">
                <i class="fa-solid fa-mobile-screen-button"></i> Step 1: Destination SIM Number
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; background:#FFF; padding:12px; border-radius:8px; border:1px solid rgba(145, 158, 171, 0.2); margin-bottom:16px;">
                <span style="font-size:1.375rem; font-weight:800; font-family:monospace; color:var(--palette-primary-main);">
                    {{ $request->receiver_phone }}
                </span>
                <button type="button" class="btn btn-outline btn-sm" onclick="copyText('{{ $request->receiver_phone }}', this)">
                    <i class="fa-regular fa-copy"></i> Copy Number
                </button>
            </div>

            <div style="font-size:0.8125rem; font-weight:700; color:var(--palette-primary-darker); margin-bottom:8px;">
                <i class="fa-solid fa-hashtag"></i> Step 2: Dial USSD Code on Your Phone
            </div>
            <div style="background:#FFF; padding:12px; border-radius:8px; border:1px solid rgba(145, 158, 171, 0.2); font-family:monospace; font-weight:700; font-size:1rem; color:var(--palette-text-primary); word-break:break-all;">
                {{ $ussdCode }}
            </div>
        </div>

        <!-- Details Summary -->
        <div style="background:var(--palette-background-neutral); border-radius:12px; padding:16px; margin-bottom:24px; border:1px solid rgba(145, 158, 171, 0.16);">
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.875rem;">
                <span style="color:var(--palette-text-secondary);">Your Sending Number:</span>
                <span style="font-weight:700;">{{ $request->sender_phone }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.875rem;">
                <span style="color:var(--palette-text-secondary);">Conversion Rate:</span>
                <span style="font-weight:700;">{{ $request->exchange_rate_percent }}%</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.875rem;">
                <span style="color:var(--palette-text-secondary);">Payout Method:</span>
                <span style="font-weight:700; text-transform:uppercase;">{{ $request->payout_method }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:1.125rem; font-weight:800; border-top:1px solid rgba(145, 158, 171, 0.12); padding-top:8px;">
                <span>Total Cash to Receive:</span>
                <span style="color:var(--palette-success-dark);">₦{{ number_format($request->amount_to_receive, 2) }}</span>
            </div>
        </div>

        @if($request->admin_note)
            <div class="mk-alert mk-alert-info" style="margin-bottom:0;">
                <i class="fa-solid fa-circle-info"></i>
                <div>
                    <strong>Admin Note:</strong> {{ $request->admin_note }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = orig;
        }, 2000);
    });
}
</script>
@endsection
