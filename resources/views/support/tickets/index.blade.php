@extends('layouts.app')

@section('title', 'My Support Tickets')
@section('header_title', 'Support Tickets')

@section('content')
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">My Support Tickets</h3>
            <p class="mk-card-subtitle">Track issues, questions, and replies from our support team</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Open New Ticket
        </a>
    </div>

    @if($tickets->isEmpty())
        <div style="text-align:center; padding:48px 16px; color:var(--palette-text-secondary);">
            <i class="fa-regular fa-comments" style="font-size:3rem; color:var(--palette-text-disabled); margin-bottom:12px; display:block;"></i>
            <h4 style="font-weight:700; font-size:1.125rem; margin-bottom:4px;">No support tickets found</h4>
            <p style="font-size:0.875rem; margin-bottom:20px;">If you have any questions or transaction issues, submit a ticket.</p>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">Create First Ticket</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
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
                                <div style="font-weight:700; color:var(--palette-text-primary);">{{ $t->subject }}</div>
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
                                <a href="{{ route('tickets.show', $t->id) }}" class="btn btn-outline btn-sm">
                                    View Chat
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
    @endif
</div>
@endsection
