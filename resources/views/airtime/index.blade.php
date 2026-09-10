@extends('layouts.app')

@section('title', 'Buy Airtime')
@section('header_title', 'Airtime Top-up')

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Airtime Top-up</h3>
            </div>
            <span class="badge badge-info">Balance: ₦{{ number_format($wallet->balance, 2) }}</span>
        </div>

        @if(!$user->hasTransactionPin())
            <div class="mk-alert mk-alert-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    You have not set your 4-digit transaction PIN yet. 
                    <a href="{{ route('security.index') }}" style="font-weight:700; text-decoration:underline;">Set Transaction PIN</a> to enable purchases.
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('airtime.purchase') }}" id="airtimeForm" data-no-loader="true">
            @csrf

            <!-- Network Selection -->
            <div class="form-group">
                <label class="form-label">Select Network Provider</label>
                <div class="grid-4" style="gap:12px;">
                    @foreach($networks as $net)
                        @php $slug = strtolower($net->network); @endphp
                        <label class="network-card {{ $loop->first ? 'selected' : '' }}" onclick="selectNetwork('{{ $net->network }}', this)">
                            <input type="radio" name="network" value="{{ $net->network }}" {{ $loop->first ? 'checked' : '' }} style="display:none;">
                            <img src="{{ asset('assets/images/networks/' . $slug . '.svg') }}" class="network-logo-img" alt="{{ $net->network }}">
                            <div style="font-size:0.8rem; font-weight:700; color:var(--palette-text-primary); margin-top:4px;">{{ $net->network }}</div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Phone Number -->
            <div class="form-group">
                <label class="form-label" for="phone">Recipient Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="e.g. 08012345678" value="{{ old('phone', $user->phone) }}" required>
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label class="form-label" for="amount">Recharge Amount (NGN)</label>
                <div style="position:relative;">
                    <span style="position:absolute; left:14px; top:12px; font-weight:700; color:var(--text-secondary);">₦</span>
                    <input type="number" id="amount" name="amount" class="form-control" style="padding-left:32px; font-weight:700; font-size:1.125rem;" min="50" max="50000" placeholder="1000" value="{{ old('amount', 500) }}" oninput="updateTotalDisplay()" required>
                </div>
                <!-- Amount Presets -->
                <div style="display:flex; gap:8px; margin-top:8px; flex-wrap:wrap;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="setAirtimeAmount(100)">₦100</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setAirtimeAmount(200)">₦200</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setAirtimeAmount(500)">₦500</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setAirtimeAmount(1000)">₦1,000</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setAirtimeAmount(2000)">₦2,000</button>
                </div>
            </div>

            <!-- Pricing Summary Card -->
            <div style="background:var(--bg-neutral); border-radius:var(--border-radius-sm); padding:16px; margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; font-size:1rem; font-weight:800;">
                    <span>Total Amount to Pay:</span>
                    <span style="color:var(--color-primary);" id="totalToPayDisplay">₦500.00</span>
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-block btn-lg" {{ !$user->hasTransactionPin() ? 'disabled' : '' }} onclick="openPinModal(this.form)">
                <i class="fa-solid fa-bolt"></i> Recharge Airtime Now
            </button>
        </form>
    </div>
</div>

<script>
    function selectNetwork(net, element) {
        document.querySelectorAll('.network-card').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        element.querySelector('input[type="radio"]').checked = true;
    }

    function setAirtimeAmount(val) {
        document.getElementById('amount').value = val;
        updateTotalDisplay();
    }

    function updateTotalDisplay() {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        document.getElementById('totalToPayDisplay').innerText = `₦${amount.toFixed(2)}`;
    }

    document.getElementById('airtimeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        
        if (window.GlobalLoader) {
            window.GlobalLoader.showActionLoader('Processing...', 'Please wait...');
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html,application/xhtml+xml,application/xml'
            }
        })
        .then(res => {
            if (res.redirected) {
                window.location.href = res.url;
            } else {
                return res.text().then(html => {
                    if (window.GlobalLoader) window.GlobalLoader.hideActionLoader();
                    document.open();
                    document.write(html);
                    document.close();
                });
            }
        })
        .catch(err => {
            if (window.GlobalLoader) window.GlobalLoader.hideActionLoader();
            alert('Transaction request failed. Please try again.');
        });
    });

    updateTotalDisplay();
</script>
@endsection
