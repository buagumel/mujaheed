@extends('layouts.auth')

@section('title', 'Admin Portal Sign In')

@section('content')
<div style="margin-bottom: 24px; text-align:center;">
    <div style="width:56px; height:56px; border-radius:12px; background:rgba(99, 102, 241, 0.1); color:#6366f1; display:inline-flex; align-items:center; justify-content:center; font-size:1.5rem; margin-bottom:12px;">
        <i class="fa-solid fa-user-shield"></i>
    </div>
    <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">Admin Portal Access</h2>
    <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 4px;">Authorized administrative personnel only</p>
</div>

@if(session('success'))
    <div style="background:rgba(34, 197, 94, 0.15); color:#166534; padding:12px; border-radius:8px; margin-bottom:16px; font-size:0.875rem; font-weight:600;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background:rgba(239, 68, 68, 0.15); color:#991b1b; padding:12px; border-radius:8px; margin-bottom:16px; font-size:0.875rem;">
        @foreach($errors->all() as $error)
            <div><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.login.post') }}">
    @csrf

    <div class="form-group" style="margin-bottom:16px;">
        <label class="form-label" for="email" style="font-weight:700;">Administrator Email</label>
        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@vtuexpress.com">
    </div>

    <div class="form-group" style="margin-bottom:16px;">
        <label class="form-label" for="password" style="font-weight:700;">Password</label>
        <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <label style="display:flex; align-items:center; gap:8px; font-size:0.875rem; cursor:pointer; color:var(--text-secondary);">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span>Remember Administrator Session</span>
        </label>
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-bottom: 20px; background:#4f46e5; border-color:#4f46e5;">
        <i class="fa-solid fa-lock-open" style="margin-right:6px;"></i> Sign In to Admin Control Center
    </button>
</form>
@endsection
