<aside class="sidebar">
    <!-- Brand Logo -->
    <div style="padding: 24px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid var(--border-color);">
        <div style="width: 38px; height: 38px; background: var(--color-primary); color:#FFF; border-radius: 10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <div>
            <div style="font-size:1.125rem; font-weight:800; color:var(--text-primary); line-height:1.2;">VTU Express</div>
            <div style="font-size:0.6875rem; color:var(--text-secondary); font-weight:600;">INSTANT RECHARGE</div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div style="flex-grow:1; overflow-y:auto; padding: 12px 0;">
        <div class="nav-section-title">Overview</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-pie nav-icon"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('wallet.index') }}" class="nav-link {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
            <i class="fa-solid fa-wallet nav-icon"></i>
            <span>My Wallet</span>
        </a>

        <div class="nav-section-title">VTU Services</div>
        <a href="{{ route('airtime.index') }}" class="nav-link {{ request()->routeIs('airtime.*') ? 'active' : '' }}">
            <i class="fa-solid fa-phone-volume nav-icon"></i>
            <span>Buy Airtime</span>
        </a>
        <a href="{{ route('data.index') }}" class="nav-link {{ request()->routeIs('data.*') ? 'active' : '' }}">
            <i class="fa-solid fa-wifi nav-icon"></i>
            <span>Buy Data</span>
        </a>
        <a href="{{ route('electricity.index') }}" class="nav-link {{ request()->routeIs('electricity.*') ? 'active' : '' }}">
            <i class="fa-solid fa-lightbulb nav-icon"></i>
            <span>Electricity Bills</span>
        </a>
        <a href="{{ route('cable.index') }}" class="nav-link {{ request()->routeIs('cable.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tv nav-icon"></i>
            <span>Cable TV</span>
        </a>

        <div class="nav-section-title">History & Account</div>
        <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <i class="fa-solid fa-clock-rotate-left nav-icon"></i>
            <span>Transactions</span>
        </a>
        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fa-solid fa-bell nav-icon"></i>
            <span>Notifications</span>
        </a>
        <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear nav-icon"></i>
            <span>Profile</span>
        </a>
        <a href="{{ route('security.index') }}" class="nav-link {{ request()->routeIs('security.*') ? 'active' : '' }}">
            <i class="fa-solid fa-shield-halved nav-icon"></i>
            <span>Security & PIN</span>
        </a>
        <a href="{{ route('support.index') }}" class="nav-link {{ request()->routeIs('support.*') ? 'active' : '' }}">
            <i class="fa-solid fa-headset nav-icon"></i>
            <span>Customer Support</span>
        </a>

        @if(auth()->user()->isAdmin())
            <div class="nav-section-title" style="color:var(--color-error);">Administration</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link" style="color:var(--color-error); font-weight:700;">
                <i class="fa-solid fa-lock nav-icon"></i>
                <span>Admin Portal</span>
            </a>
        @endif
    </div>

    <!-- User & Logout Bottom Bar -->
    <div style="padding: 16px; border-top:1px solid var(--border-color); background:var(--bg-neutral);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline btn-block btn-sm" style="justify-content:flex-start;">
                <i class="fa-solid fa-arrow-right-from-bracket" style="color:var(--color-error);"></i>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</aside>
