@extends('layouts.admin')

@section('title', 'Exam PINs Management')
@section('header_title', 'Exam Scratch Cards & Pricing')

@section('content')
<!-- Metrics -->
<div class="grid-2" style="margin-bottom:28px;">
    <div class="mk-card" style="display:flex; align-items:center; gap:16px;">
        <div style="width:52px; height:52px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">Total PINs Dispensed</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--palette-text-primary);">{{ number_format($totalPinsSold) }}</div>
        </div>
    </div>

    <div class="mk-card" style="display:flex; align-items:center; gap:16px;">
        <div style="width:52px; height:52px; border-radius:14px; background:var(--palette-success-lighter); color:var(--palette-success-dark); display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
        <div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">Total Sales Volume</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--palette-success-dark);">₦{{ number_format($totalSales, 2) }}</div>
        </div>
    </div>
</div>

<!-- Exam Packages Pricing Config -->
<div class="mk-card" style="margin-bottom:28px;">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Exam Package Pricing by User Tier</h3>
            <p class="mk-card-subtitle">Set selling prices for Smart Earner, Reseller Agent, and VIP Partner</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>Exam Body</th>
                    <th>Package Name</th>
                    <th>Standard Price (₦)</th>
                    <th>Reseller Price (₦)</th>
                    <th>VIP Price (₦)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packages as $pkg)
                    <tr>
                        <td style="font-weight:800; color:var(--palette-primary-main);">{{ $pkg->code }}</td>
                        <td style="font-weight:700;">{{ $pkg->name }}</td>
                        <form method="POST" action="{{ route('admin.exam_pins.update', $pkg->id) }}">
                            @csrf
                            <td>
                                <input type="number" name="standard_price" value="{{ $pkg->standard_price }}" class="form-control form-control-sm" style="width:110px; font-weight:700;" step="10">
                            </td>
                            <td>
                                <input type="number" name="reseller_price" value="{{ $pkg->reseller_price }}" class="form-control form-control-sm" style="width:110px; font-weight:700;" step="10">
                            </td>
                            <td>
                                <input type="number" name="vip_price" value="{{ $pkg->vip_price }}" class="form-control form-control-sm" style="width:110px; font-weight:700;" step="10">
                            </td>
                            <td>
                                <select name="status" class="form-select form-select-sm" style="width:100px;">
                                    <option value="active" {{ $pkg->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $pkg->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-save"></i> Save
                                </button>
                            </td>
                        </form>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Exam Sales Log -->
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Exam PIN Sales Log</h3>
            <p class="mk-card-subtitle">Recent purchases across all examination bodies</p>
        </div>
    </div>

    @if($transactions->isEmpty())
        <div style="text-align:center; padding:28px 16px; color:var(--palette-text-secondary);">
            <p>No exam PIN sales recorded yet.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Exam Body</th>
                        <th>Qty</th>
                        <th>Total Paid</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                        <tr>
                            <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                            <td style="font-family:monospace; font-weight:600;">{{ $tx->reference }}</td>
                            <td>
                                <div style="font-weight:700;">{{ $tx->user->name ?? 'Customer' }}</div>
                                <div style="font-size:0.75rem; color:var(--palette-text-secondary);">{{ $tx->user->email ?? '' }}</div>
                            </td>
                            <td><span class="badge badge-info">{{ $tx->exam_code }}</span></td>
                            <td style="font-weight:700;">{{ $tx->quantity }}</td>
                            <td style="font-weight:700;">₦{{ number_format($tx->total_amount, 2) }}</td>
                            <td><span class="badge badge-success">Delivered</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection
