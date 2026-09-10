@extends('layouts.app')

@section('title', 'Customer Support')
@section('header_title', 'Help & Customer Support')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <!-- Top Support Hero -->
    <div class="mk-card" style="margin-bottom:28px; text-align:center; padding:36px;">
        <div style="width:64px; height:64px; border-radius:50%; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin: 0 auto 16px;">
            <i class="fa-solid fa-headset"></i>
        </div>
        <h2 style="font-size:1.5rem; font-weight:800; margin-bottom:8px;">How can we help you today?</h2>
        <p style="font-size:0.875rem; color:var(--palette-text-secondary); max-width:500px; margin:0 auto 24px;">
            Have questions about an airtime recharge, data delivery, electricity token or wallet funding? Submit a support ticket or reach out directly.
        </p>

        <div style="display:flex; justify-content:center; gap:12px; margin-bottom:32px;">
            <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-plus-circle"></i> Open New Support Ticket
            </a>
            <a href="{{ route('tickets.index') }}" class="btn btn-outline btn-lg">
                <i class="fa-solid fa-ticket"></i> My Support Tickets
            </a>
        </div>

        <div class="grid-3" style="text-align:left;">
            <div style="background:var(--palette-background-neutral); border-radius:12px; padding:20px; border:1px solid rgba(145, 158, 171, 0.2);">
                <div style="font-size:1.25rem; color:var(--palette-primary-main); margin-bottom:8px;">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">Email Support</div>
                <a href="mailto:{{ $supportEmail }}" style="font-weight:700; font-size:0.9375rem; word-break:break-all; display:block; margin-top:4px;">{{ $supportEmail }}</a>
            </div>

            <div style="background:var(--palette-background-neutral); border-radius:12px; padding:20px; border:1px solid rgba(145, 158, 171, 0.2);">
                <div style="font-size:1.25rem; color:var(--palette-success-main); margin-bottom:8px;">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">WhatsApp Helpdesk</div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supportWhatsapp) }}" target="_blank" style="font-weight:700; font-size:0.9375rem; display:block; margin-top:4px;">{{ $supportWhatsapp }}</a>
            </div>

            <div style="background:var(--palette-background-neutral); border-radius:12px; padding:20px; border:1px solid rgba(145, 158, 171, 0.2);">
                <div style="font-size:1.25rem; color:var(--palette-warning-dark); margin-bottom:8px;">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">Phone Helpdesk</div>
                <div style="font-weight:700; font-size:0.9375rem; margin-top:4px;">{{ $supportPhone }}</div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mk-card">
        <h3 class="mk-card-title" style="margin-bottom:20px;">Frequently Asked Questions</h3>
        <div style="display:flex; flex-direction:column; gap:16px;">
            <div style="border-bottom:1px solid rgba(145, 158, 171, 0.16); padding-bottom:14px;">
                <div style="font-weight:700; color:var(--palette-text-primary); margin-bottom:4px;">How long does airtime or data delivery take?</div>
                <p style="font-size:0.875rem; color:var(--palette-text-secondary);">All VTU recharges are automated and delivered instantly within 3 to 10 seconds of payment confirmation.</p>
            </div>

            <div style="border-bottom:1px solid rgba(145, 158, 171, 0.16); padding-bottom:14px;">
                <div style="font-weight:700; color:var(--palette-text-primary); margin-bottom:4px;">What happens if a transaction fails?</div>
                <p style="font-size:0.875rem; color:var(--palette-text-secondary);">Our system automatically detects failed provider responses and instantly refunds your wallet atomically without any delays.</p>
            </div>

            <div>
                <div style="font-weight:700; color:var(--palette-text-primary); margin-bottom:4px;">How do I retrieve my electricity token?</div>
                <p style="font-size:0.875rem; color:var(--palette-text-secondary);">Your 20-digit token is displayed instantly on your screen and saved permanently in your transaction history and printable receipt.</p>
            </div>
        </div>
    </div>
</div>
@endsection
