@extends('layouts.admin')

@section('title', 'Dedicated Virtual Accounts Management')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <!-- TOP HEADER & STATS -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <h2 style="font-size:1.5rem; font-weight:800; color:var(--palette-text-primary); margin:0;">
                <i class="fa-solid fa-building-columns" style="color:var(--palette-primary-main);"></i> Dedicated Virtual Accounts
            </h2>
            <p style="font-size:0.875rem; color:var(--palette-text-secondary); margin:4px 0 0;">
                Manage customer PalmPay & Payrant virtual bank accounts and resolve deposit complaints with automated reconciliation
            </p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline">
                <i class="fa-solid fa-gear"></i> Gateway Settings
            </a>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="grid-4" style="margin-bottom:24px;">
        <div class="mk-card" style="padding:18px;">
            <div style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary); text-transform:uppercase;">Total Accounts</div>
            <div style="font-size:1.75rem; font-weight:800; color:var(--palette-primary-main); margin:4px 0;">{{ number_format($totalAccounts) }}</div>
            <div style="font-size:0.75rem; color:var(--palette-success-main);"><i class="fa-solid fa-circle-check"></i> {{ number_format($activeAccounts) }} Active</div>
        </div>

        <div class="mk-card" style="padding:18px;">
            <div style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary); text-transform:uppercase;">Total Funded Volume</div>
            <div style="font-size:1.75rem; font-weight:800; color:var(--palette-success-main); margin:4px 0;">₦{{ number_format($totalFundedVolume, 2) }}</div>
            <div style="font-size:0.75rem; color:var(--palette-text-secondary);">Lifetime Automated Credits</div>
        </div>

        <div class="mk-card" style="padding:18px;">
            <div style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary); text-transform:uppercase;">Today's Inflow</div>
            <div style="font-size:1.75rem; font-weight:800; color:var(--palette-info-main); margin:4px 0;">₦{{ number_format($recentFundings, 2) }}</div>
            <div style="font-size:0.75rem; color:var(--palette-text-secondary);">Received Today</div>
        </div>

        <div class="mk-card" style="padding:18px;">
            <div style="font-size:0.75rem; font-weight:700; color:var(--palette-text-secondary); text-transform:uppercase;">Active Gateway</div>
            <div style="font-size:1.25rem; font-weight:800; color:var(--palette-text-primary); margin:6px 0;">PalmPay / Payrant</div>
            <div style="font-size:0.75rem; color:var(--palette-success-main);"><i class="fa-solid fa-bolt"></i> Webhook Active</div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="mk-card" style="margin-bottom:20px; padding:16px;">
        <form method="GET" action="{{ route('admin.virtual-accounts.index') }}" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
            <div style="flex:1; min-width:260px;">
                <input type="text" name="search" class="form-control" placeholder="Search by account number, customer name, email, or reference..." value="{{ request('search') }}">
            </div>
            <div style="width:160px;">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'provider']))
                <a href="{{ route('admin.virtual-accounts.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <!-- VIRTUAL ACCOUNTS TABLE -->
    <div class="mk-card">
        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Customer / User</th>
                        <th>Virtual Bank</th>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Total Funded</th>
                        <th>Last Funded</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $acc)
                        <tr>
                            <td>
                                @if($acc->user)
                                    <div style="font-weight:700; color:var(--palette-text-primary);">
                                        <a href="{{ route('admin.users.show', $acc->user_id) }}" style="color:inherit; text-decoration:underline;">
                                            {{ $acc->user->name }}
                                        </a>
                                    </div>
                                    <div style="font-size:0.75rem; color:var(--palette-text-secondary);">{{ $acc->user->email }} • {{ $acc->user->phone }}</div>
                                @else
                                    <span style="color:var(--palette-error-main);">Orphaned User #{{ $acc->user_id }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background:rgba(24, 119, 242, 0.08); color:var(--palette-primary-main); font-weight:700;">
                                    <i class="fa-solid fa-building-columns"></i> {{ $acc->bank_name }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span style="font-family:monospace; font-weight:800; font-size:1.05rem; color:var(--palette-primary-main);">
                                        {{ $acc->account_number }}
                                    </span>
                                    <button type="button" class="btn btn-icon btn-sm" onclick="navigator.clipboard.writeText('{{ $acc->account_number }}'); alert('Copied {{ $acc->account_number }}!');" title="Copy">
                                        <i class="fa-solid fa-copy" style="font-size:0.8rem;"></i>
                                    </button>
                                </div>
                                <div style="font-size:0.7rem; color:var(--palette-text-secondary); font-family:monospace;">Ref: {{ $acc->reference }}</div>
                            </td>
                            <td>
                                <div style="font-size:0.8125rem; font-weight:600;">{{ $acc->account_name }}</div>
                            </td>
                            <td>
                                <div style="font-weight:800; color:var(--palette-success-main);">₦{{ number_format($acc->total_funded ?? 0, 2) }}</div>
                            </td>
                            <td>
                                @if($acc->last_funded_at)
                                    <div style="font-size:0.75rem; color:var(--palette-text-primary);">{{ $acc->last_funded_at->format('M d, Y') }}</div>
                                    <div style="font-size:0.7rem; color:var(--palette-text-secondary);">{{ $acc->last_funded_at->format('h:i A') }}</div>
                                @else
                                    <span style="font-size:0.75rem; color:var(--palette-text-disabled);">Never</span>
                                @endif
                            </td>
                            <td>
                                @if($acc->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-error">Inactive</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <a href="{{ route('admin.virtual-accounts.show', $acc->id) }}" class="btn btn-outline btn-sm" title="View details & Query Live Payrant Transactions">
                                        <i class="fa-solid fa-eye"></i> Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:48px 20px;">
                                <i class="fa-solid fa-building-columns" style="font-size:2.5rem; color:var(--palette-text-disabled); margin-bottom:12px;"></i>
                                <div style="font-weight:700; font-size:1.1rem; color:var(--palette-text-primary);">No Virtual Accounts Found</div>
                                <div style="font-size:0.85rem; color:var(--palette-text-secondary); margin-top:4px;">
                                    Virtual accounts are automatically generated when users register or visit their wallet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($accounts->hasPages())
            <div style="padding:16px; border-top:1px solid rgba(145, 158, 171, 0.16);">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
