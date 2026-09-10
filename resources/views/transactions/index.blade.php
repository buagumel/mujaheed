@extends('layouts.app')

@section('title', 'Transactions')
@section('header_title', 'Transaction History')

@section('content')
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">All Transactions</h3>
            <p class="mk-card-subtitle">Filter and search your VTU purchases</p>
        </div>
    </div>

    <!-- Filters Bar -->
    <form method="GET" action="{{ route('transactions.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
        <div style="flex:1; min-width:200px;">
            <input type="text" name="search" class="form-control" placeholder="Search reference, recipient or network..." value="{{ $filters['search'] ?? '' }}">
        </div>

        <div style="width:160px;">
            <select name="service_type" class="form-select" onchange="this.form.submit()">
                <option value="">All Services</option>
                <option value="airtime" {{ ($filters['service_type'] ?? '') === 'airtime' ? 'selected' : '' }}>Airtime</option>
                <option value="data" {{ ($filters['service_type'] ?? '') === 'data' ? 'selected' : '' }}>Data</option>
                <option value="electricity" {{ ($filters['service_type'] ?? '') === 'electricity' ? 'selected' : '' }}>Electricity</option>
                <option value="cable" {{ ($filters['service_type'] ?? '') === 'cable' ? 'selected' : '' }}>Cable TV</option>
            </select>
        </div>

        <div style="width:150px;">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="successful" {{ ($filters['status'] ?? '') === 'successful' ? 'selected' : '' }}>Success</option>
                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ ($filters['status'] ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="reversed" {{ ($filters['status'] ?? '') === 'reversed' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i> Filter
        </button>
        <a href="{{ route('transactions.index') }}" class="btn btn-outline">Reset</a>
    </form>

    @if($transactions->isEmpty())
        <div style="text-align:center; padding: 48px 16px; color:var(--text-secondary);">
            <i class="fa-solid fa-receipt" style="font-size:3rem; color:var(--text-disabled); margin-bottom:12px; display:block;"></i>
            <p style="font-size:1.125rem; font-weight:600; margin-bottom:4px;">No Transactions Found</p>
            <p style="font-size:0.875rem;">Try adjusting your filters or search terms.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
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
                            <td style="font-size:0.8125rem; color:var(--text-secondary);">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                            <td style="font-family:monospace; font-weight:600;">{{ $tx->reference }}</td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($tx->service_type) }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600;">{{ $tx->provider }}</div>
                                <div style="font-size:0.75rem; color:var(--text-secondary);">{{ $tx->recipient }}</div>
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
                                <div style="display:flex; gap:6px;">
                                    <a href="{{ route('transactions.show', $tx->reference) }}" class="btn btn-outline btn-sm">Details</a>
                                    @if($tx->status === 'successful')
                                        <a href="{{ route('transactions.receipt', $tx->reference) }}" class="btn btn-soft-primary btn-sm">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px;">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection
