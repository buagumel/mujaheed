@extends('layouts.app')

@section('title', 'My Profile')
@section('header_title', 'User Profile & Security')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">

    <!-- PROFILE DETAILS CARD -->
    <div class="mk-card" style="margin-bottom: 24px; border-radius: 18px; padding: 28px;">
        <div class="mk-card-header" style="margin-bottom: 20px;">
            <div>
                <h3 class="mk-card-title">Profile Information</h3>
                <p class="mk-card-subtitle">Manage your personal account details</p>
            </div>
            <span class="badge badge-success" style="font-weight: 700;">Active Account</span>
        </div>

        <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid rgba(145, 158, 171, 0.16);">
            <img src="{{ asset('assets/images/avatar/avatar-25.webp') }}" style="width:64px; height:64px; border-radius:50%; object-fit:cover; border:3px solid var(--palette-primary-lighter);" alt="Avatar">
            <div>
                <div style="font-size:1.15rem; font-weight:800; color:var(--palette-text-primary);">{{ $user->name }}</div>
                <div style="font-size:0.8125rem; color:var(--palette-text-secondary);">Member since {{ $user->created_at->format('M Y') }}</div>
                <div style="margin-top:6px; display:flex; gap:6px; align-items:center;">
                    <span class="badge" style="background:var(--palette-primary-main); color:#FFF; font-weight:700;">{{ $user->getTierDisplayName() }}</span>
                    <span class="badge badge-neutral" style="text-transform:capitalize;">{{ $user->role }}</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled style="background:var(--palette-background-neutral); cursor:not-allowed;">
                <span style="font-size:0.75rem; color:var(--palette-text-secondary); margin-top:4px; display:block;">Email address cannot be changed.</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:16px; font-weight:700;">
                <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
            </button>
        </form>
    </div>

    <!-- SECURITY & TRANSACTION PIN CARD -->
    <div class="mk-card" style="margin-bottom: 24px; border-radius: 18px; padding: 24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:44px; height:44px; border-radius:12px; background:rgba(24, 119, 242, 0.08); display:flex; align-items:center; justify-content:center; color:var(--palette-primary-main); font-size:1.2rem;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h4 style="font-size:1rem; font-weight:700; color:var(--palette-text-primary); margin:0;">Security & Transaction PIN</h4>
                    <p style="font-size:0.8125rem; color:var(--palette-text-secondary); margin:2px 0 0;">Manage your 4-digit transaction PIN and account password</p>
                </div>
            </div>
            <a href="{{ route('security.index') }}" class="btn btn-outline btn-sm" style="font-weight:700; gap:6px;">
                <i class="fa-solid fa-lock"></i> Manage PIN & Password
            </a>
        </div>
    </div>

    <!-- LOGOUT CARD -->
    <div class="mk-card" style="border-radius: 18px; padding: 20px; border: 1px solid rgba(255, 86, 48, 0.2); background: rgba(255, 86, 48, 0.02);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div>
                <div style="font-weight:700; font-size:0.9375rem; color:var(--palette-error-main);">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out of Your Account
                </div>
                <div style="font-size:0.8125rem; color:var(--palette-text-secondary); margin-top:2px;">
                    End your current session on this device securely.
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-error btn-sm" style="font-weight:700; padding:8px 20px; gap:6px;">
                    <i class="fa-solid fa-power-off"></i> Log Out
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
