@extends('layouts.app')

@section('title', 'Referral & Earnings')
@section('header_title', 'Refer & Earn')

@section('content')
<style>
.ref-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.ref-stat-card {
    background: var(--palette-background-paper, #FFF);
    border: 1px solid var(--palette-divider, rgba(145, 158, 171, 0.16));
    border-radius: 14px;
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}

.ref-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(24, 119, 242, 0.08);
    color: var(--palette-primary-main, #1877F2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.ref-link-input-group {
    display: flex;
    gap: 8px;
}

@media (max-width: 768px) {
    .ref-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .ref-stats-grid > div:last-child {
        grid-column: span 2;
    }
    .ref-stat-card {
        padding: 12px;
        gap: 10px;
    }
    .ref-icon-box {
        width: 36px;
        height: 36px;
        font-size: 1rem;
        border-radius: 8px;
    }
    .ref-stat-card .stat-val {
        font-size: 1.15rem !important;
    }
    .ref-link-input-group {
        flex-direction: column;
    }
    .ref-link-input-group button {
        width: 100%;
    }
}
</style>

<div style="max-width: 960px; margin: 0 auto;">

    <!-- MINIMALIST REFERRAL STATS -->
    <div class="ref-stats-grid">
        <div class="ref-stat-card">
            <div class="ref-icon-box">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div>
                <div style="font-size:0.75rem; color:var(--palette-text-secondary); font-weight:700; text-transform:uppercase;">Bonus Balance</div>
                <div class="stat-val" style="font-size:1.35rem; font-weight:800; color:var(--palette-primary-main);">₦{{ number_format($referralBalance, 2) }}</div>
            </div>
        </div>

        <div class="ref-stat-card">
            <div class="ref-icon-box">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div style="font-size:0.75rem; color:var(--palette-text-secondary); font-weight:700; text-transform:uppercase;">Referred</div>
                <div class="stat-val" style="font-size:1.35rem; font-weight:800; color:var(--palette-text-primary);">{{ number_format($totalReferralsCount) }}</div>
            </div>
        </div>

        <div class="ref-stat-card">
            <div class="ref-icon-box">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <div style="font-size:0.75rem; color:var(--palette-text-secondary); font-weight:700; text-transform:uppercase;">Total Earned</div>
                <div class="stat-val" style="font-size:1.35rem; font-weight:800; color:var(--palette-success-dark,#15803D);">₦{{ number_format($totalEarned, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- REFERRAL LINK & WITHDRAWAL CARDS -->
    <div class="grid-3" style="margin-bottom:24px;">
        <!-- Share Link Card -->
        <div class="mk-card" style="grid-column: span 2; padding:22px;">
            <div style="margin-bottom:16px;">
                <h3 style="font-size:1.05rem; font-weight:800; color:var(--palette-text-primary); margin-bottom:4px;">
                    Invite Friends & Earn
                </h3>
                <p style="font-size:0.8125rem; color:var(--palette-text-secondary); margin:0;">
                    Share your referral link. Earn instant bonus whenever invited friends register and fund their wallet.
                </p>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:0.75rem;">Your Unique Referral Link</label>
                <div class="ref-link-input-group">
                    <input type="text" class="form-control" value="{{ $referralLink }}" readonly style="font-weight:600; font-family:monospace; background:var(--palette-background-neutral);" id="refLinkInput">
                    <button type="button" class="btn btn-primary btn-sm" onclick="copyRefLink(this)" style="font-weight:700;">
                        <i class="fa-regular fa-copy"></i> Copy Link
                    </button>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-size:0.75rem;">Referral Code</label>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <span style="background:var(--palette-background-neutral); border:1px solid rgba(145, 158, 171, 0.2); padding:6px 14px; border-radius:8px; font-weight:800; font-family:monospace; letter-spacing:1.5px; font-size:1.1rem; color:var(--palette-primary-main);">
                        {{ $referralCode }}
                    </span>
                    <span style="font-size:0.75rem; color:var(--palette-text-secondary);">Enter during signup</span>
                </div>
            </div>
        </div>

        <!-- Withdraw Card -->
        <div class="mk-card" style="padding:22px; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <h3 style="font-size:1rem; font-weight:800; margin-bottom:4px;">Withdraw Bonus</h3>
                <p style="font-size:0.75rem; color:var(--palette-text-secondary); margin-bottom:14px;">
                    Transfer referral earnings directly to your main wallet balance.
                </p>

                <form method="POST" action="{{ route('referrals.withdraw') }}">
                    @csrf
                    <div class="form-group" style="margin-bottom:12px;">
                        <label class="form-label" for="amount" style="font-size:0.75rem;">Amount (Min ₦100)</label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:12px; top:10px; font-weight:700; color:var(--palette-text-secondary);">₦</span>
                            <input type="number" id="amount" name="amount" class="form-control" style="padding-left:28px; font-weight:700;" min="100" max="{{ $referralBalance }}" value="{{ min(100, $referralBalance) }}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-sm" {{ $referralBalance < 100 ? 'disabled' : '' }} style="font-weight:700;">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i> Transfer to Main Wallet
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- REFERRED USERS LIST -->
    <div class="mk-card" style="margin-bottom:24px;">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Invited Customers</h3>
                <p class="mk-card-subtitle">Users who registered using your referral link</p>
            </div>
        </div>

        @if($referredUsers->isEmpty())
            <div style="text-align:center; padding:32px 16px; color:var(--palette-text-secondary);">
                <i class="fa-solid fa-user-group" style="font-size:2rem; color:var(--palette-text-disabled); margin-bottom:10px; display:block;"></i>
                <p style="font-size:0.8125rem; margin:0;">No referrals yet. Share your link to start earning!</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="mk-table">
                    <thead>
                        <tr>
                            <th>Date Joined</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Tier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referredUsers as $downline)
                            <tr>
                                <td style="font-size:0.8125rem; color:var(--palette-text-secondary); white-space:nowrap;">{{ $downline->created_at->format('M d, Y') }}</td>
                                <td style="font-weight:700;">{{ $downline->name }}</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td><span class="badge badge-info">{{ $downline->getTierDisplayName() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($referredUsers->hasPages())
                <div style="margin-top:14px;">
                    {{ $referredUsers->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- COMMISSION HISTORY -->
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Referral Commission History</h3>
                <p class="mk-card-subtitle">Earnings history statement</p>
            </div>
        </div>

        @if($commissions->isEmpty())
            <div style="text-align:center; padding:28px 16px; color:var(--palette-text-secondary);">
                <p style="font-size:0.8125rem; margin:0;">No commission transactions yet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="mk-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th style="text-align:right;">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $comm)
                            <tr>
                                <td style="font-size:0.8125rem; color:var(--palette-text-secondary); white-space:nowrap;">{{ $comm->created_at->format('M d, Y') }}</td>
                                <td style="font-weight:600; font-size:0.875rem;">{{ $comm->description }}</td>
                                <td style="text-align:right; font-weight:800; color:var(--palette-success-main);">+₦{{ number_format($comm->amount, 2) }}</td>
                                <td><span class="badge badge-success">Credited</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<script>
function copyRefLink(btn) {
    const input = document.getElementById('refLinkInput');
    navigator.clipboard.writeText(input.value).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = orig;
        }, 2000);
    });
}
</script>
@endsection
