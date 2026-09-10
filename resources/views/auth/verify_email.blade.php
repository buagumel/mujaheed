@extends('layouts.auth')

@section('title', 'Verify Your Email')

@section('content')
<div style="margin-bottom: 24px; text-align: center;">
    <div style="width:54px; height:54px; border-radius:50%; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 16px;">
        <i class="fa-solid fa-envelope-circle-check"></i>
    </div>
    <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--palette-text-primary); margin-bottom: 8px;">Verify Your Email</h2>
    <p style="font-size: 0.875rem; color: var(--palette-text-secondary);">
        A 6-digit verification code has been sent to <strong>{{ auth()->user()->email }}</strong>. Enter the code below to activate your account.
    </p>
</div>

<form method="POST" action="{{ route('verification.verify') }}">
    @csrf

    <div class="form-group">
        <label class="form-label" for="code">6-Digit Verification Code</label>
        <input 
            type="text" 
            id="code" 
            name="code" 
            class="form-control" 
            placeholder="123456" 
            maxlength="6" 
            required 
            autofocus
            style="font-size:1.5rem; text-align:center; letter-spacing:8px; font-weight:800; font-family:monospace;"
        >
        @error('code')
            <span style="color: var(--palette-error-main); font-size: 0.75rem; margin-top: 4px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 24px;">
        <i class="fa-solid fa-shield-check"></i> Verify & Continue
    </button>
</form>

<div style="margin-top: 24px; text-align: center; font-size: 0.875rem; border-top: 1px solid rgba(145, 158, 171, 0.16); padding-top: 20px;">
    <span style="color: var(--palette-text-secondary);">Didn't receive the email code?</span>
    <form method="POST" action="{{ route('verification.resend') }}" style="display:inline; margin-left:6px;">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm" style="font-weight:700;">
            <i class="fa-solid fa-rotate-right"></i> Resend Code
        </button>
    </form>
</div>
@endsection
