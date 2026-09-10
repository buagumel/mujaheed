@extends('layouts.admin')

@section('title', 'Wallets Management')

@section('content')
<div class="mk-card" style="margin-bottom:28px;">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">User Wallets & Adjustments</h3>
            <p class="mk-card-subtitle">Perform authorized manual balance credits or debits with audit reasoning</p>
        </div>
        <div style="text-align:right;">
            <div style="font-size:0.75rem; color:var(--palette-text-secondary); font-weight:700;">TOTAL SYSTEM BALANCE</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--palette-primary-main);">₦{{ number_format($totalBalance, 2) }}</div>
        </div>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.wallets.index') }}" style="margin-bottom:20px;">
        <div style="display:flex; gap:12px;">
            <input type="text" name="search" class="form-control" placeholder="Search user by name, email or phone..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('admin.wallets.index') }}" class="btn btn-outline">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Current Balance</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Manual Adjustment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wallets as $w)
                    <tr>
                        <td style="font-weight:700;">{{ $w->user->name ?? 'Deleted User' }}</td>
                        <td style="color:var(--palette-text-secondary);">{{ $w->user->email ?? 'N/A' }}</td>
                        <td style="font-size:1.125rem; font-weight:800; color:var(--palette-text-primary);">₦{{ number_format($w->balance, 2) }}</td>
                        <td>
                            <span class="badge {{ $w->status === 'active' ? 'badge-success' : 'badge-error' }}">{{ ucfirst($w->status) }}</span>
                        </td>
                        <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">{{ $w->updated_at->diffForHumans() }}</td>
                        <td>
                            <button type="button" class="btn btn-outline btn-sm" onclick="openAdjustModal({{ $w->id }}, '{{ $w->user->name ?? 'User' }}', '{{ number_format($w->balance, 2) }}')">
                                <i class="fa-solid fa-sliders"></i> Adjust Balance
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $wallets->links() }}
    </div>
</div>

<!-- Balance Adjustment Modal -->
<div id="adjustModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center;">
    <div class="mk-card" style="width:100%; max-width:500px; margin:20px;">
        <div class="mk-card-header">
            <h3 class="mk-card-title">Manual Balance Adjustment</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeAdjustModal()">&times;</button>
        </div>

        <form method="POST" id="adjustForm" action="">
            @csrf
            <div style="margin-bottom:16px; font-size:0.875rem;">
                Adjusting wallet for: <strong id="modalUserName"></strong> (Balance: ₦<span id="modalUserBalance"></span>)
            </div>

            <div class="form-group">
                <label class="form-label">Action Type</label>
                <div style="display:flex; gap:16px;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="radio" name="action_type" value="credit" checked>
                        <span class="badge badge-success">Credit (Add Funds)</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="radio" name="action_type" value="debit">
                        <span class="badge badge-error">Debit (Deduct Funds)</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="adjAmount">Amount (NGN)</label>
                <input type="number" id="adjAmount" name="amount" class="form-control" min="1" step="0.01" required placeholder="1000">
            </div>

            <div class="form-group">
                <label class="form-label" for="adjReason">Adjustment Reason (Required for Audit Trail)</label>
                <input type="text" id="adjReason" name="reason" class="form-control" required minlength="5" placeholder="e.g. Manual bank deposit resolution">
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeAdjustModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Apply Adjustment</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAdjustModal(walletId, userName, balance) {
        document.getElementById('modalUserName').innerText = userName;
        document.getElementById('modalUserBalance').innerText = balance;
        document.getElementById('adjustForm').action = `/admin/wallets/${walletId}/adjust`;
        const modal = document.getElementById('adjustModal');
        modal.style.display = 'flex';
    }

    function closeAdjustModal() {
        document.getElementById('adjustModal').style.display = 'none';
    }
</script>
@endsection
