@extends('layouts.admin')

@section('title', 'Financial Profit Analytics')

@section('content')
<style>
    .pricing-container {
        max-width: 1100px;
        margin: 0 auto;
        padding-bottom: 40px;
    }
    .pricing-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 24px;
    }
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    .kpi-card {
        padding: 20px;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    @media (max-width: 992px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .pricing-container { padding: 0 12px 40px; }
        .pricing-header { flex-direction: column; align-items: flex-start; }
        .kpi-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="pricing-container">
    <div class="pricing-header">
        <div>
            <h2 style="font-size:1.5rem; font-weight:800; color:var(--palette-text-primary);">Financial Profit & Margin Analytics</h2>
            <p style="font-size:0.875rem; color:var(--palette-text-secondary);">Real-time revenue accounting, wholesale provider costs, and net margin reports.</p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <div style="display:inline-flex; border:1px solid rgba(145, 158, 171, 0.24); border-radius:10px; overflow:hidden; background:#fff;">
                <a href="{{ route('admin.pricing.index', ['period' => 'all']) }}" class="btn btn-sm {{ ($period ?? 'all') === 'all' ? 'btn-primary' : 'btn-outline' }}" style="border-radius:0; border:none; padding:8px 14px;">All Time</a>
                <a href="{{ route('admin.pricing.index', ['period' => 'month']) }}" class="btn btn-sm {{ ($period ?? '') === 'month' ? 'btn-primary' : 'btn-outline' }}" style="border-radius:0; border:none; padding:8px 14px;">This Month</a>
                <a href="{{ route('admin.pricing.index', ['period' => 'week']) }}" class="btn btn-sm {{ ($period ?? '') === 'week' ? 'btn-primary' : 'btn-outline' }}" style="border-radius:0; border:none; padding:8px 14px;">This Week</a>
                <a href="{{ route('admin.pricing.index', ['period' => 'today']) }}" class="btn btn-sm {{ ($period ?? '') === 'today' ? 'btn-primary' : 'btn-outline' }}" style="border-radius:0; border:none; padding:8px 14px;">Today</a>
            </div>
            <a href="{{ route('admin.pricing.export_profit') }}" class="btn btn-outline btn-sm" style="padding:8px 14px; font-weight:700;">
                <i class="fa-solid fa-file-csv" style="color:#15803d; margin-right:4px;"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Profit KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card" style="background: linear-gradient(135deg, rgba(208, 236, 254, 0.5) 0%, rgba(115, 186, 251, 0.25) 100%); border: 1px solid rgba(115, 186, 251, 0.32);">
            <div style="font-size:0.75rem; font-weight:800; color:#0C44AE; text-transform:uppercase;">Gross Sales Revenue</div>
            <div style="font-size:1.6rem; font-weight:800; color:#042174; margin-top:6px;">₦{{ number_format($totalSales, 2) }}</div>
            <div style="font-size:0.75rem; color:var(--palette-text-secondary); margin-top:2px;">Customer payments</div>
        </div>

        <div class="kpi-card" style="background: linear-gradient(135deg, rgba(255, 233, 213, 0.5) 0%, rgba(255, 172, 130, 0.25) 100%); border: 1px solid rgba(255, 172, 130, 0.32);">
            <div style="font-size:0.75rem; font-weight:800; color:#B71D18; text-transform:uppercase;">API Provider Cost</div>
            <div style="font-size:1.6rem; font-weight:800; color:#7A0916; margin-top:6px;">₦{{ number_format($totalCost, 2) }}</div>
            <div style="font-size:0.75rem; color:var(--palette-text-secondary); margin-top:2px;">Wholesale costs</div>
        </div>

        <div class="kpi-card" style="background: linear-gradient(135deg, rgba(211, 252, 210, 0.5) 0%, rgba(119, 237, 139, 0.25) 100%); border: 1px solid rgba(119, 237, 139, 0.32);">
            <div style="font-size:0.75rem; font-weight:800; color:#118D57; text-transform:uppercase;">Net Profit</div>
            <div style="font-size:1.6rem; font-weight:800; color:#065E49; margin-top:6px;">₦{{ number_format($netProfit, 2) }}</div>
            <div style="font-size:0.75rem; color:var(--palette-text-secondary); margin-top:2px;">Platform earnings</div>
        </div>

        <div class="kpi-card" style="background: linear-gradient(135deg, rgba(239, 214, 255, 0.5) 0%, rgba(198, 132, 255, 0.25) 100%); border: 1px solid rgba(198, 132, 255, 0.32);">
            <div style="font-size:0.75rem; font-weight:800; color:#5119B7; text-transform:uppercase;">Margin</div>
            <div style="font-size:1.6rem; font-weight:800; color:#27097A; margin-top:6px;">{{ $profitMargin }}%</div>
            <div style="font-size:0.75rem; color:var(--palette-text-secondary); margin-top:2px;">Gross margin ratio</div>
        </div>
    </div>

    <!-- Breakdown Table -->
    <div style="background:#fff; border-radius:14px; border:1px solid rgba(145, 158, 171, 0.16); padding:20px; margin-bottom:24px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <h3 style="font-size:1rem; font-weight:800; margin-bottom:14px; color:var(--palette-text-primary);">Profit Breakdown by Service Category</h3>
        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Tx Count</th>
                        <th>Gross Sales</th>
                        <th>Provider Cost</th>
                        <th>Net Profit</th>
                        <th>Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profitByService as $srvKey => $srvData)
                        <tr>
                            <td style="font-weight:700; text-transform:uppercase;">
                                @if($srvKey === 'data') <i class="fa-solid fa-wifi" style="margin-right:6px; color:#1877F2;"></i> Data Bundles
                                @elseif($srvKey === 'airtime') <i class="fa-solid fa-phone" style="margin-right:6px; color:#22C55E;"></i> Airtime VTU
                                @elseif($srvKey === 'electricity') <i class="fa-solid fa-lightbulb" style="margin-right:6px; color:#FFAB00;"></i> Electricity Bills
                                @elseif($srvKey === 'cable') <i class="fa-solid fa-tv" style="margin-right:6px; color:#8E33FF;"></i> Cable TV
                                @elseif($srvKey === 'exam_pins') <i class="fa-solid fa-graduation-cap" style="margin-right:6px; color:#00B8D9;"></i> Exam PINs
                                @elseif($srvKey === 'airtime_cash') <i class="fa-solid fa-arrow-right-arrow-left" style="margin-right:6px; color:#FF5630;"></i> Airtime to Cash
                                @else {{ $srvKey }}
                                @endif
                            </td>
                            <td style="font-weight:600;">{{ number_format($srvData['count']) }}</td>
                            <td style="font-weight:700;">₦{{ number_format($srvData['sales'], 2) }}</td>
                            <td style="color:var(--palette-text-secondary);">₦{{ number_format($srvData['cost'], 2) }}</td>
                            <td style="font-weight:700; color:#15803d;">₦{{ number_format($srvData['profit'], 2) }}</td>
                            <td>
                                <span class="badge {{ $srvData['margin'] >= 5 ? 'badge-success' : 'badge-warning' }}" style="font-weight:700;">
                                    {{ $srvData['margin'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
