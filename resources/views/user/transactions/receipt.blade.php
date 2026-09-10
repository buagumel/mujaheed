<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt #{{ $transaction->reference }} | {{ $platformName }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #1877F2;
            --success: #22C55E;
            --text-primary: #1C252E;
            --text-secondary: #637381;
            --bg-neutral: #F4F6F8;
            --border-color: rgba(145, 158, 171, 0.2);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--bg-neutral);
            color: var(--text-primary);
            padding: 40px 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .receipt-card {
            background: #FFFFFF;
            max-width: 520px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 12px 24px -4px rgba(145, 158, 171, 0.16);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .receipt-header {
            padding: 24px 28px;
            text-align: center;
            background: linear-gradient(135deg, rgba(24, 119, 242, 0.06) 0%, rgba(142, 51, 255, 0.04) 100%);
            border-bottom: 1px dashed var(--border-color);
        }

        .receipt-body {
            padding: 28px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid rgba(145, 158, 171, 0.1);
            font-size: 0.875rem;
        }

        .receipt-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .receipt-value {
            font-weight: 700;
            text-align: right;
            color: var(--text-primary);
        }

        .receipt-total {
            background: rgba(34, 197, 94, 0.08);
            border-radius: 12px;
            padding: 16px 20px;
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .token-box {
            background: #FFFDF0;
            border: 1.5px dashed #FFAB00;
            border-radius: 10px;
            padding: 16px;
            margin: 16px 0;
            text-align: center;
        }

        .token-code {
            font-family: monospace;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #B76E00;
            margin-top: 4px;
        }

        .receipt-actions {
            display: flex;
            gap: 12px;
            padding: 0 28px 28px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: none;
            flex: 1;
            transition: opacity 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #FFFFFF;
        }

        .btn-whatsapp {
            background-color: #25D366;
            color: #FFFFFF;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn:hover {
            opacity: 0.9;
        }

        @media print {
            body {
                background: #FFF;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                border: none;
                max-width: 100%;
            }
            .receipt-actions, .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="receipt-card">
    <div class="receipt-header">
        @if($logo)
            <img src="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}" style="max-height:44px; margin-bottom:8px;" alt="{{ $platformName }}">
        @else
            <h2 style="font-size:1.35rem; font-weight:800; color:var(--primary);">{{ $platformName }}</h2>
        @endif
        <div style="font-size:0.8125rem; color:var(--text-secondary); margin-top:4px;">Official Transaction Receipt</div>
    </div>

    <div class="receipt-body">
        <div class="receipt-total">
            <div>
                <div style="font-size:0.75rem; color:#118D57; font-weight:700; text-transform:uppercase;">Amount Paid</div>
                <div style="font-size:1.5rem; font-weight:800; color:#118D57;">₦{{ number_format($transaction->amount, 2) }}</div>
            </div>
            <span style="background:#22C55E; color:#FFF; font-size:0.75rem; font-weight:700; padding:6px 12px; border-radius:20px; text-transform:uppercase;">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>

        @if($transaction->token)
            <div class="token-box">
                <div style="font-size:0.75rem; font-weight:700; color:#B76E00; text-transform:uppercase;">PREPAID TOKEN / RECHARGE PIN</div>
                <div class="token-code">{{ $transaction->token }}</div>
                @if($transaction->units)
                    <div style="font-size:0.8125rem; color:var(--text-secondary); margin-top:4px;">Units: <strong>{{ $transaction->units }}</strong></div>
                @endif
            </div>
        @endif

        <div class="receipt-row">
            <span class="receipt-label">Transaction Ref</span>
            <span class="receipt-value" style="font-family:monospace;">{{ $transaction->reference }}</span>
        </div>

        <div class="receipt-row">
            <span class="receipt-label">Service Type</span>
            <span class="receipt-value" style="text-transform:uppercase;">{{ $transaction->service_type }}</span>
        </div>

        <div class="receipt-row">
            <span class="receipt-label">Provider / Network</span>
            <span class="receipt-value">{{ $transaction->provider }}</span>
        </div>

        @if($transaction->plan_name)
            <div class="receipt-row">
                <span class="receipt-label">Package Plan</span>
                <span class="receipt-value">{{ $transaction->plan_name }}</span>
            </div>
        @endif

        <div class="receipt-row">
            <span class="receipt-label">Recipient / Identifier</span>
            <span class="receipt-value">{{ $transaction->recipient }}</span>
        </div>

        @if($transaction->customer_name)
            <div class="receipt-row">
                <span class="receipt-label">Customer Name</span>
                <span class="receipt-value">{{ $transaction->customer_name }}</span>
            </div>
        @endif

        <div class="receipt-row">
            <span class="receipt-label">Date & Time</span>
            <span class="receipt-value">{{ $transaction->created_at->format('M d, Y • h:i A') }}</span>
        </div>

        <div class="receipt-row" style="border-bottom:none;">
            <span class="receipt-label">Customer Account</span>
            <span class="receipt-value">{{ $transaction->user->name ?? 'Customer' }} ({{ $transaction->user->email ?? '' }})</span>
        </div>
    </div>

    <!-- Actions -->
    @php
        $shareText = rawurlencode("Payment Receipt for {$transaction->service_type} ({$transaction->provider}) - Amount: ₦" . number_format($transaction->amount, 2) . " | Ref: {$transaction->reference} on {$platformName}");
    @endphp
    <div class="receipt-actions no-print">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Print Receipt
        </button>
        <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank" class="btn btn-whatsapp">
            <i class="fa-brands fa-whatsapp"></i> Share
        </a>
    </div>

    <div style="text-align:center; padding:0 28px 24px;" class="no-print">
        <a href="{{ route('dashboard') }}" style="color:var(--text-secondary); font-size:0.8125rem; font-weight:600; text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i> Return to Dashboard
        </a>
    </div>
</div>

</body>
</html>
