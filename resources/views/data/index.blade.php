@extends('layouts.app')

@section('title', 'Buy Data')
@section('header_title', 'Data Bundles')

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Buy Data</h3>
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

        <form method="POST" action="{{ route('data.purchase') }}" id="dataForm" data-no-loader="true">
            @csrf

            <!-- Network Selection -->
            <div class="form-group">
                <label class="form-label">Select Network</label>
                <div class="grid-4" style="gap:12px;">
                    @php $networks = ['MTN', 'AIRTEL', 'GLO', '9MOBILE']; @endphp
                    @foreach($networks as $net)
                        @php $slug = strtolower($net); @endphp
                        <label class="network-card {{ $loop->first ? 'selected' : '' }}" onclick="filterPlans('{{ $net }}', this)">
                            <img src="{{ asset('assets/images/networks/' . $slug . '.svg') }}" class="network-logo-img" alt="{{ $net }}">
                            <div style="font-size:0.8rem; font-weight:700; color:var(--palette-text-primary);">{{ $net }}</div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Data Plan Selection -->
            <div class="form-group">
                <label class="form-label" for="plan_id">Select Data Plan</label>
                <select id="plan_id" name="plan_id" class="form-select" required onchange="updatePlanPrice()">
                    <option value="">-- Choose Data Plan --</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" data-network="{{ $plan->network }}" data-price="{{ $plan->selling_price }}" {{ $loop->first ? 'selected' : '' }}>
                            {{ $plan->network }} - {{ $plan->name }} ({{ $plan->validity }}) - ₦{{ number_format($plan->selling_price, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Recipient Phone Number -->
            <div class="form-group">
                <label class="form-label" for="phone">Recipient Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="e.g. 08012345678" value="{{ old('phone', $user->phone) }}" required>
            </div>

            <!-- Total Price Summary -->
            <div style="background:var(--bg-neutral); border-radius:var(--border-radius-sm); padding:16px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:600; color:var(--text-secondary);">Total to Pay:</span>
                <span style="font-size:1.25rem; font-weight:800; color:var(--color-primary);" id="dataPriceDisplay">₦0.00</span>
            </div>

            <button type="button" class="btn btn-primary btn-block btn-lg" {{ !$user->hasTransactionPin() ? 'disabled' : '' }} onclick="openPinModal(this.form)">
                <i class="fa-solid fa-wifi"></i> Purchase Data Bundle
            </button>
        </form>
    </div>
</div>

<script>
    function filterPlans(network, element) {
        document.querySelectorAll('.network-card').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');

        const select = document.getElementById('plan_id');
        let firstMatch = null;

        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            if (!opt.value) continue;
            if (opt.getAttribute('data-network') === network) {
                opt.style.display = '';
                if (!firstMatch) firstMatch = opt;
            } else {
                opt.style.display = 'none';
            }
        }

        if (firstMatch) {
            select.value = firstMatch.value;
        } else {
            select.value = '';
        }
        updatePlanPrice();
    }

    function updatePlanPrice() {
        const select = document.getElementById('plan_id');
        const selected = select.options[select.selectedIndex];
        const price = selected && selected.getAttribute('data-price') ? parseFloat(selected.getAttribute('data-price')) : 0;
        document.getElementById('dataPriceDisplay').innerText = `₦${price.toFixed(2)}`;
    }

    document.getElementById('dataForm').addEventListener('submit', function(e) {
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

    filterPlans('MTN', document.querySelector('.network-card'));
</script>
@endsection
