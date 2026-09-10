@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div style="margin-bottom:20px;">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Back to Users
    </a>
</div>

<div class="grid-3" style="margin-bottom:28px;">
    <!-- Profile Card -->
    <div class="mk-card">
        <div style="text-align:center; padding:12px 0 20px; border-bottom:1px solid var(--border-color); margin-bottom:20px;">
            <div style="width:64px; height:64px; border-radius:50%; background:linear-gradient(135deg, #1877F2 0%, #0C44AE 100%); color:#FFF; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:800; margin:0 auto 12px;">
                {{ substr($user->name, 0, 1) }}
            </div>
            <h3 style="font-size:1.125rem; font-weight:800;">{{ $user->name }}</h3>
            <p style="font-size:0.8125rem; color:var(--text-secondary);">{{ $user->email }}</p>
            <div style="margin-top:8px;">
                <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-error' }}">{{ ucfirst($user->status) }}</span>
                <span class="badge badge-neutral">{{ ucfirst($user->role) }}</span>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px; font-size:0.875rem;">
            <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-secondary);">Phone:</span>
                <span style="font-weight:600;">{{ $user->phone ?? 'N/A' }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-secondary);">PIN Status:</span>
                <span style="font-weight:600;">{{ $user->hasTransactionPin() ? 'Configured' : 'Not Set' }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-secondary);">Joined:</span>
                <span style="font-weight:600;">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Status Management & Wallet Summary -->
    <div class="mk-card" style="grid-column: span 2;">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Account Status & Wallet</h3>
                <p class="mk-card-subtitle">Manage user permissions and view balance</p>
            </div>
            <span class="badge badge-info" style="font-size:1rem; padding:6px 14px;">Wallet: ₦{{ number_format($wallet->balance, 2) }}</span>
        </div>

        <form method="POST" action="{{ route('admin.users.status', $user->id) }}" style="background:var(--bg-neutral); padding:20px; border-radius:var(--border-radius-sm); margin-bottom:24px;">
            @csrf
            <div class="form-group">
                <label class="form-label">Update Account Status</label>
                <div style="display:flex; gap:16px;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="radio" name="status" value="active" {{ $user->status === 'active' ? 'checked' : '' }}>
                        <span class="badge badge-success">Active</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="radio" name="status" value="suspended" {{ $user->status === 'suspended' ? 'checked' : '' }}>
                        <span class="badge badge-warning">Suspended</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="radio" name="status" value="blocked" {{ $user->status === 'blocked' ? 'checked' : '' }}>
                        <span class="badge badge-error">Blocked</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="reason">Reason for Status Change</label>
                <input type="text" id="reason" name="reason" class="form-control" placeholder="e.g. Routine security check">
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Save Status</button>
        </form>

        <!-- Direct Wallet Balance Adjustment -->
        <div style="border-top:1px solid var(--border-color); padding-top:20px; margin-bottom:20px;">
            <h4 style="font-size:0.9375rem; font-weight:700; color:var(--palette-primary-main); margin-bottom:12px;">
                <i class="fa-solid fa-sliders"></i> Manual Wallet Adjustment (Credit / Debit)
            </h4>
            <form method="POST" action="{{ route('admin.wallets.adjust', $wallet->id) }}" style="background:rgba(24, 119, 242, 0.04); padding:16px; border-radius:var(--border-radius-sm); border:1px solid rgba(24, 119, 242, 0.16);">
                @csrf
                <div class="grid-3" style="gap:12px; margin-bottom:12px;">
                    <div>
                        <label class="form-label">Action</label>
                        <select name="action_type" class="form-select" required>
                            <option value="credit">Credit (Add Funds)</option>
                            <option value="debit">Debit (Deduct Funds)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Amount (₦)</label>
                        <input type="number" name="amount" class="form-control" min="1" step="0.01" required placeholder="500">
                    </div>
                    <div>
                        <label class="form-label">Reason</label>
                        <input type="text" name="reason" class="form-control" required minlength="5" placeholder="e.g. Bank transfer top-up">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-check"></i> Apply Wallet Adjustment
                </button>
            </form>
        </div>

        <!-- Admin Direct Reset User Password -->
        <div style="border-top:1px solid var(--border-color); padding-top:20px;">
            <h4 style="font-size:0.9375rem; font-weight:700; color:#dc2626; margin-bottom:12px;">
                <i class="fa-solid fa-key"></i> Reset Customer Password (Admin Direct Override)
            </h4>
            <form method="POST" action="{{ route('admin.users.reset_password', $user->id) }}" style="background:rgba(239, 68, 68, 0.04); padding:16px; border-radius:var(--border-radius-sm); border:1px solid rgba(239, 68, 68, 0.16);">
                @csrf
                <div class="grid-2" style="gap:12px; margin-bottom:12px;">
                    <div>
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Min 8 characters">
                    </div>
                    <div>
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                    </div>
                </div>
                <button type="submit" class="btn btn-danger btn-sm" style="background:#dc2626; border-color:#dc2626; color:#fff; font-weight:700;">
                    <i class="fa-solid fa-lock"></i> Reset User Password
                </button>
            </form>
        </div>
    </div>
</div>

<!-- User Recent Transactions -->
<div class="mk-card">
    <div class="mk-card-header">
        <h3 class="mk-card-title">Recent User Purchases</h3>
    </div>

    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Service</th>
                    <th>Provider / Recipient</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td style="font-size:0.8125rem; color:var(--text-secondary);">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                        <td style="font-family:monospace; font-weight:600;">{{ $tx->reference }}</td>
                        <td><span class="badge badge-info">{{ ucfirst($tx->service_type) }}</span></td>
                        <td>{{ $tx->provider }} ({{ $tx->recipient }})</td>
                        <td style="font-weight:700;">₦{{ number_format($tx->amount, 2) }}</td>
                        <td><span class="badge {{ $tx->status === 'successful' ? 'badge-success' : 'badge-error' }}">{{ $tx->status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--text-secondary); padding:24px;">No purchase activity yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
