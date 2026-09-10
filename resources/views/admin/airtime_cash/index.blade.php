@extends('layouts.admin')

@section('title', 'Airtime to Cash Management')
@section('header_title', 'Airtime to Cash Requests')

@section('content')
<div class="grid-3" style="margin-bottom:28px;">
    <div class="mk-card" style="display:flex; align-items:center; gap:16px;">
        <div style="width:52px; height:52px; border-radius:14px; background:var(--palette-warning-lighter); color:var(--palette-warning-dark); display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">Pending Verification</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--palette-warning-dark);">{{ $pendingCount }}</div>
        </div>
    </div>

    <div class="mk-card" style="display:flex; align-items:center; gap:16px;">
        <div style="width:52px; height:52px; border-radius:14px; background:var(--palette-success-lighter); color:var(--palette-success-dark); display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">Total Approved</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--palette-success-dark);">{{ $approvedCount }}</div>
        </div>
    </div>

    <div class="mk-card" style="display:flex; align-items:center; justify-content:center;">
        <button type="button" class="btn btn-outline btn-block" onclick="document.getElementById('ratesBox').style.display = document.getElementById('ratesBox').style.display === 'none' ? 'block' : 'none'">
            <i class="fa-solid fa-gear"></i> Configure Payout Rates & SIMs
        </button>
    </div>
</div>

<!-- Rates & Receiver SIM Settings Form -->
<div id="ratesBox" class="mk-card" style="display:none; margin-bottom:28px; padding:24px; border:1px solid rgba(24, 119, 242, 0.2); background:rgba(24, 119, 242, 0.02);">
    <div style="margin-bottom:18px;">
        <h4 style="font-size:1.05rem; font-weight:800; margin-bottom:4px;">Manage Airtime to Cash Payout Rates & Receiving SIMs</h4>
        <p style="font-size:0.8125rem; color:var(--palette-text-secondary); margin:0;">
            Set payout percentages and receiving phone numbers for customer airtime transfers.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.airtime_cash.rates') }}">
        @csrf
        <div class="grid-2" style="margin-bottom:16px;">
            @foreach(['MTN', 'AIRTEL', 'GLO', '9MOBILE'] as $net)
                @php $key = strtolower($net); @endphp
                <div style="background:#FFF; padding:16px; border-radius:12px; border:1px solid rgba(145, 158, 171, 0.16);">
                    <div style="font-weight:800; font-size:0.9375rem; margin-bottom:10px; color:var(--palette-primary-main);">{{ $net }} Config</div>
                    <div class="grid-2">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" style="font-size:0.75rem;">Payout Rate (%)</label>
                            <input type="number" name="rate_{{ $key }}" class="form-control" value="{{ $rates[$net] ?? 80 }}" step="0.1" min="0" max="100" required style="font-weight:700;">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" style="font-size:0.75rem;">Receiving Phone No</label>
                            <input type="text" name="receiver_{{ $key }}" class="form-control" value="{{ $receivers[$net] ?? '' }}" required style="font-weight:700;">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-primary btn-sm" style="font-weight:700;"><i class="fa-solid fa-check"></i> Save Payout Settings</button>
        </div>
    </form>
</div>

<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Airtime Conversion Requests</h3>
            <p class="mk-card-subtitle">Verify received airtime on server SIMs and credit customers</p>
        </div>
    </div>

    @if($requests->isEmpty())
        <div style="text-align:center; padding:32px 16px; color:var(--palette-text-secondary);">
            <p>No airtime to cash requests found.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Network</th>
                        <th>Airtime Amount</th>
                        <th>Payout Cash</th>
                        <th>Sender SIM</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                        <tr>
                            <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">{{ $req->created_at->format('M d, Y h:i A') }}</td>
                            <td style="font-family:monospace; font-weight:600;">{{ $req->reference }}</td>
                            <td>
                                <div style="font-weight:700;">{{ $req->user->name ?? 'User' }}</div>
                                <div style="font-size:0.75rem; color:var(--palette-text-secondary);">{{ $req->user->email ?? '' }}</div>
                            </td>
                            <td><span class="badge badge-info">{{ $req->network }}</span></td>
                            <td style="font-weight:700;">₦{{ number_format($req->amount, 2) }}</td>
                            <td style="font-weight:800; color:var(--palette-success-dark);">₦{{ number_format($req->amount_to_receive, 2) }}</td>
                            <td style="font-family:monospace;">{{ $req->sender_phone }}</td>
                            <td>
                                @if($req->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($req->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-error">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($req->status === 'pending')
                                    <div style="display:flex; gap:6px;">
                                        <form method="POST" action="{{ route('admin.airtime_cash.approve', $req->id) }}" onsubmit="return confirm('Confirm that airtime has been received and credit the user wallet?');">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fa-solid fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.airtime_cash.reject', $req->id) }}" onsubmit="return confirm('Reject this airtime request?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-xmark"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="font-size:0.75rem; color:var(--palette-text-secondary);">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
