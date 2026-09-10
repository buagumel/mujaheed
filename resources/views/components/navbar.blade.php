<header class="top-navbar">
    <div style="display:flex; align-items:center; gap:16px;">
        <button type="button" class="btn btn-outline btn-sm" onclick="toggleSidebar()" style="display:none;" id="customer-sidebar-toggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <span style="font-size:1.125rem; font-weight:700; color:var(--text-primary);">
            @yield('header_title', 'Welcome back, ' . (auth()->user()->name ?? 'Customer'))
        </span>
    </div>

    <div style="display:flex; align-items:center; gap:18px;">
        <!-- Quick Wallet Balance Pill -->
        <a href="{{ route('wallet.fund') }}" class="btn btn-soft-primary btn-sm" style="border-radius:var(--border-radius-full); padding:6px 14px; font-weight:700;">
            <i class="fa-solid fa-wallet"></i> ₦{{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}
            <span style="font-size:0.75rem; background:var(--color-primary); color:#FFF; padding:2px 6px; border-radius:10px; margin-left:4px;">+ Fund</span>
        </a>

        <!-- Notifications Icon -->
        <a href="{{ route('notifications.index') }}" style="position:relative; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--text-secondary); background:rgba(145, 158, 171, 0.08);">
            <i class="fa-regular fa-bell" style="font-size:1.125rem;"></i>
            @php
                $unreadCount = auth()->user()->appNotifications()->where('is_read', false)->count();
            @endphp
            @if($unreadCount > 0)
                <span style="position:absolute; top:4px; right:4px; width:18px; height:18px; background:var(--color-error); color:#FFF; font-size:0.6875rem; font-weight:700; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </a>

        <!-- User Dropdown Profile -->
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg, #1877F2 0%, #0C44AE 100%); color:#FFF; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.9375rem;">
                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
            </div>
            <div style="display:none; @media(min-width:768px){display:block;} line-height:1.2;">
                <div style="font-size:0.875rem; font-weight:700; color:var(--text-primary);">{{ auth()->user()->name }}</div>
                <div style="font-size:0.75rem; color:var(--text-secondary);">{{ auth()->user()->phone ?? auth()->user()->email }}</div>
            </div>
        </div>
    </div>
</header>

<script>
    if (window.innerWidth <= 992) {
        const toggleBtn = document.getElementById('customer-sidebar-toggle');
        if (toggleBtn) toggleBtn.style.display = 'inline-flex';
    }
</script>
