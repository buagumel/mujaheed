@extends('layouts.app')

@section('title', 'Airtime to Cash')
@section('header_title', 'Airtime to Cash')

@section('content')
<div style="margin-bottom:28px;">
    <!-- Airtime to Cash Form -->
    <div class="mk-card" style="padding:clamp(16px, 3vw, 28px); background: transparent; border: none; box-shadow: none;">
        <div style="margin-bottom:20px; padding-bottom:14px; border-bottom:1px solid rgba(145, 158, 171, 0.12);">
            <h3 style="font-size:1.1rem; font-weight:800; color:var(--palette-text-primary); margin:0;">
                P2P Airtime to Cash Swap
            </h3>
        </div>

        <form method="POST" action="{{ route('airtime_cash.store') }}" data-action-loader="true" data-loader-title="Submitting Conversion..." data-loader-subtitle="Generating secure transfer order...">
            @csrf

            <!-- Network Selection -->
            <div class="form-group">
                <label class="form-label">Select Network Provider</label>
                <div class="grid-4" style="gap:12px;">
                    @php $networks = ['MTN', 'AIRTEL', 'GLO', '9MOBILE']; @endphp
                    @foreach($networks as $net)
                        @php 
                            $slug = strtolower($net); 
                            $rate = $rates[$net] ?? 80;
                        @endphp
                        <label class="network-card {{ $loop->first ? 'selected' : '' }}" onclick="selectA2CNetwork('{{ $net }}', {{ $rate }}, this)">
                            <input type="radio" name="network" value="{{ $net }}" {{ $loop->first ? 'checked' : '' }} style="display:none;">
                            <img src="{{ asset('assets/images/networks/' . $slug . '.svg') }}" class="network-logo-img" alt="{{ $net }}">
                            <div style="font-size:0.8rem; font-weight:700; color:var(--palette-text-primary);">{{ $net }}</div>
                            <div style="font-size:0.6875rem; color:#15803d; font-weight:700; margin-top:2px;">{{ $rate }}% Payout</div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label class="form-label" for="amount">Airtime Amount (Min ₦500)</label>
                <div style="position:relative;">
                    <span style="position:absolute; left:14px; top:12px; font-weight:700; color:var(--palette-text-secondary);">₦</span>
                    <input type="number" id="amount" name="amount" class="form-control" style="padding-left:32px; font-weight:700; font-size:1.125rem;" min="500" max="50000" step="100" value="1000" required oninput="calculateCashPayout()">
                </div>
            </div>

            <!-- Sender Phone -->
            <div class="form-group">
                <label class="form-label" for="sender_phone">Your SIM Phone Number (Sending Airtime)</label>
                <input type="tel" id="sender_phone" name="sender_phone" class="form-control" placeholder="08012345678" value="{{ old('sender_phone', $user->phone) }}" required>
            </div>

            <!-- Payout Destination -->
            <div class="form-group">
                <label class="form-label">Payout Destination</label>
                <div style="display:flex; gap:16px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="payout_method" value="wallet" checked onclick="toggleBankDetails(false)">
                        <span style="font-weight:700; font-size:0.875rem;">Instant Wallet Credit</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="payout_method" value="bank" onclick="toggleBankDetails(true)">
                        <span style="font-weight:700; font-size:0.875rem;">Direct Bank Account</span>
                    </label>
                </div>
            </div>

            <!-- Bank Details (Hidden by default) -->
            <div id="bankDetailsBox" style="display:none; background:var(--palette-background-neutral); padding:16px; border-radius:12px; margin-bottom:20px; border:1px solid rgba(145, 158, 171, 0.16);">
                <div class="form-group">
                    <label class="form-label" for="bank_name">Bank Name</label>
                    <input type="text" id="bank_name" name="bank_name" class="form-control" placeholder="e.g. OPay, GTBank, Access Bank">
                </div>
                <div class="grid-2">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="bank_account_number">10-Digit Account Number</label>
                        <input type="text" id="bank_account_number" name="bank_account_number" class="form-control" placeholder="0123456789" maxlength="10">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="bank_account_name">Account Holder Name</label>
                        <input type="text" id="bank_account_name" name="bank_account_name" class="form-control" placeholder="e.g. John Doe">
                    </div>
                </div>
            </div>

            <!-- Conversion Payout Summary Box -->
            <div style="background:rgba(24, 119, 242, 0.05); border-radius:12px; padding:16px; margin-bottom:20px; border:1px solid rgba(24, 119, 242, 0.16);">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.875rem;">
                    <span style="color:var(--palette-text-secondary);">Conversion Rate:</span>
                    <span style="font-weight:700; color:var(--palette-primary-main);" id="a2cRateDisplay">80%</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:1.125rem; font-weight:800; border-top:1px solid rgba(24, 119, 242, 0.16); padding-top:8px;">
                    <span>Amount You Will Receive:</span>
                    <span style="color:var(--palette-success-dark);" id="a2cPayoutDisplay">₦800.00</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-arrow-right-arrow-left"></i> Proceed & View Transfer Code
            </button>
        </form>
    </div>
</div>

<!-- History Table -->
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Airtime to Cash Requests</h3>
            <p class="mk-card-subtitle">Track status of your conversion orders</p>
        </div>
    </div>

    @if($requests->isEmpty())
        <div style="text-align:center; padding:32px 16px; color:var(--palette-text-secondary);">
            <p>No conversion requests yet.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Network</th>
                        <th>Airtime Amount</th>
                        <th>Cash Payout</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                        <tr>
                            <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">{{ $req->created_at->format('M d, Y h:i A') }}</td>
                            <td style="font-family:monospace; font-weight:600;">{{ $req->reference }}</td>
                            <td><span class="badge badge-info">{{ $req->network }}</span></td>
                            <td style="font-weight:700;">₦{{ number_format($req->amount, 2) }}</td>
                            <td style="font-weight:800; color:var(--palette-success-dark);">₦{{ number_format($req->amount_to_receive, 2) }}</td>
                            <td>
                                @if($req->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($req->status === 'pending')
                                    <span class="badge badge-warning">Pending Review</span>
                                @else
                                    <span class="badge badge-error">Declined</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('airtime_cash.show', $req->reference) }}" class="btn btn-outline btn-sm">
                                    Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
let currentA2cRate = {{ $rates['MTN'] ?? 80 }};

function selectA2CNetwork(net, rate, el) {
    document.querySelectorAll('.network-card').forEach(item => item.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input[type="radio"]').checked = true;
    currentA2cRate = rate;
    calculateCashPayout();
}

function calculateCashPayout() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const payout = (amount * currentA2cRate) / 100;
    document.getElementById('a2cRateDisplay').innerText = `${currentA2cRate}%`;
    document.getElementById('a2cPayoutDisplay').innerText = `₦${payout.toFixed(2)}`;
}

function toggleBankDetails(show) {
    document.getElementById('bankDetailsBox').style.display = show ? 'block' : 'none';
}

calculateCashPayout();
</script>
@endsection
