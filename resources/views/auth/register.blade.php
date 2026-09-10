@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
@if(isset($allowRegistration) && !$allowRegistration)
    <div style="text-align:center; padding:16px 8px;">
        <div style="width:64px; height:64px; border-radius:50%; background:rgba(255, 171, 0, 0.12); color:#B76E00; display:grid; place-items:center; margin:0 auto 16px; font-size:1.75rem;">
            <i class="fa-solid fa-user-lock"></i>
        </div>
        <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom:8px;">Registration Temporarily Closed</h2>
        <p style="font-size: 0.9rem; color: var(--text-secondary); line-height:1.6; margin-bottom:24px;">
            {{ $closedMessage ?? 'New user registrations are currently closed by administrator. Please check back later or contact support if you need assistance.' }}
        </p>

        <div style="display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('login') }}" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-right-to-bracket" style="margin-right:6px;"></i> Sign In to Existing Account
            </a>
            <a href="{{ url('/') }}" class="btn btn-outline btn-block" style="border:1px solid var(--border-color); padding:10px;">
                <i class="fa-solid fa-house" style="margin-right:6px;"></i> Return to Homepage
            </a>
        </div>
    </div>
@else
    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">Create your account</h2>
        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 4px;">Get instant access to discounted VTU & bill payments</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="e.g. John Doe">
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="name@example.com">
        </div>

        <div class="form-group">
            <label class="form-label" for="phone">Phone Number (Nigerian)</label>
            <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="08012345678">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password (min 8 characters)</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="••••••••">
        </div>

        <div class="form-group">
            <label class="form-label" for="referral_code">Referral Code (Optional)</label>
            <input type="text" id="referral_code" name="referral_code" class="form-control" value="{{ old('referral_code', $ref ?? '') }}" placeholder="e.g. VTU123" style="text-transform:uppercase; letter-spacing:2px; font-weight:700;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display:flex; align-items:flex-start; gap:8px; font-size:0.8125rem; cursor:pointer; color:var(--text-secondary);">
                <input type="checkbox" name="terms" required style="margin-top:3px;">
                <span>I agree to the <a href="{{ route('terms') }}" style="font-weight:600;">Terms of Service</a> and <a href="{{ route('privacy') }}" style="font-weight:600;">Privacy Policy</a>.</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-bottom: 20px;">
            Create Account
        </button>

        <div style="text-align: center; font-size: 0.875rem; color: var(--text-secondary);">
            Already have an account? 
            <a href="{{ route('login') }}" style="font-weight: 700;">Sign In</a>
        </div>
    </form>
@endif
@endsection
