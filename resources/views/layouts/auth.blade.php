<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication') | {{ \App\Models\SystemSetting::get('platform_name', 'VTU Express') }}</title>

    <!-- Dynamic Favicon -->
    @php
        $favicon = \App\Models\SystemSetting::get('favicon', null);
        $faviconUrl = $favicon ? (str_starts_with($favicon, 'http') ? $favicon : asset('storage/' . $favicon)) : asset('assets/favicon.ico');
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/material-kit.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Dynamic Theme Colors -->
    @include('components.theme-variables')

    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #F4F6F8 0%, #E9ECEF 100%);
            padding: 24px 16px;
        }
        .auth-card {
            width: 100%;
            max-width: 460px;
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 0 2px 0 rgba(145, 158, 171, 0.2), 0 24px 48px 0 rgba(145, 158, 171, 0.16);
            padding: 40px 32px;
        }
        .auth-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card page-fade-in">
            <a href="{{ url('/') }}" class="auth-logo">
                @include('components.brand-logo')
            </a>

            @include('components.alert')

            @yield('content')
        </div>
    </div>

    <!-- Global Loader & Progress System -->
    <script src="{{ asset('js/global-loader.js') }}"></script>
</body>
</html>
