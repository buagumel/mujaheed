@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">User Accounts</h3>
            <p class="mk-card-subtitle">Manage customer accounts, wallets, and access statuses</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
        <div style="flex:1; min-width:200px;">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email or phone..." value="{{ request('search') }}">
        </div>

        <div style="width:140px;">
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div style="width:140px;">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Wallet Balance</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:34px; height:34px; border-radius:50%; background:var(--color-primary); color:#FFF; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8125rem;">
                                    {{ substr($u->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight:700;">{{ $u->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-secondary);">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $u->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $u->role === 'admin' ? 'badge-error' : 'badge-neutral' }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td>
                            @if($u->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($u->status === 'suspended')
                                <span class="badge badge-warning">Suspended</span>
                            @else
                                <span class="badge badge-error">Blocked</span>
                            @endif
                        </td>
                        <td style="font-weight:700;">₦{{ number_format($u->wallet->balance ?? 0, 2) }}</td>
                        <td style="font-size:0.8125rem; color:var(--text-secondary);">{{ $u->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-outline btn-sm">
                                View Profile
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $users->links() }}
    </div>
</div>
@endsection
