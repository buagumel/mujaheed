@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div style="margin-bottom:28px;">
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary);">Platform Overview</h2>
    <p style="font-size:0.875rem; color:var(--text-secondary);">Real-time monitoring of users, revenue, transactions and wallet ledger</p>
</div>

<!-- Material Kit Glass Stat Cards -->
<div class="grid-4" style="margin-bottom:28px;">
    <div class="mk-card" style="background: linear-gradient(135deg, rgba(208, 236, 254, 0.48) 0%, rgba(115, 186, 251, 0.24) 100%); border: 1px solid rgba(115, 186, 251, 0.32);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <div style="width:44px; height:44px; border-radius:12px; background:#1877F2; color:#FFF; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <span class="badge badge-success">Today</span>
        </div>
        <div style="font-size:1.75rem; font-weight:800; color:#042174;">₦{{ number_format($todayRevenue, 2) }}</div>
        <div style="font-size:0.8125rem; font-weight:600; color:#0C44AE; margin-top:2px;">Today's Revenue</div>
    </div>

    <div class="mk-card" style="background: linear-gradient(135deg, rgba(211, 252, 210, 0.48) 0%, rgba(119, 237, 139, 0.24) 100%); border: 1px solid rgba(119, 237, 139, 0.32);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <div style="width:44px; height:44px; border-radius:12px; background:#22C55E; color:#FFF; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <span class="badge badge-success">Ledger</span>
        </div>
        <div style="font-size:1.75rem; font-weight:800; color:#065E49;">₦{{ number_format($totalWalletBalance, 2) }}</div>
        <div style="font-size:0.8125rem; font-weight:600; color:#118D57; margin-top:2px;">Total User Wallets</div>
    </div>

    <div class="mk-card" style="background: linear-gradient(135deg, rgba(255, 245, 204, 0.48) 0%, rgba(255, 214, 102, 0.24) 100%); border: 1px solid rgba(255, 214, 102, 0.32);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <div style="width:44px; height:44px; border-radius:12px; background:#FFAB00; color:#1C252E; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                <i class="fa-solid fa-users"></i>
            </div>
            <span class="badge badge-warning">{{ $activeUsers }} Active</span>
        </div>
        <div style="font-size:1.75rem; font-weight:800; color:#7A4100;">{{ $totalUsers }}</div>
        <div style="font-size:0.8125rem; font-weight:600; color:#B76E00; margin-top:2px;">Total Customers</div>
    </div>

    <div class="mk-card" style="background: linear-gradient(135deg, rgba(239, 214, 255, 0.48) 0%, rgba(198, 132, 255, 0.24) 100%); border: 1px solid rgba(198, 132, 255, 0.32);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <div style="width:44px; height:44px; border-radius:12px; background:#8E33FF; color:#FFF; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <span class="badge badge-info">{{ $successfulTransactions }} Success</span>
        </div>
        <div style="font-size:1.75rem; font-weight:800; color:#27097A;">{{ $todayTransactions }}</div>
        <div style="font-size:0.8125rem; font-weight:600; color:#5119B7; margin-top:2px;">Today's Transactions</div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid-2" style="margin-bottom:28px;">
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Revenue Statistics</h3>
                <p class="mk-card-subtitle">Monthly transaction turnover (₦)</p>
            </div>
        </div>
        <div style="height:260px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Service Distribution</h3>
                <p class="mk-card-subtitle">Breakdown by airtime, data, electricity, cable</p>
            </div>
        </div>
        <div style="height:260px; display:flex; align-items:center; justify-content:center;">
            <canvas id="serviceChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Transactions & Recent Users -->
<div class="grid-2">
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Recent Transactions</h3>
                <p class="mk-card-subtitle">Latest platform activity</p>
            </div>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline btn-sm">View All</a>
        </div>

        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $tx)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $tx->user->name ?? 'Guest' }}</div>
                                <div style="font-size:0.75rem; color:var(--text-secondary); font-family:monospace;">{{ $tx->reference }}</div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($tx->service_type) }}</span>
                            </td>
                            <td style="font-weight:700;">₦{{ number_format($tx->amount, 2) }}</td>
                            <td>
                                @if($tx->status === 'successful')
                                    <span class="badge badge-success">Success</span>
                                @elseif($tx->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-error">{{ $tx->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Newly Registered Users</h3>
                <p class="mk-card-subtitle">Latest customer registrations</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">View All</a>
        </div>

        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Name & Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $u)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $u->name }}</div>
                                <div style="font-size:0.75rem; color:var(--text-secondary);">{{ $u->email }}</div>
                            </td>
                            <td>{{ $u->phone }}</td>
                            <td>
                                <span class="badge {{ $u->status === 'active' ? 'badge-success' : 'badge-error' }}">{{ $u->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-outline btn-sm">Manage</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Revenue Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($monthlyRevenue)) !!},
            datasets: [{
                label: 'Revenue (₦)',
                data: {!! json_encode(array_values($monthlyRevenue)) !!},
                backgroundColor: '#1877F2',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // Service Distribution Chart
    const srvCtx = document.getElementById('serviceChart').getContext('2d');
    new Chart(srvCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($serviceDistribution)) !!},
            datasets: [{
                data: {!! json_encode(array_values($serviceDistribution)) !!},
                backgroundColor: ['#1877F2', '#22C55E', '#FFAB00', '#8E33FF'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endpush
@endsection
