@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div style="margin-bottom: 24px; text-align: center;">
    <div style="width:54px; height:54px; border-radius:50%; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 16px;">
        <i class="fa-solid fa-key"></i>
    </div>
    <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--palette-text-primary); margin-bottom: 8px;">Forgot Password?</h2>
    <p style="font-size: 0.875rem; color: var(--palette-text-secondary);">
        Enter your registered email address and we will send you a 6-digit security OTP code to reset your password.
    </p>
</div>

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-control" 
            value="{{ old('email') }}" 
            placeholder="yourname@example.com" 
            required 
            autofocus
        >
        @error('email')
            <span style="color: var(--palette-error-main); font-size: 0.75rem; margin-top: 4px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 24px;">
        <i class="fa-solid fa-paper-plane"></i> Send 6-Digit OTP Code
    </button>
</form>

<div style="margin-top: 24px; text-align: center; font-size: 0.875rem;">
    <a href="{{ route('login') }}" style="font-weight: 700;">
        <i class="fa-solid fa-arrow-left"></i> Back to Login
    </a>
</div>
@endsection
