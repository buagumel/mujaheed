@extends('layouts.admin')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div style="margin-bottom:20px;">
    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Back to Helpdesk
    </a>
</div>

<div class="grid-3">
    <!-- Main Conversation Thread & Reply -->
    <div style="grid-column: span 2;">
        <!-- Ticket Information Header -->
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
                <div><strong>Submitted:</strong> {{ $ticket->created_at->format('M d, Y h:i A') }}</div>
            </div>
        </div>

        <!-- Messages Flow -->
        <div class="mk-card" style="margin-bottom:24px;">
            <h4 style="font-size:1rem; font-weight:700; margin-bottom:20px; border-bottom:1px solid rgba(145, 158, 171, 0.12); padding-bottom:10px;">
                Ticket Messages
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
                                    {{ $msg->is_admin_reply ? 'Staff: ' . $msg->user->name : $msg->user->name }}
                                    @if($msg->is_admin_reply)
                                        <span class="badge badge-info" style="margin-left:6px; font-size:0.6875rem;">Administrator</span>
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
                                        <i class="fa-solid fa-paperclip"></i> View Attached File
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Reply Box -->
        <div class="mk-card">
            <h4 style="font-size:1rem; font-weight:700; margin-bottom:16px;">Post Official Response</h4>
            <form method="POST" action="{{ route('admin.tickets.reply', $ticket->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Message Content</label>
                    <textarea name="message" class="form-control" rows="5" placeholder="Write response to customer..." required></textarea>
                </div>

                <div class="grid-2" style="margin-bottom:16px;">
                    <div class="form-group">
                        <label class="form-label">Set Ticket Status</label>
                        <select name="status" class="form-select">
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved (Solved issue)</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Attachment (Optional)</label>
                        <input type="file" name="attachment" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa-solid fa-paper-plane"></i> Send Reply & Notify Customer
                </button>
            </form>
        </div>
    </div>

    <!-- Customer Sidebar Info & Ticket Actions -->
    <div>
        <div class="mk-card" style="margin-bottom:24px;">
            <h4 style="font-size:1rem; font-weight:700; margin-bottom:16px;">Customer Details</h4>

            <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                <div style="width:44px; height:44px; border-radius:50%; background:var(--palette-primary-main); color:#FFF; display:flex; align-items:center; justify-content:center; font-size:1.125rem; font-weight:700;">
                    {{ substr($ticket->user->name ?? 'U', 0, 1) }}
                </div>
                <div>
                    <div style="font-weight:700;">{{ $ticket->user->name ?? 'N/A' }}</div>
                    <div style="font-size:0.75rem; color:var(--palette-text-secondary);">{{ $ticket->user->email ?? 'N/A' }}</div>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px; font-size:0.8125rem;">
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--palette-text-secondary);">Phone:</span>
                    <span style="font-weight:600;">{{ $ticket->user->phone ?? 'N/A' }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--palette-text-secondary);">Wallet Balance:</span>
                    <span style="font-weight:800; color:var(--palette-primary-main);">₦{{ number_format($ticket->user->wallet->balance ?? 0, 2) }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--palette-text-secondary);">Status:</span>
                    <span class="badge {{ $ticket->user->status === 'active' ? 'badge-success' : 'badge-error' }}">{{ ucfirst($ticket->user->status ?? 'N/A') }}</span>
                </div>
            </div>

            <div style="margin-top:16px;">
                <a href="{{ route('admin.users.show', $ticket->user_id) }}" class="btn btn-outline btn-sm btn-block">
                    <i class="fa-solid fa-user"></i> View Complete User Profile
                </a>
            </div>
        </div>

        <!-- Quick Status & Priority Editor -->
        <div class="mk-card">
            <h4 style="font-size:1rem; font-weight:700; margin-bottom:16px;">Ticket Control</h4>
            <form method="POST" action="{{ route('admin.tickets.status', $ticket->id) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Update Status</label>
                    <select name="status" class="form-select">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Update Priority</label>
                    <select name="priority" class="form-select">
                        <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-outline btn-sm btn-block">
                    Update Control Settings
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
