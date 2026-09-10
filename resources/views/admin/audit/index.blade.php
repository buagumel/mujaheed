@extends('layouts.admin')

@section('title', 'Audit Trail Logs')

@section('content')
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Audit Trail & Security Logs</h3>
            <p class="mk-card-subtitle">Immutable log of administrative operations, logins, and status updates</p>
        </div>
    </div>

    <!-- Search / Filter -->
    <form method="GET" action="{{ route('admin.audit.index') }}" style="display:flex; gap:12px; margin-bottom:20px;">
        <input type="text" name="search" class="form-control" placeholder="Search audit description, IP address or user..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('admin.audit.index') }}" class="btn btn-outline">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Admin / User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td style="font-size:0.8125rem; color:var(--palette-text-secondary); white-space:nowrap;">
                            {{ $log->created_at->format('M d, Y h:i:s A') }}
                        </td>
                        <td>
                            <div style="font-weight:700;">{{ $log->user->name ?? 'System' }}</div>
                            <div style="font-size:0.75rem; color:var(--palette-text-secondary);">{{ $log->user->email ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="badge badge-info" style="font-family:monospace;">{{ $log->action }}</span>
                        </td>
                        <td style="font-size:0.875rem;">{{ $log->description }}</td>
                        <td style="font-family:monospace; font-size:0.75rem; color:var(--palette-text-secondary);">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $logs->links() }}
    </div>
</div>
@endsection
