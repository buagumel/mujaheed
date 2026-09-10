@extends('layouts.app')

@section('title', 'Wallet')

@section('content')
@php
    $virtualAccounts = $user->getOrCreateVirtualAccounts();
    $primaryAccount = $virtualAccounts->first();
@endphp

<style>
.wallet-hero {
    background: var(--palette-background-paper, #FFF);
    border: 1px solid var(--palette-divider, rgba(145, 158, 171, 0.16));
    border-radius: 16px;
    padding: clamp(20px, 4vw, 28px);
    margin-bottom: 24px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
}

.wallet-balance-amount {
    font-size: clamp(2rem, 5vw, 2.5rem);
    font-weight: 800;
    color: var(--palette-text-primary, #1C252E);
    letter-spacing: -0.5px;
    line-height: 1.2;
    margin: 4px 0 16px;
}

.wallet-account-box {
    background: var(--palette-background-neutral, #F4F6F8);
    border: 1px solid rgba(145, 158, 171, 0.12);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.account-num-highlight {
    font-family: monospace;
    font-size: 1.25rem;
    font-weight: 800;
    letter-spacing: 1px;
    color: var(--palette-primary-main, #1877F2);
}

.wallet-quick-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

@media (max-width: 640px) {
    .wallet-hero-top {
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
    }
    .wallet-quick-actions {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .wallet-quick-actions a:first-child {
        grid-column: span 2;
    }
    .wallet-account-box {
        flex-direction: column;
        align-items: flex-start;
    }
    .wallet-account-box button {
        width: 100%;
    }
}
</style>

<div style="max-width: 900px; margin: 0 auto;">

    <!-- MINIMALIST HERO WALLET CARD -->
    <div class="wallet-hero">
        <div class="wallet-hero-top" style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
            <div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:0.8125rem; font-weight:700; color:var(--palette-text-secondary); text-transform:uppercase; letter-spacing:0.5px;">
                        Wallet Balance
                    </span>
                    <span class="badge badge-info" style="font-size:0.75rem; font-weight:700;">
                        {{ $user->getTierDisplayName() }}
                    </span>
                </div>
                <div class="wallet-balance-amount">
                    ₦{{ number_format($wallet->balance, 2) }}
                </div>
            </div>

            <div class="wallet-quick-actions">
                <a href="{{ route('wallet.fund') }}" class="btn btn-primary btn-sm" style="font-weight:700; gap:6px;">
                    <i class="fa-solid fa-plus"></i> Add Money
                </a>
                <a href="{{ route('tier.index') }}" class="btn btn-outline btn-sm" style="font-weight:600; gap:6px;">
                    <i class="fa-solid fa-crown" style="color:#FFB800;"></i> Upgrade
                </a>
                <a href="{{ route('referrals.index') }}" class="btn btn-outline btn-sm" style="font-weight:600; gap:6px;">
                    <i class="fa-solid fa-gift" style="color:#22C55E;"></i> Bonus: ₦{{ number_format($user->referral_balance, 2) }}
                </a>
            </div>
        </div>

        @if($primaryAccount)
            <div class="wallet-account-box">
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary); text-transform:uppercase; margin-bottom:4px;">
                        Automated Funding Bank Account
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <span class="badge badge-success" style="font-size:0.75rem; font-weight:700;">
                            {{ $primaryAccount->bank_name }}
                        </span>
                        <span class="account-num-highlight">
                            {{ $primaryAccount->account_number }}
                        </span>
                        <span style="font-size:0.8125rem; color:var(--palette-text-secondary);">
                            ({{ $primaryAccount->account_name }})
                        </span>
                    </div>
                </div>

                <button type="button" class="btn btn-outline btn-sm" onclick="copyToClipboard('{{ $primaryAccount->account_number }}', this)" style="font-weight:600;">
                    <i class="fa-regular fa-copy"></i> Copy Account
                </button>
            </div>
        @endif
    </div>

    <!-- CLEAN TRANSACTIONS LIST -->
    <div class="mk-card" style="border-radius:16px; padding:clamp(16px, 3vw, 24px);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h4 style="font-size:1rem; font-weight:700; color:var(--palette-text-primary); margin:0;">
                Recent Transactions
            </h4>
            <span style="font-size:0.8125rem; color:var(--palette-text-secondary);">
                {{ $transactions->total() }} total
            </span>
        </div>

        @if($transactions->isEmpty())
            <div style="text-align:center; padding:40px 16px; color:var(--palette-text-secondary);">
                <i class="fa-solid fa-receipt" style="font-size:2rem; color:var(--palette-text-disabled); margin-bottom:10px; display:block;"></i>
                <div style="font-weight:600; font-size:0.9375rem; color:var(--palette-text-primary);">No transactions yet</div>
                <p style="font-size:0.8125rem; margin-top:4px;">Transfer funds to your PalmPay account above to auto-credit your wallet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="mk-table" style="min-width:540px;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th>Type</th>
                            <th style="text-align:right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                            <tr>
                                <td style="font-size:0.8125rem; color:var(--palette-text-secondary); white-space:nowrap;">
                                    {{ $tx->created_at->format('M d, Y') }}
                                </td>
                                <td style="font-weight:600; color:var(--palette-text-primary); font-size:0.875rem;">
                                    {{ $tx->description }}
                                </td>
                                <td style="font-family:monospace; font-size:0.8125rem; color:var(--palette-text-secondary);">
                                    {{ $tx->reference }}
                                </td>
                                <td>
                                    @if($tx->type === 'credit')
                                        <span class="badge badge-success">Credit</span>
                                    @elseif($tx->type === 'refund')
                                        <span class="badge badge-info">Refund</span>
                                    @else
                                        <span class="badge badge-neutral">Debit</span>
                                    @endif
                                </td>
                                <td style="text-align:right; font-weight:800; color:{{ $tx->type === 'credit' || $tx->type === 'refund' ? 'var(--palette-success-main)' : 'var(--palette-text-primary)' }};">
                                    {{ $tx->type === 'credit' || $tx->type === 'refund' ? '+' : '-' }}₦{{ number_format($tx->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div style="margin-top:16px;">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check" style="color:var(--palette-success-main);"></i> Copied';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 2000);
    });
}
</script>
@endsection
