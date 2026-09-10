@extends('layouts.admin')

@section('title', 'Support Tickets Helpdesk')

@section('content')
<div class="mk-card" style="margin-bottom:28px;">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Customer Support Helpdesk</h3>
            <p class="mk-card-subtitle">Manage customer complaints, transaction disputes, and technical queries</p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid-4" style="margin-bottom:24px;">
        <div class="glass-widget primary">
            <div class="glass-widget-total">{{ $counts['open'] }}</div>
            <div class="glass-widget-title">Open Tickets</div>
        </div>
        <div class="glass-widget warning">
            <div class="glass-widget-total">{{ $counts['in_progress'] }}</div>
            <div class="glass-widget-title">In Progress</div>
        </div>
        <div class="glass-widget success">
            <div class="glass-widget-total">{{ $counts['resolved'] }}</div>
            <div class="glass-widget-title">Resolved</div>
        </div>
        <div class="glass-widget secondary">
            <div class="glass-widget-total">{{ $counts['closed'] }}</div>
            <div class="glass-widget-title">Closed</div>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <form method="GET" action="{{ route('admin.tickets.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
        <div style="flex:1; min-width:200px;">
            <input type="text" name="search" class="form-control" placeholder="Search ticket #, subject, customer name or email..." value="{{ request('search') }}">
        </div>

        <div style="width:140px;">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>

        <div style="width:140px;">
            <select name="priority" class="form-select" onchange="this.form.submit()">
                <option value="">All Priorities</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>

        <div style="width:140px;">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <option value="airtime" {{ request('category') === 'airtime' ? 'selected' : '' }}>Airtime</option>
                <option value="data" {{ request('category') === 'data' ? 'selected' : '' }}>Data</option>
                <option value="electricity" {{ request('category') === 'electricity' ? 'selected' : '' }}>Electricity</option>
                <option value="cable" {{ request('category') === 'cable' ? 'selected' : '' }}>Cable TV</option>
                <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>Billing</option>
                <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Customer</th>
                    <th>Subject & Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Last Activity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $t)
                    <tr>
                        <td style="font-family:monospace; font-weight:700; color:var(--palette-primary-main);">
                            #{{ $t->ticket_number }}
                        </td>
                        <td>
                            <div style="font-weight:700;">{{ $t->user->name ?? 'Deleted User' }}</div>
                            <div style="font-size:0.75rem; color:var(--palette-text-secondary);">{{ $t->user->email ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600; color:var(--palette-text-primary);">{{ $t->subject }}</div>
                            <div style="font-size:0.75rem; color:var(--palette-text-secondary); text-transform:uppercase;">Category: {{ $t->category }}</div>
                        </td>
                        <td>
                            @if($t->priority === 'urgent')
                                <span class="badge badge-error">Urgent</span>
                            @elseif($t->priority === 'high')
                                <span class="badge badge-warning">High</span>
                            @else
                                <span class="badge badge-neutral">{{ ucfirst($t->priority) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($t->status === 'open')
                                <span class="badge badge-info">Open</span>
                            @elseif($t->status === 'in_progress')
                                <span class="badge badge-warning">In Progress</span>
                            @elseif($t->status === 'resolved')
                                <span class="badge badge-success">Resolved</span>
                            @else
                                <span class="badge badge-neutral">Closed</span>
                            @endif
                        </td>
                        <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">
                            {{ $t->last_reply_at ? $t->last_reply_at->diffForHumans() : $t->created_at->diffForHumans() }}
                        </td>
                        <td>
                            <a href="{{ route('admin.tickets.show', $t->id) }}" class="btn btn-outline btn-sm">
                                Open Ticket
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
