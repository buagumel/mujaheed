@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div style="margin-bottom: 24px; text-align: center;">
    <div style="width:54px; height:54px; border-radius:50%; background:var(--palette-success-lighter); color:var(--palette-success-dark); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 16px;">
        <i class="fa-solid fa-lock-open"></i>
    </div>
    <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--palette-text-primary); margin-bottom: 8px;">Reset Your Password</h2>
    <p style="font-size: 0.875rem; color: var(--palette-text-secondary);">
        Enter the 6-digit OTP code sent to <strong>{{ $email }}</strong> and create a new secure password.
    </p>
</div>

@if(session('success'))
    <div style="background:rgba(34, 197, 94, 0.15); color:#15803d; padding:12px; border-radius:8px; margin-bottom:16px; font-size:0.875rem; font-weight:600; text-align:center;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

@if(session('warning'))
    <div style="background:rgba(234, 179, 8, 0.15); color:#854d0e; padding:12px; border-radius:8px; margin-bottom:16px; font-size:0.875rem; font-weight:600; text-align:center;">
        <i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="email" value="{{ $email }}">

    <div class="form-group">
        <label class="form-label" for="code">6-Digit Security OTP Code</label>
        <input 
            type="text" 
            id="code" 
            name="code" 
            class="form-control" 
            placeholder="123456" 
            maxlength="6" 
            required 
            autofocus
            style="font-size:1.4rem; text-align:center; letter-spacing:6px; font-weight:800; font-family:monospace;"
        >
        @error('code')
            <span style="color: var(--palette-error-main); font-size: 0.75rem; margin-top: 4px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password">New Password</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-control" 
            placeholder="Min. 8 characters" 
            required
        >
        @error('password')
            <span style="color: var(--palette-error-main); font-size: 0.75rem; margin-top: 4px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirm New Password</label>
        <input 
            type="password" 
            id="password_confirmation" 
            name="password_confirmation" 
            class="form-control" 
            placeholder="Re-enter password" 
            required
        >
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 24px;">
        <i class="fa-solid fa-check"></i> Set New Password
    </button>
</form>

<div style="margin-top: 24px; text-align: center; font-size: 0.875rem; display:flex; flex-direction:column; gap:12px; align-items:center;">
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button type="submit" class="btn btn-outline btn-sm" style="font-weight:600;">
            <i class="fa-solid fa-rotate"></i> Didn't receive code? Resend OTP
        </button>
    </form>

    <a href="{{ route('login') }}" style="font-weight: 700; color:var(--palette-text-secondary);">
        <i class="fa-solid fa-arrow-left"></i> Back to Login
    </a>
</div>
@endsection
