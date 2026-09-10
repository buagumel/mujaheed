@extends('layouts.app')

@section('title', 'Exam Scratch Cards & PINs')
@section('header_title', 'Exam PINs')

@section('content')
<div style="margin-bottom:28px;">
    <!-- Purchase Form Card -->
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Exam PINs</h3>
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

        <form method="POST" action="{{ route('exam.purchase') }}" data-action-loader="true" data-loader-title="Generating Exam PINs..." data-loader-subtitle="Retrieving verified result checker tokens...">
            @csrf

            <!-- Select Examination Body -->
            <div class="form-group">
                <label class="form-label">Select Examination Board</label>
                <div class="grid-4" style="gap:12px;">
                    @foreach($packages as $pkg)
                        @php
                            $userPrice = $pkg->getPriceForTier($user->tier);
                            $slug = strtolower(explode(' ', $pkg->name)[0]);
                        @endphp
                        <label class="network-card {{ $loop->first ? 'selected' : '' }}" onclick="selectExamBoard('{{ $pkg->code }}', {{ $userPrice }}, '{{ addslashes($pkg->name) }}', this)">
                            <input type="radio" name="exam_code" value="{{ $pkg->code }}" {{ $loop->first ? 'checked' : '' }} style="display:none;">
                            <img src="{{ asset('assets/images/networks/' . $slug . '.svg') }}" class="network-logo-img" alt="{{ $pkg->name }}">
                            <div style="font-size:0.875rem; font-weight:800; color:var(--palette-text-primary);">{{ $pkg->code }}</div>
                            <div style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:700; margin-top:2px;">₦{{ number_format($userPrice, 2) }}</div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Quantity Selector -->
            <div class="form-group">
                <label class="form-label" for="quantity">Quantity (Number of PINs)</label>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <input type="number" id="quantity" name="quantity" class="form-control" min="1" max="20" value="1" required oninput="calculateExamTotal()" style="font-weight:700; font-size:1.125rem; max-width:140px;">
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="setExamQty(1)">1</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="setExamQty(2)">2</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="setExamQty(5)">5</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="setExamQty(10)">10</button>
                    </div>
                </div>
            </div>

            <!-- Total Price Display Box -->
            <div style="background:var(--palette-background-neutral); border-radius:12px; padding:16px; margin-bottom:20px; border:1px solid rgba(145, 158, 171, 0.16);">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.875rem;">
                    <span style="color:var(--palette-text-secondary);" id="examSummaryLabel">WAEC Result Checker PIN:</span>
                    <span style="color:var(--palette-text-primary); font-weight:700;" id="examUnitLabel">₦3,800.00 x 1</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:1.125rem; font-weight:800; border-top:1px solid rgba(145, 158, 171, 0.12); padding-top:8px;">
                    <span>Total Amount:</span>
                    <span style="color:var(--palette-primary-main);" id="examTotalDisplay">₦3,800.00</span>
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-block btn-lg" {{ !$user->hasTransactionPin() ? 'disabled' : '' }} onclick="openPinModal(this.form)">
                <i class="fa-solid fa-bolt"></i> Generate Exam PIN(s) Now
            </button>
        </form>
    </div>
</div>

<!-- Recent Purchased PINs -->
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Recent Exam PIN Purchases</h3>
            <p class="mk-card-subtitle">Access your generated tokens & PIN codes</p>
        </div>
    </div>

    @if($recentTransactions->isEmpty())
        <div style="text-align:center; padding:32px 16px; color:var(--palette-text-secondary);">
            <i class="fa-solid fa-ticket" style="font-size:2.5rem; color:var(--palette-text-disabled); margin-bottom:12px; display:block;"></i>
            <p>No exam PINs purchased yet.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Exam Body</th>
                        <th>Quantity</th>
                        <th>Total Paid</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $tx)
                        <tr>
                            <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                            <td style="font-family:monospace; font-weight:600;">{{ $tx->reference }}</td>
                            <td><span class="badge badge-info">{{ $tx->exam_code }}</span></td>
                            <td style="font-weight:700;">{{ $tx->quantity }} PIN(s)</td>
                            <td style="font-weight:700;">₦{{ number_format($tx->total_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('exam.show', $tx->reference) }}" class="btn btn-outline btn-sm">
                                    <i class="fa-solid fa-eye"></i> View PINs
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
let currentExamUnitPrice = {{ $packages->first() ? $packages->first()->getPriceForTier($user->tier) : 3800 }};
let currentExamName = "{{ $packages->first() ? addslashes($packages->first()->name) : 'WAEC Result Checker' }}";

function selectExamBoard(code, price, name, el) {
    document.querySelectorAll('.network-card').forEach(item => item.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input[type="radio"]').checked = true;
    currentExamUnitPrice = price;
    currentExamName = name;
    calculateExamTotal();
}

function setExamQty(qty) {
    document.getElementById('quantity').value = qty;
    calculateExamTotal();
}

function calculateExamTotal() {
    const qty = parseInt(document.getElementById('quantity').value) || 1;
    const total = qty * currentExamUnitPrice;
    document.getElementById('examSummaryLabel').innerText = `${currentExamName}:`;
    document.getElementById('examUnitLabel').innerText = `₦${currentExamUnitPrice.toFixed(2)} x ${qty}`;
    document.getElementById('examTotalDisplay').innerText = `₦${total.toFixed(2)}`;
}

calculateExamTotal();
</script>
@endsection
