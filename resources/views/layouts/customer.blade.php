<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ \App\Models\SystemSetting::get('platform_name', 'BJ Data Sub') }}</title>

    <!-- Dynamic Favicon -->
    @php
        $favicon = \App\Models\SystemSetting::get('favicon', null);
        $faviconUrl = $favicon ? (str_starts_with($favicon, 'http') ? $favicon : asset('storage/' . $favicon)) : asset('assets/favicon.ico');
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Material Kit Design System & Global Loader -->
    <link rel="stylesheet" href="{{ asset('css/material-kit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-loader.css') }}">

    <!-- Dynamic Admin Theme Colors (Loaded After CSS to override :root) -->
    @include('components.theme-variables')

    @stack('styles')
</head>
<body class="mk-app">
    <div class="layout-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="layout-nav">
            <!-- Brand Logo Header -->
            <div class="nav-brand-header">
                @include('components.brand-logo')
            </div>

            <!-- Customer Mini Profile Card -->
            <div class="nav-user-card" style="display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:12px; background:var(--palette-background-neutral); margin-bottom:20px;">
                <img src="{{ asset('assets/images/avatar/avatar-25.webp') }}" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--palette-primary-lighter);" alt="Avatar">
                <div style="flex-grow:1; min-width:0;">
                    <div style="font-weight:700; font-size:0.875rem; color:var(--palette-text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ auth()->user()->name ?? 'Customer' }}
                    </div>
                    <div style="font-size:0.75rem; color:var(--palette-text-secondary); font-weight:700;">
                        ₦{{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}
                    </div>
                </div>
                <span class="badge badge-success" style="font-size:0.6875rem;">Active</span>
            </div>

            <!-- Nav Links List -->
            <div style="flex-grow:1; overflow-y:auto; margin-bottom:16px;">
                <div class="nav-section-header">Overview</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <img src="{{ asset('assets/icons/navbar/ic-analytics.svg') }}" style="width:22px; height:22px;" alt="">
                    </span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('wallet.index') }}" class="nav-item {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-wallet" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>My Wallet</span>
                </a>

                <div class="nav-section-header">VTU Services</div>
                <a href="{{ route('airtime.index') }}" class="nav-item {{ request()->routeIs('airtime.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-phone-volume" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Airtime Top-up</span>
                </a>
                <a href="{{ route('data.index') }}" class="nav-item {{ request()->routeIs('data.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-wifi" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Data Bundles</span>
                </a>
                <a href="{{ route('electricity.index') }}" class="nav-item {{ request()->routeIs('electricity.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-lightbulb" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Electricity Bills</span>
                </a>
                <a href="{{ route('cable.index') }}" class="nav-item {{ request()->routeIs('cable.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-tv" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Cable TV</span>
                </a>
                <a href="{{ route('exam.index') }}" class="nav-item {{ request()->routeIs('exam.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-graduation-cap" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Exam PINs</span>
                </a>
                <a href="{{ route('airtime_cash.index') }}" class="nav-item {{ request()->routeIs('airtime_cash.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-arrow-right-arrow-left" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Airtime to Cash</span>
                </a>

                <div class="nav-section-header">Account & Earnings</div>
                <a href="{{ route('tier.index') }}" class="nav-item {{ request()->routeIs('tier.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-crown" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Wholesale Tiers</span>
                </a>
                <a href="{{ route('referrals.index') }}" class="nav-item {{ request()->routeIs('referrals.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-gift" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Referrals & Earn</span>
                </a>
                <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <img src="{{ asset('assets/icons/navbar/ic-cart.svg') }}" style="width:22px; height:22px;" alt="">
                    </span>
                    <span>Transactions</span>
                </a>
                <a href="{{ route('profile.index') }}" class="nav-item {{ request()->routeIs('profile.*') || request()->routeIs('security.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <img src="{{ asset('assets/icons/navbar/ic-user.svg') }}" style="width:22px; height:22px;" alt="">
                    </span>
                    <span>My Profile</span>
                </a>
                <a href="{{ route('support.index') }}" class="nav-item {{ request()->routeIs('support.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-headset" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Customer Support</span>
                </a>
            </div>

            <!-- Download Mobile App Bottom CTA Card -->
            <div class="nav-upgrade-box" style="background: linear-gradient(135deg, rgba(24, 119, 242, 0.06) 0%, rgba(24, 119, 242, 0.14) 100%); border: 1px solid rgba(24, 119, 242, 0.18); border-radius: 14px; padding: 16px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                    <i class="fa-solid fa-mobile-screen-button" style="color:var(--palette-primary-main); font-size:1.15rem;"></i>
                    <span style="font-weight:700; font-size:0.875rem; color:var(--palette-text-primary);">Mobile App</span>
                </div>
                <div style="font-size:0.75rem; color:var(--palette-text-secondary); margin-bottom:12px;">Get {{ \App\Models\SystemSetting::get('platform_name', 'BJ Data Sub') }} on Android & iOS.</div>
                <a href="{{ route('profile.index') }}" class="btn btn-primary btn-sm btn-block" style="font-weight:700; justify-content:center; gap:6px;">
                    <i class="fa-solid fa-download"></i> Download App
                </a>
            </div>
        </aside>

        <!-- Main Layout Area -->
        <div class="layout-main">
            <!-- Header Navbar -->
            <header class="layout-header">
                <div style="display:flex; align-items:center; gap:16px;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleSidebar()" style="display:none;" id="sidebar-toggle-btn">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="header-title-text" style="font-size:1.125rem; font-weight:700; color:var(--palette-text-primary); max-width:280px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        @yield('header_title', 'Hi, ' . (auth()->user()->name ?? 'User'))
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:16px;">
                    <!-- Balance Quick Pill (Hidden on Mobile view) -->
                    <a href="{{ route('wallet.fund') }}" class="btn btn-soft btn-sm hide-on-mobile" style="border-radius:9999px; padding:6px 14px; font-weight:700;">
                        <i class="fa-solid fa-wallet"></i> ₦{{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}
                        <span style="background:var(--palette-primary-main); color:#FFF; padding:2px 6px; border-radius:10px; font-size:0.6875rem; margin-left:4px;">+ Top-up</span>
                    </a>

                    <!-- Notification Bell -->
                    <a href="{{ route('notifications.index') }}" style="position:relative; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--palette-text-secondary); background:rgba(145, 158, 171, 0.08);" title="Notifications">
                        <i class="fa-regular fa-bell" style="font-size:1.125rem;"></i>
                        @php
                            $unreadCount = auth()->user()->appNotifications()->where('is_read', false)->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span style="position:absolute; top:4px; right:4px; width:18px; height:18px; background:var(--palette-error-main); color:#FFF; font-size:0.6875rem; font-weight:700; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- User Avatar linking to Profile -->
                    <a href="{{ route('profile.index') }}" style="display:flex; align-items:center;" title="My Profile & Settings">
                        <img src="{{ asset('assets/images/avatar/avatar-25.webp') }}" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--palette-primary-lighter);" alt="Avatar">
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="layout-content page-fade-in">
                @include('components.alert')
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Drawer Overlay -->
    <div id="sidebar-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1050;" onclick="toggleSidebar()"></div>

    <!-- Transaction PIN Modal Component -->
    @include('components.pin-modal')

    <!-- Global Loader & Progress System -->
    <script src="{{ asset('js/global-loader.js') }}"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.layout-nav');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
                overlay.style.display = 'none';
            } else {
                sidebar.classList.add('mobile-open');
                overlay.style.display = 'block';
            }
        }
        if (window.innerWidth <= 992) {
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            if (toggleBtn) toggleBtn.style.display = 'inline-flex';
        }
    </script>
    @stack('scripts')
</body>
</html>
