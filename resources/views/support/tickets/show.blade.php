@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->ticket_number)
@section('header_title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <a href="{{ route('tickets.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to All Tickets
        </a>

        @if($ticket->status !== 'closed')
            <form method="POST" action="{{ route('tickets.close', $ticket->id) }}">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Close this support ticket?')">
                    <i class="fa-solid fa-check"></i> Close Ticket
                </button>
            </form>
        @endif
    </div>

    <!-- Ticket Summary Card -->
    <div class="mk-card" style="margin-bottom:24px;">
        <div class="mk-card-header" style="margin-bottom:12px;">
            <div>
                <div style="font-size:0.75rem; font-family:monospace; color:var(--palette-primary-main); font-weight:700;">
                    TICKET #{{ $ticket->ticket_number }}
                </div>
                <h3 style="font-size:1.25rem; font-weight:800; color:var(--palette-text-primary); margin-top:2px;">
                    {{ $ticket->subject }}
                </h3>
            </div>
            <div>
                @if($ticket->status === 'open')
                    <span class="badge badge-info">Open</span>
                @elseif($ticket->status === 'in_progress')
                    <span class="badge badge-warning">In Progress</span>
                @elseif($ticket->status === 'resolved')
                    <span class="badge badge-success">Resolved</span>
                @else
                    <span class="badge badge-neutral">Closed</span>
                @endif
            </div>
        </div>

        <div style="display:flex; gap:16px; font-size:0.8125rem; color:var(--palette-text-secondary); border-top:1px solid rgba(145, 158, 171, 0.12); padding-top:12px;">
            <div><strong>Category:</strong> {{ ucfirst($ticket->category) }}</div>
            <div><strong>Priority:</strong> {{ ucfirst($ticket->priority) }}</div>
            <div><strong>Created:</strong> {{ $ticket->created_at->format('M d, Y h:i A') }}</div>
        </div>
    </div>

    <!-- Conversation Chat Thread -->
    <div class="mk-card" style="margin-bottom:24px;">
        <h4 style="font-size:1rem; font-weight:700; margin-bottom:20px; border-bottom:1px solid rgba(145, 158, 171, 0.12); padding-bottom:10px;">
            Conversation Thread
        </h4>

        <div style="display:flex; flex-direction:column; gap:20px;">
            @foreach($ticket->messages as $msg)
                <div style="display:flex; gap:14px; align-items:flex-start; {{ $msg->is_admin_reply ? 'background:rgba(24, 119, 242, 0.04); border-left:4px solid var(--palette-primary-main); padding:16px; border-radius:8px;' : 'padding:12px 0;' }}">
                    <div style="width:38px; height:38px; border-radius:50%; background:{{ $msg->is_admin_reply ? 'var(--palette-primary-main)' : 'var(--palette-grey-700)' }}; color:#FFF; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.875rem; flex-shrink:0;">
                        {{ $msg->is_admin_reply ? 'A' : substr($msg->user->name ?? 'U', 0, 1) }}
                    </div>

                    <div style="flex-grow:1;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <div style="font-weight:700; font-size:0.875rem;">
                                {{ $msg->is_admin_reply ? 'Official Customer Support' : $msg->user->name }}
                                @if($msg->is_admin_reply)
                                    <span class="badge badge-info" style="margin-left:6px; font-size:0.6875rem;">Staff</span>
                                @endif
                            </div>
                            <div style="font-size:0.75rem; color:var(--palette-text-secondary);">
                                {{ $msg->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>

                        <div style="font-size:0.875rem; line-height:1.6; color:var(--palette-text-primary); white-space:pre-wrap;">{{ $msg->message }}</div>

                        @if($msg->attachment)
                            <div style="margin-top:10px;">
                                <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank" style="display:inline-flex; align-items:center; gap:6px; font-size:0.75rem; font-weight:700; background:rgba(145, 158, 171, 0.12); padding:6px 12px; border-radius:6px;">
                                    <i class="fa-solid fa-paperclip"></i> View Attached Image
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Reply Form -->
    @if($ticket->status !== 'closed')
        <div class="mk-card">
            <h4 style="font-size:1rem; font-weight:700; margin-bottom:16px;">Send a Reply</h4>
            <form method="POST" action="{{ route('tickets.reply', $ticket->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <textarea name="message" class="form-control" rows="4" placeholder="Write your reply here..." required></textarea>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                    <div>
                        <input type="file" name="attachment" accept="image/png,image/jpeg,image/webp" style="font-size:0.8125rem;">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="mk-alert mk-alert-info" style="text-align:center; justify-content:center;">
            This support ticket has been closed. If you have another issue, please <a href="{{ route('tickets.create') }}" style="margin-left:4px; font-weight:700; text-decoration:underline;">open a new ticket</a>.
        </div>
    @endif
</div>
@endsection
