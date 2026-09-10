@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Hi, ' . $user->name . ' 👋')

@section('content')
<!-- Top Hero Wallet Banner (Dynamically adapts to configured Primary Brand Color) -->
<div class="mk-card" style="background: linear-gradient(135deg, var(--palette-primary-main) 0%, var(--palette-primary-dark) 100%); color:#FFF; margin-bottom:28px; padding:32px; border-radius:16px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
        <div>
            <div style="font-size:0.875rem; color:rgba(255,255,255,0.85); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Available Wallet Balance</div>
            <div style="font-size:2.25rem; font-weight:800; margin: 8px 0; letter-spacing:-0.5px;">₦{{ number_format($wallet->balance, 2) }}</div>
            <div style="font-size:0.8125rem; color:rgba(255,255,255,0.8);">Account Status: <span class="badge badge-success" style="color:#FFF; background:rgba(255,255,255,0.25); border:1px solid rgba(255,255,255,0.4);">Active Account</span></div>
        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a href="{{ route('wallet.fund') }}" class="btn btn-lg" style="background:#FFF; color:var(--palette-primary-main); font-weight:800; border-radius:10px; box-shadow:0 8px 16px rgba(0,0,0,0.15);">
                <i class="fa-solid fa-plus-circle"></i> Fund Wallet
            </a>
            <a href="{{ route('transactions.index') }}" class="btn btn-lg" style="background:rgba(255,255,255,0.15); color:#FFF; font-weight:600; border-radius:10px; border:1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-clock-rotate-left"></i> History
            </a>
        </div>
    </div>
</div>

<!-- Quick Statistics Grid -->
<div class="grid-3" style="margin-bottom: 28px;">
    <div class="mk-card" style="display:flex; align-items:center; gap:18px;">
        <div style="width:52px; height:52px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
            <i class="fa-solid fa-arrow-down-long"></i>
        </div>
        <div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">Total Funded</div>
            <div style="font-size:1.375rem; font-weight:700; color:var(--palette-text-primary);">₦{{ number_format($totalFunded, 2) }}</div>
        </div>
    </div>

    <div class="mk-card" style="display:flex; align-items:center; gap:18px;">
        <div style="width:52px; height:52px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
        <div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">Total Purchases</div>
            <div style="font-size:1.375rem; font-weight:700; color:var(--palette-text-primary);">₦{{ number_format($totalSpent, 2) }}</div>
        </div>
    </div>

    <div class="mk-card" style="display:flex; align-items:center; gap:18px;">
        <div style="width:52px; height:52px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
            <div style="font-size:0.8125rem; color:var(--palette-text-secondary); font-weight:600;">Security PIN</div>
            <div style="font-size:1rem; font-weight:700; color:var(--palette-text-primary);">
                @if($user->hasTransactionPin())
                    <span class="badge" style="background:var(--palette-primary-lighter); color:var(--palette-primary-main); font-weight:700;">PIN Active</span>
                @else
                    <a href="{{ route('security.index') }}" class="badge badge-error" style="text-decoration:none;">Set PIN Now</a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick VTU Services -->
