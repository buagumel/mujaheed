@extends('layouts.app')

@section('title', 'Exam PIN Tokens')
@section('header_title', 'Exam PIN Voucher')

@section('content')
<div style="max-width:680px; margin:0 auto;">
    <!-- Top Action Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <a href="{{ route('exam.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Buy More PINs
        </a>
        <a href="{{ route('exam.receipt', $transaction->reference) }}" target="_blank" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-print"></i> Print Voucher Receipt
        </a>
    </div>

    <!-- Voucher Details Card -->
    <div class="mk-card" style="padding:28px;">
        <div style="text-align:center; padding-bottom:20px; border-bottom:1px solid rgba(145, 158, 171, 0.16); margin-bottom:24px;">
            <div style="width:60px; height:60px; border-radius:50%; background:var(--palette-success-lighter); color:var(--palette-success-dark); display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin:0 auto 12px;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h2 style="font-size:1.375rem; font-weight:800; color:var(--palette-text-primary); margin-bottom:4px;">{{ $transaction->exam_code }} Result Checker PIN(s)</h2>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary);">Reference: <strong style="font-family:monospace;">{{ $transaction->reference }}</strong></div>
        </div>

        <!-- Delivered PINs List -->
        <h4 style="font-size:0.9375rem; font-weight:700; color:var(--palette-text-primary); margin-bottom:16px;">
            Generated PIN Tokens ({{ count($transaction->pins_data) }})
        </h4>

        <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:28px;">
            @foreach($transaction->pins_data as $index => $pinItem)
                <div style="background:var(--palette-background-neutral); border:1px solid rgba(145, 158, 171, 0.2); border-radius:12px; padding:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span class="badge badge-info">Voucher #{{ $index + 1 }}</span>
                        <button type="button" class="btn btn-outline btn-sm" onclick="copyPin('{{ $pinItem['pin'] }}', this)">
                            <i class="fa-regular fa-copy"></i> Copy PIN
                        </button>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <div style="font-size:0.6875rem; color:var(--palette-text-secondary); text-transform:uppercase; font-weight:700;">Serial Number</div>
                            <div style="font-size:0.9375rem; font-weight:700; font-family:monospace; color:var(--palette-text-primary); margin-top:2px;">
                                {{ $pinItem['serial'] }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size:0.6875rem; color:var(--palette-text-secondary); text-transform:uppercase; font-weight:700;">PIN Code</div>
                            <div style="font-size:1.125rem; font-weight:800; font-family:monospace; color:var(--palette-primary-main); margin-top:2px; letter-spacing:1px;">
                                {{ $pinItem['pin'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div style="background:rgba(24, 119, 242, 0.05); border-radius:12px; padding:16px; border:1px solid rgba(24, 119, 242, 0.16);">
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.875rem;">
                <span style="color:var(--palette-text-secondary);">Quantity:</span>
                <span style="font-weight:700;">{{ $transaction->quantity }} PIN(s)</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.875rem;">
                <span style="color:var(--palette-text-secondary);">Unit Price:</span>
                <span style="font-weight:700;">₦{{ number_format($transaction->unit_price, 2) }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:1rem; font-weight:800; border-top:1px solid rgba(24, 119, 242, 0.2); padding-top:8px;">
                <span>Total Paid:</span>
                <span style="color:var(--palette-primary-main);">₦{{ number_format($transaction->total_amount, 2) }}</span>
            </div>
        </div>
    </div>
</div>

<script>
function copyPin(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check" style="color:var(--palette-success-main)"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = orig;
        }, 2000);
    });
}
</script>
@endsection
