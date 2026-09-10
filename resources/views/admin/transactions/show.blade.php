@extends('layouts.admin')

@section('title', 'Transaction #' . $transaction->reference)

@section('content')
<div style="margin-bottom:20px;">
    <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Back to Transactions
    </a>
</div>

<div class="grid-3">
    <div class="mk-card" style="grid-column: span 2;">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Transaction Details</h3>
                <p class="mk-card-subtitle">Created at {{ $transaction->created_at->format('M d, Y h:i:s A') }}</p>
            </div>
            <div>
                @if($transaction->status === 'successful')
                    <span class="badge badge-success" style="font-size:0.875rem;">Success</span>
                @elseif($transaction->status === 'pending')
                    <span class="badge badge-warning" style="font-size:0.875rem;">Pending</span>
                @elseif($transaction->status === 'reversed')
                    <span class="badge badge-info" style="font-size:0.875rem;">Refunded</span>
                @else
                    <span class="badge badge-error" style="font-size:0.875rem;">Failed</span>
                @endif
            </div>
        </div>

        @if($transaction->token)
            <div style="background:var(--palette-success-lighter); border:1px solid var(--palette-success-dark); border-radius:8px; padding:16px; margin-bottom:20px; text-align:center;">
                <div style="font-size:0.75rem; font-weight:700; color:var(--palette-success-darker);">PREPAID TOKEN</div>
                <div style="font-size:1.5rem; font-weight:800; font-family:monospace; color:var(--palette-success-darker);">{{ $transaction->token }}</div>
                @if($transaction->units)
                    <div style="font-size:0.8125rem; color:var(--palette-success-dark);">Units: {{ $transaction->units }}</div>
                @endif
            </div>
        @endif

        <div style="display:flex; flex-direction:column; gap:14px; font-size:0.875rem;">
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(145, 158, 171, 0.12); padding-bottom:10px;">
                <span style="color:var(--palette-text-secondary);">Reference:</span>
                <span style="font-family:monospace; font-weight:700;">{{ $transaction->reference }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(145, 158, 171, 0.12); padding-bottom:10px;">
                <span style="color:var(--palette-text-secondary);">Provider Reference:</span>
                <span style="font-family:monospace;">{{ $transaction->provider_reference ?? 'N/A' }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(145, 158, 171, 0.12); padding-bottom:10px;">
                <span style="color:var(--palette-text-secondary);">Customer:</span>
                <span style="font-weight:600;">{{ $transaction->user->name ?? 'N/A' }} ({{ $transaction->user->email ?? 'N/A' }})</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(145, 158, 171, 0.12); padding-bottom:10px;">
                <span style="color:var(--palette-text-secondary);">Service & Provider:</span>
                <span style="font-weight:600; text-transform:capitalize;">{{ $transaction->service_type }} ({{ $transaction->provider }})</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(145, 158, 171, 0.12); padding-bottom:10px;">
                <span style="color:var(--palette-text-secondary);">Recipient:</span>
                <span style="font-weight:700;">{{ $transaction->recipient }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(145, 158, 171, 0.12); padding-bottom:10px;">
                <span style="color:var(--palette-text-secondary);">Amount & Fee:</span>
                <span style="font-weight:800; color:var(--palette-primary-main);">₦{{ number_format($transaction->amount, 2) }}</span>
            </div>
            @if($transaction->error_message)
                <div style="border-bottom:1px solid rgba(145, 158, 171, 0.12); padding-bottom:10px;">
                    <span style="color:var(--palette-error-main); font-weight:700;">Error Message:</span>
                    <div style="color:var(--palette-text-secondary); margin-top:4px;">{{ $transaction->error_message }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions Box -->
    <div class="mk-card">
        <h3 class="mk-card-title" style="margin-bottom:16px;">Administrative Actions</h3>

        <!-- Status Re-check -->
        <form method="POST" action="{{ route('admin.transactions.retry_status', $transaction->id) }}" style="margin-bottom:20px;">
            @csrf
            <button type="submit" class="btn btn-outline btn-block">
                <i class="fa-solid fa-arrows-rotate"></i> Query Provider Status
            </button>
        </form>

        <!-- Authorized Refund -->
        @if($transaction->status !== 'reversed')
            <div style="border-top:1px solid rgba(145, 158, 171, 0.12); padding-top:16px;">
                <div style="font-weight:700; font-size:0.875rem; color:var(--palette-error-main); margin-bottom:8px;">Authorized Wallet Refund</div>
                <form method="POST" action="{{ route('admin.transactions.refund', $transaction->id) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="reason">Refund Reason</label>
                        <input type="text" id="reason" name="reason" class="form-control" required minlength="5" placeholder="e.g. Service failed delivery">
                    </div>
                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to refund ₦{{ number_format($transaction->amount, 2) }} to this user wallet?')">
                        <i class="fa-solid fa-rotate-left"></i> Process Full Refund
                    </button>
                </form>
            </div>
        @else
            <div class="badge badge-info" style="width:100%; justify-content:center; padding:10px;">
                This transaction has been refunded
            </div>
        @endif
    </div>
</div>
@endsection
