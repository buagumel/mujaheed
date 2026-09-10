<aside class="sidebar" style="border-right: 1px solid var(--border-color);">
    <!-- Brand Logo -->
    <div style="padding: 24px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid var(--border-color); background:linear-gradient(135deg, rgba(24, 119, 242, 0.05) 0%, rgba(142, 51, 255, 0.05) 100%);">
        <div style="width: 38px; height: 38px; background: var(--text-primary); color:#FFF; border-radius: 10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div>
            <div style="font-size:1.125rem; font-weight:800; color:var(--text-primary); line-height:1.2;">Admin Portal</div>
            <div style="font-size:0.6875rem; color:var(--color-primary); font-weight:700;">VTU SYSTEM CONTROL</div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div style="flex-grow:1; overflow-y:auto; padding: 12px 0;">
        <div class="nav-section-title">Core Management</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high nav-icon"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users nav-icon"></i>
            <span>Users Management</span>
        </a>
        <a href="{{ route('admin.wallets.index') }}" class="nav-link {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
            <i class="fa-solid fa-wallet nav-icon"></i>
            <span>Wallets & Adjustments</span>
        </a>
        <a href="{{ route('admin.virtual-accounts.index') }}" class="nav-link {{ request()->routeIs('admin.virtual-accounts.*') ? 'active' : '' }}">
            <i class="fa-solid fa-building-columns nav-icon"></i>
            <span>Virtual Accounts</span>
        </a>
        <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt nav-icon"></i>
            <span>All Transactions</span>
        </a>

        <div class="nav-section-title">Services & Pricing</div>
        <a href="{{ route('admin.airtime.index') }}" class="nav-link {{ request()->routeIs('admin.airtime.*') ? 'active' : '' }}">
            <i class="fa-solid fa-phone-volume nav-icon"></i>
            <span>Airtime Discounts</span>
        </a>
        <a href="{{ route('admin.data.index') }}" class="nav-link {{ request()->routeIs('admin.data.*') ? 'active' : '' }}">
            <i class="fa-solid fa-wifi nav-icon"></i>
            <span>Data Bundles Plans</span>
        </a>
        <a href="{{ route('admin.electricity.index') }}" class="nav-link {{ request()->routeIs('admin.electricity.*') ? 'active' : '' }}">
            <i class="fa-solid fa-lightbulb nav-icon"></i>
            <span>Electricity DISCOs</span>
        </a>
        <a href="{{ route('admin.cable.index') }}" class="nav-link {{ request()->routeIs('admin.cable.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tv nav-icon"></i>
            <span>Cable TV Packages</span>
        </a>
        <a href="{{ route('admin.pricing.index') }}" class="nav-link {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tags nav-icon"></i>
            <span>Pricing & Profit</span>
        </a>

        <div class="nav-section-title">System & Security</div>
        <a href="{{ route('admin.providers.index') }}" class="nav-link {{ request()->routeIs('admin.providers.*') ? 'active' : '' }}">
            <i class="fa-solid fa-server nav-icon"></i>
            <span>Providers Status</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fa-solid fa-sliders nav-icon"></i>
            <span>Platform Settings</span>
        </a>
        <a href="{{ route('admin.audit.index') }}" class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
            <i class="fa-solid fa-shield-virus nav-icon"></i>
            <span>Audit Trail Logs</span>
        </a>
    </div>

    <!-- User & Logout Bottom Bar -->
    <div style="padding: 16px; border-top:1px solid var(--border-color); background:var(--bg-neutral);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline btn-block btn-sm" style="justify-content:flex-start;">
                <i class="fa-solid fa-arrow-right-from-bracket" style="color:var(--color-error);"></i>
                <span>Exit Portal</span>
            </button>
        </form>
    </div>
</aside>
