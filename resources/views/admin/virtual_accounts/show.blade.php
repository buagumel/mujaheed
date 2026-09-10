@extends('layouts.admin')

@section('title', 'Virtual Account Details - ' . $virtualAccount->account_number)

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">

    <!-- BACK BUTTON & HEADER -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <a href="{{ route('admin.virtual-accounts.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Back to Accounts
            </a>
            <h2 style="font-size:1.4rem; font-weight:800; color:var(--palette-text-primary); margin:0;">
                Virtual Account: <span style="color:var(--palette-primary-main); font-family:monospace;">{{ $virtualAccount->account_number }}</span>
            </h2>
        </div>
        <div style="display:flex; gap:10px;">
            <form method="POST" action="{{ route('admin.virtual-accounts.regenerate', $virtualAccount->user_id) }}" onsubmit="return confirm('Sync/Regenerate virtual account with Payrant API?');">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-rotate"></i> Re-Sync with Payrant
                </button>
            </form>
        </div>
    </div>

    <!-- ACCOUNT SUMMARY & USER WALLET CARD -->
    <div class="grid-3" style="margin-bottom:24px;">
        <div class="mk-card" style="padding:20px;">
            <div style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary); text-transform:uppercase;">Bank & Account Info</div>
            <div style="font-size:1.4rem; font-weight:800; color:var(--palette-primary-main); font-family:monospace; margin:6px 0;">
                {{ $virtualAccount->account_number }}
            </div>
            <div style="font-size:0.875rem; font-weight:700; color:var(--palette-text-primary);">{{ $virtualAccount->bank_name }}</div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); margin-top:2px;">Name: {{ $virtualAccount->account_name }}</div>
            <div style="margin-top:10px;">
                <span class="badge badge-success">{{ strtoupper($virtualAccount->status) }}</span>
                <span class="badge" style="background:rgba(24, 119, 242, 0.08); color:var(--palette-primary-main); font-weight:700;">{{ strtoupper($virtualAccount->provider) }}</span>
            </div>
        </div>

        <div class="mk-card" style="padding:20px;">
            <div style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary); text-transform:uppercase;">Assigned Customer</div>
            @if($virtualAccount->user)
                <div style="font-size:1.15rem; font-weight:800; color:var(--palette-text-primary); margin:6px 0;">
                    <a href="{{ route('admin.users.show', $virtualAccount->user_id) }}" style="color:inherit; text-decoration:underline;">
                        {{ $virtualAccount->user->name }}
                    </a>
                </div>
                <div style="font-size:0.8125rem; color:var(--palette-text-secondary);">Email: {{ $virtualAccount->user->email }}</div>
                <div style="font-size:0.8125rem; color:var(--palette-text-secondary);">Phone: {{ $virtualAccount->user->phone }}</div>
                <div style="margin-top:10px; font-size:0.75rem; color:var(--palette-text-secondary);">
                    Tier: <span style="font-weight:700; text-transform:uppercase; color:var(--palette-primary-main);">{{ $virtualAccount->user->tier }}</span>
                </div>
            @else
                <div style="color:var(--palette-error-main); margin-top:8px;">User account deleted</div>
            @endif
        </div>

        <div class="mk-card" style="padding:20px;">
            <div style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary); text-transform:uppercase;">Financial Statistics</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--palette-success-main); margin:6px 0;">
                ₦{{ number_format($virtualAccount->total_funded, 2) }}
            </div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary);">Total Volume Funded</div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); margin-top:6px;">
                Current Wallet Balance: <strong>₦{{ number_format($virtualAccount->user->wallet->balance ?? 0, 2) }}</strong>
            </div>
        </div>
    </div>

    <!-- RESOLVE PAYMENT COMPLAINTS / MANUAL RECONCILIATION MODAL/CARD -->
    <div class="mk-card" style="margin-bottom:24px; border-left:4px solid var(--palette-warning-main); padding:20px;">
        <h4 style="font-size:1rem; font-weight:700; color:var(--palette-text-primary); margin:0 0 8px;">
            <i class="fa-solid fa-headset" style="color:var(--palette-warning-main);"></i> Resolve Customer Deposit Complaint (Manual Reconciliation)
        </h4>
        <p style="font-size:0.8125rem; color:var(--palette-text-secondary); margin-bottom:16px;">
            If a customer transferred funds to this account number and experienced a delayed bank network webhook, verify their debit alert and credit their wallet here securely.
        </p>

        <form method="POST" action="{{ route('admin.virtual-accounts.reconcile', $virtualAccount->id) }}" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px; gap:12px; align-items:end;">
            @csrf
            <div>
                <label class="form-label" style="font-size:0.75rem;">Amount (₦)</label>
                <input type="number" name="amount" class="form-control" placeholder="5000" step="0.01" min="50" required>
            </div>
            <div>
                <label class="form-label" style="font-size:0.75rem;">Bank Session ID / Ref</label>
                <input type="text" name="session_id" class="form-control" placeholder="00001625..." required style="font-family:monospace;">
            </div>
            <div>
                <label class="form-label" style="font-size:0.75rem;">Sender Account Name</label>
                <input type="text" name="payer_name" class="form-control" placeholder="CHINEDU EZE" required>
            </div>
            <div>
                <label class="form-label" style="font-size:0.75rem;">Sender Bank</label>
                <input type="text" name="payer_bank" class="form-control" placeholder="GTBank / OPay" required>
            </div>
            <button type="submit" class="btn btn-primary" style="height:44px;">
                <i class="fa-solid fa-check"></i> Credit
            </button>
        </form>
    </div>

    <!-- LIVE PAYRANT API TRANSACTION HISTORY -->
    <div class="mk-card" style="margin-bottom:24px;">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Live Transactions Query (from Payrant API)</h3>
                <p class="mk-card-subtitle">Real-time ledger returned directly by Payrant Gateway for account #{{ $virtualAccount->account_number }}</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Sender Name</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($liveTransactions as $ltx)
                        <tr>
                            <td style="font-family:monospace; font-weight:700;">{{ $ltx['reference'] ?? 'N/A' }}</td>
                            <td style="font-weight:800; color:var(--palette-success-main);">₦{{ number_format($ltx['amount'] ?? 0, 2) }}</td>
                            <td><span class="badge badge-success">{{ strtoupper($ltx['type'] ?? 'CREDIT') }}</span></td>
                            <td>{{ $ltx['sender'] ?? 'Bank Customer' }}</td>
                            <td>{{ $ltx['date'] ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:32px 16px; color:var(--palette-text-secondary);">
                                No transaction records returned by Payrant API for this account yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- RECENT AUTOMATED CREDITS IN VTU PLATFORM -->
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Platform Wallet Funding History</h3>
                <p class="mk-card-subtitle">Automated webhook credits applied to customer's balance</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Transaction Ref</th>
                        <th>Amount Credited</th>
                        <th>Fee</th>
                        <th>Sender / Payer</th>
                        <th>Channel</th>
                        <th>Paid At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentTransactions as $ptx)
                        <tr>
                            <td style="font-family:monospace; font-weight:700; color:var(--palette-primary-main);">
                                {{ $ptx->reference }}
                            </td>
                            <td style="font-weight:800; color:var(--palette-success-main);">
                                ₦{{ number_format($ptx->amount, 2) }}
                            </td>
                            <td>₦{{ number_format($ptx->fee, 2) }}</td>
                            <td>{{ $ptx->metadata['payer_name'] ?? ($ptx->user->name ?? 'Customer') }} ({{ $ptx->metadata['payer_bank'] ?? 'Bank' }})</td>
                            <td><span class="badge" style="background:rgba(0, 167, 111, 0.08); color:var(--palette-success-main); font-weight:700;">{{ strtoupper($ptx->channel ?? 'TRANSFER') }}</span></td>
                            <td>{{ $ptx->paid_at ? $ptx->paid_at->format('M d, Y h:i A') : $ptx->created_at->format('M d, Y h:i A') }}</td>
                            <td><span class="badge badge-success">{{ strtoupper($ptx->status) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:32px 16px; color:var(--palette-text-secondary);">
                                No automated deposit credits recorded for this user yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