<div class="mk-card" style="margin-bottom: 28px; background: transparent; border: none; box-shadow: none; padding: 0;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:18px;">
        <div>
            <h3 class="mk-card-title" style="font-size:1.25rem; font-weight:800; color:var(--palette-text-primary); margin:0;">Services Catalog</h3>
            <p class="mk-card-subtitle" style="font-size:0.875rem; color:var(--palette-text-secondary); margin:2px 0 0 0;">Instant VTU recharge & automated bill payments</p>
        </div>

        <!-- Service Category Filter Dropdown -->
        <div style="display:flex; align-items:center; gap:8px;">
            <label for="serviceCategorySelect" style="font-size:0.875rem; font-weight:700; color:var(--palette-text-secondary);">Category:</label>
            <select id="serviceCategorySelect" class="form-control" style="font-weight:700; padding:8px 16px; border-radius:10px; min-width:180px;" onchange="filterServiceCards(this.value)">
                <option value="all" selected>All Services</option>
                <option value="airtime">Airtime Top-up</option>
                <option value="data">Data Bundles</option>
                <option value="electricity">Electricity Tokens</option>
                <option value="cable">Cable TV Subscriptions</option>
                <option value="exam">Exam PINs (WAEC/NECO)</option>
                <option value="p2p">P2P Airtime to Cash</option>
                <option value="account">Wholesale Tiers & Earnings</option>
            </select>
        </div>
    </div>

    <div class="grid-4 quick-services-grid" style="gap:16px;">
        <a href="{{ route('airtime.index') }}" class="mk-card quick-service-item" data-category="airtime" style="text-decoration:none; text-align:center; padding:20px; border:1px solid rgba(145, 158, 171, 0.16); transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="quick-service-icon" style="width:54px; height:54px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-phone-volume"></i>
            </div>
            <div class="quick-service-title" style="font-weight:700; color:var(--palette-text-primary);">Buy Airtime</div>
            <div class="quick-service-subtitle" style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:600; margin-top:2px;">Instant Top-up</div>
        </a>

        <a href="{{ route('data.index') }}" class="mk-card quick-service-item" data-category="data" style="text-decoration:none; text-align:center; padding:20px; border:1px solid rgba(145, 158, 171, 0.16); transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="quick-service-icon" style="width:54px; height:54px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-wifi"></i>
            </div>
            <div class="quick-service-title" style="font-weight:700; color:var(--palette-text-primary);">Buy Data</div>
            <div class="quick-service-subtitle" style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:600; margin-top:2px;">SME & Gifting</div>
        </a>

        <a href="{{ route('electricity.index') }}" class="mk-card quick-service-item" data-category="electricity" style="text-decoration:none; text-align:center; padding:20px; border:1px solid rgba(145, 158, 171, 0.16); transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="quick-service-icon" style="width:54px; height:54px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-lightbulb"></i>
            </div>
            <div class="quick-service-title" style="font-weight:700; color:var(--palette-text-primary);">Electricity Bill</div>
            <div class="quick-service-subtitle" style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:600; margin-top:2px;">Instant Token</div>
        </a>

        <a href="{{ route('cable.index') }}" class="mk-card quick-service-item" data-category="cable" style="text-decoration:none; text-align:center; padding:20px; border:1px solid rgba(145, 158, 171, 0.16); transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="quick-service-icon" style="width:54px; height:54px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-tv"></i>
            </div>
            <div class="quick-service-title" style="font-weight:700; color:var(--palette-text-primary);">Cable TV</div>
            <div class="quick-service-subtitle" style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:600; margin-top:2px;">DSTV, GOTV</div>
        </a>

        <a href="{{ route('exam.index') }}" class="mk-card quick-service-item" data-category="exam" style="text-decoration:none; text-align:center; padding:20px; border:1px solid rgba(145, 158, 171, 0.16); transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="quick-service-icon" style="width:54px; height:54px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="quick-service-title" style="font-weight:700; color:var(--palette-text-primary);">Exam PINs</div>
            <div class="quick-service-subtitle" style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:600; margin-top:2px;">WAEC & NECO</div>
        </a>

        <a href="{{ route('airtime_cash.index') }}" class="mk-card quick-service-item" data-category="p2p" style="text-decoration:none; text-align:center; padding:20px; border:1px solid rgba(145, 158, 171, 0.16); transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="quick-service-icon" style="width:54px; height:54px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
            </div>
            <div class="quick-service-title" style="font-weight:700; color:var(--palette-text-primary);">P2P Airtime to Cash</div>
            <div class="quick-service-subtitle" style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:600; margin-top:2px;">Instant Swap</div>
        </a>

        <a href="{{ route('tier.index') }}" class="mk-card quick-service-item" data-category="account" style="text-decoration:none; text-align:center; padding:20px; border:1px solid rgba(145, 158, 171, 0.16); transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="quick-service-icon" style="width:54px; height:54px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-crown"></i>
            </div>
            <div class="quick-service-title" style="font-weight:700; color:var(--palette-text-primary);">Wholesale Tiers</div>
            <div class="quick-service-subtitle" style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:600; margin-top:2px;">{{ $user->getTierDisplayName() }}</div>
        </a>

        <a href="{{ route('referrals.index') }}" class="mk-card quick-service-item" data-category="account" style="text-decoration:none; text-align:center; padding:20px; border:1px solid rgba(145, 158, 171, 0.16); transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="quick-service-icon" style="width:54px; height:54px; border-radius:14px; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div class="quick-service-title" style="font-weight:700; color:var(--palette-text-primary);">Refer & Earn</div>
            <div class="quick-service-subtitle" style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:600; margin-top:2px;">₦{{ number_format($user->referral_balance, 2) }} Bonus</div>
        </a>
    </div>
</div>

<script>
function filterServiceCards(cat) {
    const items = document.querySelectorAll('.quick-service-item');
    items.forEach(item => {
        if (cat === 'all' || item.getAttribute('data-category') === cat) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<!-- Recent Transactions -->
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Recent Transactions</h3>
            <p class="mk-card-subtitle">Your latest top-ups and bill payments</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-outline btn-sm">View All</a>
    </div>

    @if($recentTransactions->isEmpty())
        <div style="text-align:center; padding: 36px 16px; color:var(--palette-text-secondary);">
            <i class="fa-regular fa-clipboard" style="font-size:2.5rem; color:var(--palette-text-disabled); margin-bottom:12px; display:block;"></i>
            <p>No transactions yet. Start by recharging airtime or funding your wallet.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Service</th>
                        <th>Provider / Recipient</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $tx)
                        <tr>
                            <td style="font-weight:600; font-family:monospace;">{{ $tx->reference }}</td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($tx->service_type) }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600;">{{ $tx->provider }}</div>
                                <div style="font-size:0.75rem; color:var(--palette-text-secondary);">{{ $tx->recipient }}</div>
                            </td>
                            <td style="font-weight:700;">₦{{ number_format($tx->amount, 2) }}</td>
                            <td>
                                @if($tx->status === 'successful')
                                    <span class="badge badge-success">Success</span>
                                @elseif($tx->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($tx->status === 'reversed')
                                    <span class="badge badge-info">Refunded</span>
                                @else
                                    <span class="badge badge-error">Failed</span>
                                @endif
                            </td>
                            <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <a href="{{ route('transactions.show', $tx->reference) }}" class="btn btn-outline btn-sm">Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
