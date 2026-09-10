@extends('layouts.app')

@section('title', 'Electricity Bill')
@section('header_title', 'Pay Electricity Bill')

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Electricity Bill</h3>
            </div>
            <span class="badge badge-info">Balance: ₦{{ number_format($wallet->balance, 2) }}</span>
        </div>

        @if(!$user->hasTransactionPin())
            <div class="mk-alert mk-alert-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    You have not set your 4-digit transaction PIN yet. 
                    <a href="{{ route('security.index') }}" style="font-weight:700; text-decoration:underline;">Set Transaction PIN</a> to enable payments.
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('electricity.pay') }}" id="electricityForm" data-action-loader="true" data-loader-title="Generating Electricity Token..." data-loader-subtitle="Connecting to DISCO billing infrastructure...">
            @csrf

            <!-- DISCO Provider -->
            <div class="form-group">
                <label class="form-label" for="disco">Select Distribution Company (DISCO)</label>
                <select id="disco" name="disco" class="form-select" required>
                    <option value="">-- Choose DISCO Provider --</option>
                    @foreach($providers as $p)
                        <option value="{{ $p->code }}" data-fee="{{ $p->convenience_fee }}" data-min="{{ $p->min_amount }}" data-max="{{ $p->max_amount }}">
                            {{ $p->name }} (₦{{ number_format($p->convenience_fee, 2) }} Fee)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Meter Type -->
            <div class="form-group">
                <label class="form-label">Meter Type</label>
                <div style="display:flex; gap:16px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="meter_type" value="prepaid" checked style="accent-color:var(--color-primary);">
                        <span style="font-weight:600;">Prepaid (Token)</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="meter_type" value="postpaid" style="accent-color:var(--color-primary);">
                        <span style="font-weight:600;">Postpaid (Bill)</span>
                    </label>
                </div>
            </div>

            <!-- Meter Number & Verification -->
            <div class="form-group">
                <label class="form-label" for="meter_number">Meter / Account Number</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" id="meter_number" name="meter_number" class="form-control" placeholder="Enter meter number" required>
                    <button type="button" class="btn btn-outline" onclick="verifyMeter()" id="verifyBtn">
                        <i class="fa-solid fa-user-check"></i> Verify
                    </button>
                </div>
            </div>

            <!-- Verified Customer Details Card (Hidden until verified) -->
            <div id="customerDetailsBox" style="display:none; background:var(--color-info-light); border:1px solid #006C9C; border-radius:var(--border-radius-sm); padding:16px; margin-bottom:20px;">
                <div style="font-weight:700; color:#003768; margin-bottom:4px;">Verified Customer:</div>
                <div id="verifiedCustomerName" style="font-weight:700; font-size:1rem; color:#003768;"></div>
                <div id="verifiedCustomerAddress" style="font-size:0.8125rem; color:#006C9C; margin-top:2px;"></div>
                <input type="hidden" name="customer_name" id="customer_name_input">
            </div>

            <!-- Recharge Amount -->
            <div class="form-group">
                <label class="form-label" for="amount">Amount (NGN)</label>
                <div style="position:relative;">
                    <span style="position:absolute; left:14px; top:12px; font-weight:700; color:var(--text-secondary);">₦</span>
                    <input type="number" id="amount" name="amount" class="form-control" style="padding-left:32px; font-weight:700; font-size:1.125rem;" min="500" max="100000" placeholder="5000" value="{{ old('amount', 2000) }}" required>
                </div>
            </div>

            <!-- Recipient Phone -->
            <div class="form-group">
                <label class="form-label" for="phone">Customer Phone Number (For Token SMS)</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="08012345678" value="{{ old('phone', $user->phone) }}" required>
            </div>

            <button type="button" class="btn btn-primary btn-block btn-lg" {{ !$user->hasTransactionPin() ? 'disabled' : '' }} onclick="openPinModal(this.form)">
                <i class="fa-solid fa-bolt"></i> Pay Electricity Bill
            </button>
        </form>
    </div>
</div>

<script>
    async function verifyMeter() {
        const disco = document.getElementById('disco').value;
        const meter = document.getElementById('meter_number').value;
        const type = document.querySelector('input[name="meter_type"]:checked').value;
        const btn = document.getElementById('verifyBtn');

        if (!disco || !meter) {
            alert('Please select DISCO and enter a meter number first.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

        try {
            const response = await fetch('{{ route('electricity.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ disco, meter_number: meter, type })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('customerDetailsBox').style.display = 'block';
                document.getElementById('verifiedCustomerName').innerText = data.customer_name;
                document.getElementById('verifiedCustomerAddress').innerText = data.address || '';
                document.getElementById('customer_name_input').value = data.customer_name;
            } else {
                alert(data.message || 'Verification failed. Please check the meter number.');
            }
        } catch (e) {
            alert('Error verifying meter number.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Verify';
        }
    }
</script>
@endsection
