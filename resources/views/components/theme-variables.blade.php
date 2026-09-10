@php
    $primaryColor = \App\Models\SystemSetting::get('primary_color', '#1877F2');
    $secondaryColor = \App\Models\SystemSetting::get('secondary_color', '#8E33FF');

    // Function to calculate hex brightness / adjustments
    if (!function_exists('hexToRgb')) {
        function hexToRgb($hex) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            if (strlen($hex) !== 6) return [24, 119, 242];
            return [
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2))
            ];
        }
    }

    if (!function_exists('adjustBrightness')) {
        function adjustBrightness($hex, $steps) {
            // Steps should be between -255 and 255. Negative = darker, Positive = lighter
            $rgb = hexToRgb($hex);
            $newRgb = [];
            foreach ($rgb as $color) {
                $adjusted = max(0, min(255, $color + $steps));
                $newRgb[] = str_pad(dechex($adjusted), 2, '0', STR_PAD_LEFT);
            }
            return '#' . implode('', $newRgb);
        }
    }

    $primaryRgb = hexToRgb($primaryColor);
    $primaryLight = adjustBrightness($primaryColor, 40);
    $primaryDark = adjustBrightness($primaryColor, -40);
    $primaryDarker = adjustBrightness($primaryColor, -80);
    $primaryLighter = adjustBrightness($primaryColor, 120);

    $secondaryRgb = hexToRgb($secondaryColor);
    $secondaryLight = adjustBrightness($secondaryColor, 40);
    $secondaryDark = adjustBrightness($secondaryColor, -40);
@endphp

<style id="custom-theme-colors">
:root {
    --palette-primary-main: {{ $primaryColor }};
    --palette-primary-light: {{ $primaryLight }};
    --palette-primary-lighter: rgba({{ $primaryRgb[0] }}, {{ $primaryRgb[1] }}, {{ $primaryRgb[2] }}, 0.16);
    --palette-primary-dark: {{ $primaryDark }};
    --palette-primary-darker: {{ $primaryDarker }};
    
    --palette-secondary-main: {{ $secondaryColor }};
    --palette-secondary-light: {{ $secondaryLight }};
    --palette-secondary-dark: {{ $secondaryDark }};
    --palette-secondary-lighter: rgba({{ $secondaryRgb[0] }}, {{ $secondaryRgb[1] }}, {{ $secondaryRgb[2] }}, 0.16);
}

/* Dynamically adjust active states and buttons to match selected primary color */
.btn-primary {
    background-color: var(--palette-primary-main) !important;
    border-color: var(--palette-primary-main) !important;
}
.btn-primary:hover {
    background-color: var(--palette-primary-dark) !important;
    border-color: var(--palette-primary-dark) !important;
}
.btn-soft {
    background-color: rgba({{ $primaryRgb[0] }}, {{ $primaryRgb[1] }}, {{ $primaryRgb[2] }}, 0.08) !important;
    color: var(--palette-primary-main) !important;
}
.btn-soft:hover {
    background-color: rgba({{ $primaryRgb[0] }}, {{ $primaryRgb[1] }}, {{ $primaryRgb[2] }}, 0.16) !important;
}
.nav-item.active {
    background-color: rgba({{ $primaryRgb[0] }}, {{ $primaryRgb[1] }}, {{ $primaryRgb[2] }}, 0.08) !important;
    color: var(--palette-primary-main) !important;
}
.form-control:focus, .form-select:focus {
    border-color: var(--palette-primary-main) !important;
    box-shadow: 0 0 0 3px rgba({{ $primaryRgb[0] }}, {{ $primaryRgb[1] }}, {{ $primaryRgb[2] }}, 0.16) !important;
}
</style>
