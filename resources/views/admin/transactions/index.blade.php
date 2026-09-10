@extends('layouts.admin')

@section('title', 'All Transactions')

@section('content')
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">All VTU Transactions</h3>
            <p class="mk-card-subtitle">Real-time purchase monitoring across all users and services</p>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.transactions.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
        <div style="flex:1; min-width:200px;">
            <input type="text" name="search" class="form-control" placeholder="Search reference, phone, user..." value="{{ request('search') }}">
        </div>

        <div style="width:140px;">
            <select name="service_type" class="form-select" onchange="this.form.submit()">
                <option value="">All Services</option>
                <option value="airtime" {{ request('service_type') === 'airtime' ? 'selected' : '' }}>Airtime</option>
                <option value="data" {{ request('service_type') === 'data' ? 'selected' : '' }}>Data</option>
                <option value="electricity" {{ request('service_type') === 'electricity' ? 'selected' : '' }}>Electricity</option>
                <option value="cable" {{ request('service_type') === 'cable' ? 'selected' : '' }}>Cable TV</option>
            </select>
        </div>

        <div style="width:140px;">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="successful" {{ request('status') === 'successful' ? 'selected' : '' }}>Success</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="reversed" {{ request('status') === 'reversed' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Reference</th>
                    <th>Service</th>
                    <th>Provider / Recipient</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                    <tr>
                        <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <div style="font-weight:700;">{{ $tx->user->name ?? 'Deleted' }}</div>
                            <div style="font-size:0.75rem; color:var(--palette-text-secondary);">{{ $tx->user->email ?? 'N/A' }}</div>
                        </td>
                        <td style="font-family:monospace; font-weight:600;">{{ $tx->reference }}</td>
                        <td><span class="badge badge-info">{{ ucfirst($tx->service_type) }}</span></td>
                        <td>
                            <div style="font-weight:600;">{{ $tx->provider }}</div>
                            <div style="font-size:0.75rem; color:var(--palette-text-secondary);">{{ $tx->recipient }}</div>
                        </td>
                        <td style="font-weight:700;">₦{{ number_format($tx->amount, 2) }}</td>
                        <td>
                            @if($tx->status === 'successful')
                                <span class="badge badge-success">Success</span>
                            @elseif($tx->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($tx->status === 'reversed')
                                <span class="badge badge-info">Refunded</span>
                            @else
                                <span class="badge badge-error">Failed</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.transactions.show', $tx->id) }}" class="btn btn-outline btn-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
