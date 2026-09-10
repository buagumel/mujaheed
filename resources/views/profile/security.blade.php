@extends('layouts.app')

@section('title', 'Security & PIN')
@section('header_title', 'Security Settings')

@section('content')
<div style="max-width: 580px; margin: 0 auto;">
    <!-- Minimalist Tab Navigation -->
    <div style="display:flex; background:var(--palette-background-neutral); padding:4px; border-radius:12px; margin-bottom:24px; border:1px solid rgba(145, 158, 171, 0.16);">
        <button type="button" id="tabPinBtn" class="btn btn-sm" onclick="switchSecurityTab('pin')" style="flex:1; font-weight:700; border-radius:8px; background:#FFF; color:var(--palette-text-primary); box-shadow:var(--shadow-card);">
            <i class="fa-solid fa-key" style="margin-right:6px; color:var(--palette-primary-main);"></i> Transaction PIN
        </button>
        <button type="button" id="tabPasswordBtn" class="btn btn-sm" onclick="switchSecurityTab('password')" style="flex:1; font-weight:600; border-radius:8px; background:transparent; color:var(--palette-text-secondary); box-shadow:none;">
            <i class="fa-solid fa-lock" style="margin-right:6px;"></i> Account Password
        </button>
    </div>

    <!-- Live Toast Alert for OTP -->
    <div id="otpToast" style="display:none; margin-bottom:20px;" class="mk-alert mk-alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span id="otpToastMessage"></span>
    </div>

    <!-- SECTION 1: TRANSACTION PIN -->
    <div id="sectionPin" class="mk-card" style="padding:28px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid rgba(145, 158, 171, 0.12);">
            <div>
                <h3 style="font-size:1.125rem; font-weight:800; color:var(--palette-text-primary); margin-bottom:2px;">Transaction PIN</h3>
                <p style="font-size:0.8125rem; color:var(--palette-text-secondary);">4-digit code required to authorize debits and recharges</p>
            </div>
            <span class="badge {{ $user->hasTransactionPin() ? 'badge-success' : 'badge-warning' }}">
                {{ $user->hasTransactionPin() ? 'Active' : 'Not Set' }}
            </span>
        </div>

        @if(!$user->hasTransactionPin())
            <!-- SIMPLE INITIAL PIN SETUP -->
            <div style="text-align:center; padding:10px 0 20px;">
                <div style="width:52px; height:52px; border-radius:50%; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.4rem; margin:0 auto 12px;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h4 style="font-size:1rem; font-weight:700; margin-bottom:4px;">Set Your 4-Digit PIN</h4>
                <p style="font-size:0.8125rem; color:var(--palette-text-secondary); max-width:340px; margin:0 auto;">Choose a secret 4-digit code you will remember.</p>
            </div>

            <form method="POST" action="{{ route('security.pin.set') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="new_pin">Create 4-Digit PIN</label>
                    <input type="password" id="new_pin" name="pin" class="form-control" maxlength="4" placeholder="••••" required autofocus style="text-align:center; letter-spacing:10px; font-weight:800; font-size:1.5rem;">
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_pin_confirmation">Confirm 4-Digit PIN</label>
                    <input type="password" id="new_pin_confirmation" name="pin_confirmation" class="form-control" maxlength="4" placeholder="••••" required style="text-align:center; letter-spacing:10px; font-weight:800; font-size:1.5rem;">
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:20px;">
                    <i class="fa-solid fa-check"></i> Save Transaction PIN
                </button>
            </form>
        @else
            <!-- PIN ALREADY CONFIGURED: MINIMALIST TOGGLE -->
            <div style="display:flex; gap:8px; margin-bottom:20px;">
                <button type="button" id="pinModeChangeBtn" class="btn btn-sm" onclick="switchPinSubMode('change')" style="background:rgba(24, 119, 242, 0.08); color:var(--palette-primary-main); font-weight:700; border-radius:8px;">
                    Change PIN
                </button>
                <button type="button" id="pinModeResetBtn" class="btn btn-outline btn-sm" onclick="switchPinSubMode('reset')" style="font-weight:600; border-radius:8px;">
                    Forgot PIN? Reset with OTP
                </button>
            </div>

            <!-- CHANGE CURRENT PIN FORM -->
            <div id="pinChangeBox">
                <form method="POST" action="{{ route('security.pin.change') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="current_pin">Current 4-Digit PIN</label>
                        <input type="password" id="current_pin" name="current_pin" class="form-control" maxlength="4" placeholder="••••" required style="letter-spacing:6px; font-weight:800;">
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="change_pin">New PIN</label>
                            <input type="password" id="change_pin" name="pin" class="form-control" maxlength="4" placeholder="••••" required style="letter-spacing:6px; font-weight:800;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="change_pin_confirmation">Confirm PIN</label>
                            <input type="password" id="change_pin_confirmation" name="pin_confirmation" class="form-control" maxlength="4" placeholder="••••" required style="letter-spacing:6px; font-weight:800;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="pin_otp">Email Security OTP</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" id="pin_otp" name="otp" class="form-control" placeholder="6-digit code" maxlength="6" required style="letter-spacing:4px; font-weight:700; font-family:monospace;">
                            <button type="button" class="btn btn-outline" onclick="requestSecurityOtp('pin_change', this)" style="white-space:nowrap; font-size:0.8125rem; font-weight:700;">
                                <i class="fa-solid fa-paper-plane"></i> Send OTP
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:20px;">
                        Update Transaction PIN
                    </button>
                </form>
            </div>

            <!-- RESET FORGOTTEN PIN FORM -->
            <div id="pinResetBox" style="display:none;">
                <form method="POST" action="{{ route('security.pin.reset') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="reset_pin_otp">Email Security OTP</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" id="reset_pin_otp" name="otp" class="form-control" placeholder="Enter 6-digit code sent to email" maxlength="6" required style="letter-spacing:4px; font-weight:700; font-family:monospace;">
                            <button type="button" class="btn btn-outline" onclick="requestSecurityOtp('pin_reset', this)" style="white-space:nowrap; font-size:0.8125rem; font-weight:700;">
                                <i class="fa-solid fa-paper-plane"></i> Send Code
                            </button>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="reset_new_pin">New 4-Digit PIN</label>
                            <input type="password" id="reset_new_pin" name="pin" class="form-control" maxlength="4" placeholder="••••" required style="letter-spacing:6px; font-weight:800;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="reset_new_pin_conf">Confirm PIN</label>
                            <input type="password" id="reset_new_pin_conf" name="pin_confirmation" class="form-control" maxlength="4" placeholder="••••" required style="letter-spacing:6px; font-weight:800;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:20px;">
                        Reset PIN via OTP
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- SECTION 2: PASSWORD SECURITY -->
    <div id="sectionPassword" class="mk-card" style="display:none; padding:28px;">
        <div style="margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid rgba(145, 158, 171, 0.12);">
            <h3 style="font-size:1.125rem; font-weight:800; color:var(--palette-text-primary); margin-bottom:2px;">Change Password</h3>
            <p style="font-size:0.8125rem; color:var(--palette-text-secondary);">Update your login credentials with 2-step security verification</p>
        </div>

        <form method="POST" action="{{ route('security.password.update') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Re-enter password" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_otp">Email Security OTP</label>
                <div style="display:flex; gap:8px;">
                    <input type="text" id="password_otp" name="otp" class="form-control" placeholder="Enter 6-digit OTP code" maxlength="6" required style="letter-spacing:4px; font-weight:700; font-family:monospace;">
                    <button type="button" class="btn btn-outline" onclick="requestSecurityOtp('password_change', this)" style="white-space:nowrap; font-size:0.8125rem; font-weight:700;">
                        <i class="fa-solid fa-paper-plane"></i> Send OTP
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:20px;">
                <i class="fa-solid fa-lock"></i> Update Password
            </button>
        </form>
    </div>
