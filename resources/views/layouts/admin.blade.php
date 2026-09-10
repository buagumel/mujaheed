<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Portal') | {{ \App\Models\SystemSetting::get('platform_name', 'VTU Express') }}</title>

    <!-- Dynamic Favicon -->
    @php
        $favicon = \App\Models\SystemSetting::get('favicon', null);
        $faviconUrl = $favicon ? (str_starts_with($favicon, 'http') ? $favicon : asset('storage/' . $favicon)) : asset('assets/favicon.ico');
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">

    <!-- Google Fonts: Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Material Kit Design System -->
    <link rel="stylesheet" href="{{ asset('css/material-kit.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Dynamic Admin Theme Colors -->
    @include('components.theme-variables')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body>
    <div class="layout-root">
        <!-- Material Kit Admin Sidebar -->
        <aside class="layout-nav">
            <!-- Dynamic Brand Logo & Mobile Close -->
            <div style="margin-bottom:20px; padding:0 8px; display:flex; align-items:center; justify-content:space-between;">
                @include('components.brand-logo', ['isAdmin' => true])
                <button type="button" class="sidebar-close-btn" onclick="toggleAdminSidebar()" title="Close Navigation">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Workspace / Admin Quick Card -->
            <div class="workspace-box">
                <img src="{{ asset('assets/images/avatar/avatar-1.webp') }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover;" alt="Avatar">
                <div style="flex-grow:1; min-width:0;">
                    <div style="font-size:0.875rem; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ auth()->user()->name }}
                    </div>
                    <div style="font-size:0.75rem; color:var(--palette-text-secondary);">Super Admin</div>
                </div>
                <span class="badge badge-error" style="font-size:0.6875rem;">Root</span>
            </div>

            <!-- Nav Links List -->
            <div style="flex-grow:1; overflow-y:auto; margin-bottom:16px;">
                <div class="nav-section-header">Core Management</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <img src="{{ asset('assets/icons/navbar/ic-analytics.svg') }}" style="width:22px; height:22px;" alt="">
                    </span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <img src="{{ asset('assets/icons/navbar/ic-user.svg') }}" style="width:22px; height:22px;" alt="">
                    </span>
                    <span>User Accounts</span>
                </a>
                <a href="{{ route('admin.wallets.index') }}" class="nav-item {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-wallet" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Wallets & Ledger</span>
                </a>
                <a href="{{ route('admin.virtual-accounts.index') }}" class="nav-item {{ request()->routeIs('admin.virtual-accounts.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-building-columns" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Virtual Accounts</span>
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="nav-item {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <img src="{{ asset('assets/icons/navbar/ic-cart.svg') }}" style="width:22px; height:22px;" alt="">
                    </span>
                    <span>All Transactions</span>
                </a>

                <div class="nav-section-header">Services & Pricing</div>
                <a href="{{ route('admin.airtime.index') }}" class="nav-item {{ request()->routeIs('admin.airtime.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-phone-volume" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Airtime Discounts</span>
                </a>
                <a href="{{ route('admin.data.index') }}" class="nav-item {{ request()->routeIs('admin.data.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-wifi" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Data Bundles Plans</span>
                </a>
                <a href="{{ route('admin.electricity.index') }}" class="nav-item {{ request()->routeIs('admin.electricity.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-lightbulb" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Electricity DISCOs</span>
                </a>
                <a href="{{ route('admin.cable.index') }}" class="nav-item {{ request()->routeIs('admin.cable.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-tv" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Cable TV Packages</span>
                </a>
                <a href="{{ route('admin.exam_pins.index') }}" class="nav-item {{ request()->routeIs('admin.exam_pins.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-graduation-cap" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Exam Scratch PINs</span>
                </a>
                <a href="{{ route('admin.airtime_cash.index') }}" class="nav-item {{ request()->routeIs('admin.airtime_cash.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-arrow-right-arrow-left" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Airtime to Cash</span>
                </a>
                <a href="{{ route('admin.pricing.index') }}" class="nav-item {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-tags" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Pricing & Profit</span>
                </a>

                <div class="nav-section-header">Marketing & Promo</div>
                <a href="{{ route('admin.coupons.index') }}" class="nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-ticket" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Coupons & Promo</span>
                </a>
                <a href="{{ route('admin.broadcast.index') }}" class="nav-item {{ request()->routeIs('admin.broadcast.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-bullhorn" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Broadcast & Marketing</span>
                </a>

                <div class="nav-section-header">Communications & Support</div>
                <a href="{{ route('admin.tickets.index') }}" class="nav-item {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-headset" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Support Helpdesk</span>
                </a>

                <div class="nav-section-header">System & Config</div>
                <a href="{{ route('admin.providers.index') }}" class="nav-item {{ request()->routeIs('admin.providers.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-server" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Providers Health</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-sliders" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>Platform Settings</span>
                </a>
                <a href="{{ route('admin.system.logs') }}" class="nav-item {{ request()->routeIs('admin.system.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <i class="fa-solid fa-terminal" style="font-size:1.1rem; color:inherit;"></i>
                    </span>
                    <span>System Logs & Backups</span>
                </a>
                <a href="{{ route('admin.audit.index') }}" class="nav-item {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
                    <span class="nav-item-icon">
                        <img src="{{ asset('assets/icons/navbar/ic-lock.svg') }}" style="width:22px; height:22px;" alt="">
                    </span>
                    <span>Audit Trail Logs</span>
                </a>
            </div>

            <!-- Material Kit Quick View Switcher -->
            <div class="nav-upgrade-box" style="background:rgba(255, 86, 48, 0.08);">
                <div style="font-weight:700; font-size:0.875rem; color:var(--palette-error-darker); margin-bottom:4px;">Customer View</div>
                <div style="font-size:0.75rem; color:var(--palette-text-secondary); margin-bottom:12px;">Test platform as regular user</div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-block">
                    <i class="fa-solid fa-arrow-left"></i> Switch to App
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="layout-main">
            <!-- Header Navbar -->
            <header class="layout-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleAdminSidebar()" id="admin-sidebar-toggle" title="Open Navigation Menu" style="padding:6px 10px;">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                    <span class="badge badge-error" style="font-size:0.75rem; padding:4px 10px; font-weight:700;">
                        <i class="fa-solid fa-shield-halved" style="margin-right:4px;"></i> ADMIN PORTAL
                    </span>
                </div>

                <div style="display:flex; align-items:center; gap:12px;">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm" title="View Customer Portal">
                        <i class="fa-solid fa-store"></i> <span style="display:inline-block;" class="admin-top-text">Customer Portal</span>
                    </a>

                    <div style="display:flex; align-items:center; gap:8px;">
                        <img src="{{ asset('assets/images/avatar/avatar-1.webp') }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid var(--palette-error-light);" alt="Avatar">
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm" title="Log Out" style="padding:6px 10px;">
                                <i class="fa-solid fa-arrow-right-from-bracket" style="color:var(--palette-error-main);"></i>
                            </button>
                        </form>
                    </div>
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
    <div id="admin-sidebar-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1150; backdrop-filter:blur(2px);" onclick="toggleAdminSidebar()"></div>

    <!-- Global Loader & Progress System -->
    <script src="{{ asset('js/global-loader.js') }}"></script>
    <script>
        function toggleAdminSidebar() {
            const sidebar = document.querySelector('.layout-nav');
            const overlay = document.getElementById('admin-sidebar-overlay');
            if (sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('mobile-open');
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }

        // Close mobile sidebar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.querySelector('.layout-nav');
                if (sidebar && sidebar.classList.contains('mobile-open')) {
                    toggleAdminSidebar();
                }
            }
        });

        // Close mobile sidebar on nav click on mobile screens
        document.querySelectorAll('.layout-nav .nav-item').forEach(function(item) {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    const sidebar = document.querySelector('.layout-nav');
                    if (sidebar && sidebar.classList.contains('mobile-open')) {
                        toggleAdminSidebar();
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
