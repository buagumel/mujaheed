<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam PIN Receipt - {{ $transaction->reference }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background: #F4F6F8;
            margin: 0;
            padding: 24px;
            color: #1C252E;
        }
        .receipt-card {
            max-width: 520px;
            margin: 0 auto;
            background: #FFF;
            border-radius: 16px;
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
            padding: 32px;
            border: 1px solid #DFE3E8;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #DFE3E8;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .pin-box {
            background: #F9FAFB;
            border: 1px solid #DFE3E8;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
        }
        @media print {
            body { background: #FFF; padding: 0; }
            .receipt-card { box-shadow: none; border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="header">
            <h2 style="margin:0; font-size:1.5rem; color:#1877F2;">{{ \App\Models\SystemSetting::get('platform_name', 'VTU Express') }}</h2>
            <div style="font-size:0.875rem; color:#637381; margin-top:4px;">Official Exam PIN Voucher</div>
        </div>

        <div style="margin-bottom:20px; font-size:0.875rem;">
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span style="color:#637381;">Reference:</span>
                <span style="font-weight:700; font-family:monospace;">{{ $transaction->reference }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span style="color:#637381;">Exam Body:</span>
                <span style="font-weight:700;">{{ $transaction->exam_code }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span style="color:#637381;">Date:</span>
                <span>{{ $transaction->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span style="color:#637381;">Customer:</span>
                <span style="font-weight:700;">{{ $user->name }}</span>
            </div>
        </div>

        <div style="margin-bottom:24px;">
            <h4 style="margin:0 0 12px; font-size:0.9375rem;">Tokens / PINs:</h4>
            @foreach($transaction->pins_data as $idx => $pin)
                <div class="pin-box">
                    <div style="font-size:0.75rem; color:#637381;">#{{ $idx + 1 }} Serial: <strong>{{ $pin['serial'] }}</strong></div>
                    <div style="font-size:1.125rem; font-weight:800; color:#1877F2; font-family:monospace; margin-top:4px;">
                        PIN: {{ $pin['pin'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div style="border-top:2px dashed #DFE3E8; padding-top:16px; margin-bottom:24px; font-size:0.9375rem;">
            <div style="display:flex; justify-content:space-between; font-weight:800; font-size:1.125rem;">
                <span>Total Amount Paid:</span>
                <span style="color:#1877F2;">₦{{ number_format($transaction->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="no-print" style="display:flex; gap:12px;">
            <button onclick="window.print()" style="flex:1; padding:12px; background:#1877F2; color:#FFF; font-weight:700; border:none; border-radius:8px; cursor:pointer;">
                Print Receipt
            </button>
            <button onclick="window.close()" style="flex:1; padding:12px; background:#DFE3E8; color:#1C252E; font-weight:700; border:none; border-radius:8px; cursor:pointer;">
                Close
            </button>
        </div>
    </div>
</body>
</html>