</div>

<script>
function switchSecurityTab(tab) {
    const secPin = document.getElementById('sectionPin');
    const secPass = document.getElementById('sectionPassword');
    const btnPin = document.getElementById('tabPinBtn');
    const btnPass = document.getElementById('tabPasswordBtn');

    if (tab === 'pin') {
        secPin.style.display = 'block';
        secPass.style.display = 'none';

        btnPin.style.background = '#FFF';
        btnPin.style.color = 'var(--palette-text-primary)';
        btnPin.style.fontWeight = '700';
        btnPin.style.boxShadow = 'var(--shadow-card)';

        btnPass.style.background = 'transparent';
        btnPass.style.color = 'var(--palette-text-secondary)';
        btnPass.style.fontWeight = '600';
        btnPass.style.boxShadow = 'none';
    } else {
        secPin.style.display = 'none';
        secPass.style.display = 'block';

        btnPass.style.background = '#FFF';
        btnPass.style.color = 'var(--palette-text-primary)';
        btnPass.style.fontWeight = '700';
        btnPass.style.boxShadow = 'var(--shadow-card)';

        btnPin.style.background = 'transparent';
        btnPin.style.color = 'var(--palette-text-secondary)';
        btnPin.style.fontWeight = '600';
        btnPin.style.boxShadow = 'none';
    }
}

function switchPinSubMode(mode) {
    const changeBox = document.getElementById('pinChangeBox');
    const resetBox = document.getElementById('pinResetBox');
    const changeBtn = document.getElementById('pinModeChangeBtn');
    const resetBtn = document.getElementById('pinModeResetBtn');

    if (mode === 'change') {
        changeBox.style.display = 'block';
        resetBox.style.display = 'none';
        changeBtn.className = 'btn btn-sm';
        changeBtn.style.background = 'rgba(24, 119, 242, 0.08)';
        changeBtn.style.color = 'var(--palette-primary-main)';
        resetBtn.className = 'btn btn-outline btn-sm';
        resetBtn.style.background = 'transparent';
        resetBtn.style.color = 'var(--palette-text-primary)';
    } else {
        changeBox.style.display = 'none';
        resetBox.style.display = 'block';
        resetBtn.className = 'btn btn-sm';
        resetBtn.style.background = 'rgba(24, 119, 242, 0.08)';
        resetBtn.style.color = 'var(--palette-primary-main)';
        changeBtn.className = 'btn btn-outline btn-sm';
        changeBtn.style.background = 'transparent';
        changeBtn.style.color = 'var(--palette-text-primary)';
    }
}

function requestSecurityOtp(purpose, btn) {
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
    btn.disabled = true;

    fetch('{{ route("security.send_otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ purpose: purpose })
    })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = '✓ Sent';
        const toast = document.getElementById('otpToast');
        const msg = document.getElementById('otpToastMessage');
        msg.innerText = data.message;
        toast.style.display = 'flex';

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 30000);
    })
    .catch(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Could not send OTP. Please check your internet connection.');
    });
}
</script>
@endsection
