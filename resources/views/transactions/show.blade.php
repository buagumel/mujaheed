@extends('layouts.app')

@section('title', 'Transaction Details')
@section('header_title', 'Transaction #' . $transaction->reference)

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Transaction Summary</h3>
                <p class="mk-card-subtitle">Processed on {{ $transaction->created_at->format('M d, Y h:i:s A') }}</p>
            </div>
            <div>
                @if($transaction->status === 'successful')
                    <span class="badge badge-success" style="font-size:0.875rem; padding:6px 14px;">Successful</span>
                @elseif($transaction->status === 'pending')
                    <span class="badge badge-warning" style="font-size:0.875rem; padding:6px 14px;">Pending</span>
                @elseif($transaction->status === 'reversed')
                    <span class="badge badge-info" style="font-size:0.875rem; padding:6px 14px;">Refunded</span>
                @else
                    <span class="badge badge-error" style="font-size:0.875rem; padding:6px 14px;">Failed</span>
                @endif
            </div>
        </div>

        @if($transaction->token)
            <div style="background:var(--color-success-light); border:1px solid var(--color-success-dark); border-radius:var(--border-radius-md); padding:20px; text-align:center; margin-bottom:24px;">
                <div style="font-size:0.8125rem; font-weight:700; color:var(--color-success-dark); text-transform:uppercase; letter-spacing:1px;">Prepaid Meter Token</div>
                <div style="font-size:1.75rem; font-weight:800; font-family:monospace; color:var(--color-success-dark); margin:8px 0; letter-spacing:2px;">{{ $transaction->token }}</div>
                @if($transaction->units)
                    <div style="font-size:0.875rem; color:var(--color-success-dark); font-weight:600;">Units Generated: {{ $transaction->units }}</div>
                @endif
            </div>
        @endif

        <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:28px;">
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:12px;">
                <span style="color:var(--text-secondary);">Transaction Reference</span>
                <span style="font-weight:700; font-family:monospace;">{{ $transaction->reference }}</span>
            </div>

            <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:12px;">
                <span style="color:var(--text-secondary);">Service Type</span>
                <span style="font-weight:700; text-transform:capitalize;">{{ $transaction->service_type }}</span>
            </div>

            <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:12px;">
                <span style="color:var(--text-secondary);">Provider / Network</span>
                <span style="font-weight:700;">{{ $transaction->provider }}</span>
            </div>

            @if($transaction->plan_name)
                <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:12px;">
                    <span style="color:var(--text-secondary);">Plan / Package</span>
                    <span style="font-weight:700;">{{ $transaction->plan_name }}</span>
                </div>
            @endif

            <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:12px;">
                <span style="color:var(--text-secondary);">Recipient</span>
                <span style="font-weight:700;">{{ $transaction->recipient }}</span>
            </div>

            @if($transaction->customer_name)
                <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:12px;">
                    <span style="color:var(--text-secondary);">Customer Name</span>
                    <span style="font-weight:700;">{{ $transaction->customer_name }}</span>
                </div>
            @endif

            @if($transaction->discount_amount > 0)
                <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:12px;">
                    <span style="color:var(--text-secondary);">Discount Savings</span>
                    <span style="color:var(--color-success-dark); font-weight:700;">-₦{{ number_format($transaction->discount_amount, 2) }}</span>
                </div>
            @endif

            <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:12px; font-size:1.125rem;">
                <span style="font-weight:700;">Amount Paid</span>
                <span style="font-weight:800; color:var(--color-primary);">₦{{ number_format($transaction->amount - $transaction->discount_amount, 2) }}</span>
            </div>
        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a href="{{ route('transactions.index') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Transactions
            </a>
            @if($transaction->status === 'successful')
                <a href="{{ route('transactions.receipt', $transaction->reference) }}" class="btn btn-primary">
                    <i class="fa-solid fa-print"></i> View / Print Receipt
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
