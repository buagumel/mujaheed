@extends('layouts.app')

@section('title', 'Cable TV')
@section('header_title', 'Cable TV Subscription')

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Cable TV</h3>
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

        <form method="POST" action="{{ route('cable.pay') }}" data-action-loader="true" data-loader-title="Renewing Cable Subscription..." data-loader-subtitle="Activating cable bouquet on provider network...">
            @csrf

            <!-- Provider Selection -->
            <div class="form-group">
                <label class="form-label">Select Cable Provider</label>
                <div class="grid-3" style="gap:12px;">
                    @php $providers = ['DSTV', 'GOTV', 'STARTIMES']; @endphp
                    @foreach($providers as $prov)
                        @php $slug = strtolower($prov); @endphp
                        <label class="network-card {{ $loop->first ? 'selected' : '' }}" onclick="filterCablePlans('{{ $prov }}', this)">
                            <img src="{{ asset('assets/images/networks/' . $slug . '.svg') }}" class="network-logo-img" alt="{{ $prov }}">
                            <div style="font-weight:700; font-size:0.85rem; color:var(--palette-text-primary);">{{ $prov }}</div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Cable Plan Package -->
            <div class="form-group">
                <label class="form-label" for="plan_id">Select Bouquet / Package</label>
                <select id="plan_id" name="plan_id" class="form-select" required onchange="updateCablePrice()">
                    <option value="">-- Choose Package --</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" data-provider="{{ $plan->provider }}" data-price="{{ $plan->selling_price }}">
                            {{ $plan->provider }} - {{ $plan->name }} (₦{{ number_format($plan->selling_price, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Smartcard Number & Verification -->
            <div class="form-group">
                <label class="form-label" for="smartcard_number">Smartcard / IUC / UIC Number</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" id="smartcard_number" name="smartcard_number" class="form-control" placeholder="e.g. 7029104928" required>
                    <button type="button" class="btn btn-outline" onclick="verifySmartcard()" id="verifyCableBtn">
                        <i class="fa-solid fa-user-check"></i> Verify
                    </button>
                </div>
            </div>

            <!-- Verified Customer Details Box -->
            <div id="cableCustomerBox" style="display:none; background:var(--color-info-light); border:1px solid #006C9C; border-radius:var(--border-radius-sm); padding:16px; margin-bottom:20px;">
                <div style="font-weight:700; color:#003768; margin-bottom:4px;">Account Name:</div>
                <div id="verifiedCableName" style="font-weight:700; font-size:1rem; color:#003768;"></div>
                <input type="hidden" name="customer_name" id="cable_customer_name_input">
            </div>

            <!-- Customer Phone -->
            <div class="form-group">
                <label class="form-label" for="phone">Customer Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="08012345678" value="{{ old('phone', $user->phone) }}" required>
            </div>

            <!-- Price Summary -->
            <div style="background:var(--bg-neutral); border-radius:var(--border-radius-sm); padding:16px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:600; color:var(--text-secondary);">Subscription Amount:</span>
                <span style="font-size:1.25rem; font-weight:800; color:var(--color-primary);" id="cablePriceDisplay">₦0.00</span>
            </div>

            <button type="button" class="btn btn-primary btn-block btn-lg" {{ !$user->hasTransactionPin() ? 'disabled' : '' }} onclick="openPinModal(this.form)">
                <i class="fa-solid fa-tv"></i> Pay & Renew Subscription
            </button>
        </form>
    </div>
</div>

<script>
    let currentProvider = 'DSTV';

    function filterCablePlans(prov, element) {
        document.querySelectorAll('.network-card').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        currentProvider = prov;

        const select = document.getElementById('plan_id');
        let firstMatch = null;

        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            if (!opt.value) continue;
            if (opt.getAttribute('data-provider') === prov) {
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
        updateCablePrice();
    }

    function updateCablePrice() {
        const select = document.getElementById('plan_id');
        const selected = select.options[select.selectedIndex];
        const price = selected && selected.getAttribute('data-price') ? parseFloat(selected.getAttribute('data-price')) : 0;
        document.getElementById('cablePriceDisplay').innerText = `₦${price.toFixed(2)}`;
    }

    async function verifySmartcard() {
        const smartcard = document.getElementById('smartcard_number').value;
        const btn = document.getElementById('verifyCableBtn');

        if (!smartcard) {
            alert('Please enter a smartcard/IUC number first.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

        try {
            const response = await fetch('{{ route('cable.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ provider: currentProvider, smartcard_number: smartcard })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('cableCustomerBox').style.display = 'block';
                document.getElementById('verifiedCableName').innerText = data.customer_name;
                document.getElementById('cable_customer_name_input').value = data.customer_name;
            } else {
                alert(data.message || 'Smartcard verification failed.');
            }
        } catch (e) {
            alert('Error verifying smartcard number.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Verify';
        }
    }

    filterCablePlans('DSTV', document.querySelector('.network-card'));
</script>
@endsection
