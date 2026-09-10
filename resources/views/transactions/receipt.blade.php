<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction->reference }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #F4F6F8;
            color: #1C252E;
            padding: 32px 16px;
            display: flex;
            justify-content: center;
        }
        .receipt-card {
            width: 100%;
            max-width: 480px;
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 0 2px 0 rgba(145, 158, 171, 0.2), 0 12px 24px -4px rgba(145, 158, 171, 0.12);
            padding: 32px;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #DFE3E8;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo-icon {
            width: 48px;
            height: 48px;
            background: #1877F2;
            color: #FFFFFF;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.875rem;
        }
        .item-label {
            color: #637381;
        }
        .item-value {
            font-weight: 600;
            text-align: right;
        }
        .total-box {
            background: #F9FAFB;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
        }
        .btn-primary { background: #1877F2; color: #FFF; }
        .btn-outline { border-color: #DFE3E8; color: #1C252E; background: transparent; }
        @media print {
            body { background: #FFF; padding: 0; }
            .receipt-card { box-shadow: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="header">
            <div class="logo-icon">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <h2 style="font-size:1.25rem; font-weight:800; margin-bottom:2px;">VTU Express Nigeria</h2>
            <p style="font-size:0.75rem; color:#637381;">Transaction Receipt</p>
        </div>

        <div style="text-align:center; margin-bottom:20px;">
            <span style="background:rgba(34, 197, 94, 0.16); color:#118D57; padding:4px 12px; border-radius:9999px; font-weight:700; font-size:0.75rem; text-transform:uppercase;">
                <i class="fa-solid fa-circle-check"></i> Payment Successful
            </span>
        </div>

        @if($transaction->token)
            <div style="background:#D3FCD2; border:1px solid #118D57; border-radius:8px; padding:16px; text-align:center; margin-bottom:16px;">
                <div style="font-size:0.75rem; font-weight:700; color:#065E49; text-transform:uppercase;">Prepaid Meter Token</div>
                <div style="font-size:1.35rem; font-weight:800; font-family:monospace; color:#065E49; margin:4px 0;">{{ $transaction->token }}</div>
                @if($transaction->units)
                    <div style="font-size:0.75rem; color:#065E49;">Units: {{ $transaction->units }}</div>
                @endif
            </div>
        @endif

        <div class="item-row">
            <span class="item-label">Reference</span>
            <span class="item-value" style="font-family:monospace;">{{ $transaction->reference }}</span>
        </div>
        <div class="item-row">
            <span class="item-label">Date & Time</span>
            <span class="item-value">{{ $transaction->created_at->format('d/m/Y, h:i A') }}</span>
        </div>
        <div class="item-row">
            <span class="item-label">Customer</span>
            <span class="item-value">{{ $transaction->user->name }}</span>
        </div>
        <div class="item-row">
            <span class="item-label">Service</span>
            <span class="item-value" style="text-transform:capitalize;">{{ $transaction->service_type }}</span>
        </div>
        <div class="item-row">
            <span class="item-label">Provider</span>
            <span class="item-value">{{ $transaction->provider }}</span>
        </div>
        @if($transaction->plan_name)
            <div class="item-row">
                <span class="item-label">Plan</span>
                <span class="item-value">{{ $transaction->plan_name }}</span>
            </div>
        @endif
        <div class="item-row">
            <span class="item-label">Recipient</span>
            <span class="item-value">{{ $transaction->recipient }}</span>
        </div>

        <div class="total-box">
            <span style="font-weight:700;">Total Paid</span>
            <span style="font-size:1.25rem; font-weight:800; color:#1877F2;">₦{{ number_format($transaction->amount - $transaction->discount_amount, 2) }}</span>
        </div>

        <div style="text-align:center; font-size:0.75rem; color:#919EAB; margin-bottom:24px;">
            Thank you for choosing VTU Express. Need help? support@vtuexpress.ng
        </div>

        <div class="no-print" style="display:flex; gap:10px;">
            <button onclick="window.print()" class="btn-action btn-primary" style="flex:1;">
                <i class="fa-solid fa-print"></i> Print Receipt
            </button>
            <a href="{{ route('transactions.index') }}" class="btn-action btn-outline">
                Back
            </a>
        </div>
    </div>
</body>
</html>
