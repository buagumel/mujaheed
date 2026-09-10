@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">Sign in to your account</h2>
    <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 4px;">Enter your credentials to continue</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
    </div>

    <div class="form-group">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" for="password" style="margin-bottom:0;">Password</label>
        </div>
        <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <label style="display:flex; align-items:center; gap:8px; font-size:0.875rem; cursor:pointer; color:var(--text-secondary);">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span>Remember me</span>
        </label>
        <a href="{{ route('password.request') }}" style="font-size:0.875rem; font-weight:600; color:var(--palette-primary-main, #1877f2);">Forgot password?</a>
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-bottom: 20px;">
        Sign In
    </button>

    <div style="text-align: center; font-size: 0.875rem; color: var(--text-secondary);">
        Don't have an account? 
        <a href="{{ route('register') }}" style="font-weight: 700;">Sign Up</a>
    </div>
</form>
@endsection
