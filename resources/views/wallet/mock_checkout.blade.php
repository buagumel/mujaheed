@extends('layouts.app')

@section('title', 'Complete Payment')
@section('header_title', 'Payment Gateway Simulator')

@section('content')
<div style="max-width: 520px; margin: 20px auto;">
    <div class="mk-card" style="text-align:center;">
        <div style="width:64px; height:64px; border-radius:50%; background:var(--color-primary-light); color:var(--color-primary-dark); display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin: 0 auto 16px;">
            <i class="fa-solid fa-lock"></i>
        </div>

        <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:4px;">Payment Gateway Simulator</h3>
        <p style="font-size:0.8125rem; color:var(--text-secondary); margin-bottom:24px;">Test Mode: Simulate payment verification without real credit card charge</p>

        <div style="background:var(--bg-neutral); border-radius:var(--border-radius-md); padding:20px; text-align:left; margin-bottom:24px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.875rem;">
                <span style="color:var(--text-secondary);">Reference:</span>
                <span style="font-family:monospace; font-weight:700;">{{ $payment->reference }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.875rem;">
                <span style="color:var(--text-secondary);">Customer:</span>
                <span style="font-weight:600;">{{ $payment->user->name }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:1.125rem; font-weight:800; border-top:1px solid var(--border-color); padding-top:10px; margin-top:10px;">
                <span>Total Amount:</span>
                <span style="color:var(--color-primary);">₦{{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('wallet.fund.mock.complete', $payment->reference) }}">
            @csrf

            <div style="display:flex; gap:12px;">
                <button type="submit" name="action" value="success" class="btn btn-success btn-lg" style="flex:1;">
                    <i class="fa-solid fa-circle-check"></i> Simulate Success
                </button>
                <button type="submit" name="action" value="cancel" class="btn btn-danger btn-lg" style="flex:1;">
                    <i class="fa-solid fa-circle-xmark"></i> Cancel Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
