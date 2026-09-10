@extends('layouts.admin')

@section('title', 'System Logs & Backups')

@section('content')
<div class="mk-card" style="margin-bottom:28px;">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">System Error Logs & Server Health</h3>
            <p class="mk-card-subtitle">Real-time Laravel application logs and database snapshot backups</p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('admin.system.backup') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-download"></i> Download Database Backup (.SQL)
            </a>
            <form method="POST" action="{{ route('admin.system.logs.clear') }}" onsubmit="return confirm('Are you sure you want to clear the application log file?')" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--palette-error-main);">
                    <i class="fa-solid fa-trash"></i> Clear Logs
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('admin.system.logs') }}" style="display:flex; gap:12px; margin-bottom:16px;">
        <input type="text" name="filter" class="form-control" placeholder="Search log keywords (e.g. error, exception, webhook, timeout)..." value="{{ request('filter') }}">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="{{ route('admin.system.logs') }}" class="btn btn-outline btn-sm">Reset</a>
    </form>

    <!-- Terminal-like Log Viewer -->
    <div style="background:#141A21; border-radius:12px; padding:20px; overflow-x:auto; max-height:550px; overflow-y:auto; font-family:monospace; font-size:0.8125rem; line-height:1.6; color:#73BAFB; border:1px solid rgba(145, 158, 171, 0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:10px; margin-bottom:14px; color:#919EAB;">
            <span><i class="fa-solid fa-terminal" style="margin-right:6px;"></i> storage/logs/laravel.log ({{ number_format($fileSize / 1024, 2) }} KB)</span>
            <span>Latest 250 Lines</span>
        </div>
        <pre style="margin:0; white-space:pre-wrap; word-break:break-all; color:#E0E6ED;">{{ $logContent }}</pre>
    </div>
</div>
@endsection
