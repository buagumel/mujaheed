@extends('layouts.app')

@section('title', 'Notifications')
@section('header_title', 'In-App Notifications')

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Notifications</h3>
                <p class="mk-card-subtitle">Real-time alerts for top-ups, wallet credits, and security updates</p>
            </div>
            @if($notifications->total() > 0)
                <form method="POST" action="{{ route('notifications.mark_read') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-check-double"></i> Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        @if($notifications->isEmpty())
            <div style="text-align:center; padding: 48px 16px; color:var(--text-secondary);">
                <i class="fa-regular fa-bell-slash" style="font-size:3rem; color:var(--text-disabled); margin-bottom:12px; display:block;"></i>
                <p style="font-size:1.125rem; font-weight:600; margin-bottom:4px;">No Notifications</p>
                <p style="font-size:0.875rem;">You are all caught up with your activities.</p>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach($notifications as $notif)
                    <div style="padding:16px; border-radius:var(--border-radius-sm); border:1px solid var(--border-color); background:{{ $notif->is_read ? 'var(--bg-paper)' : 'rgba(24, 119, 242, 0.04)' }}; display:flex; gap:14px; align-items:flex-start;">
                        <div style="width:38px; height:38px; border-radius:50%; background:{{ $notif->type === 'wallet' ? 'var(--color-success-light)' : ($notif->type === 'vtu' ? 'var(--color-primary-light)' : 'var(--color-warning-light)') }}; color:{{ $notif->type === 'wallet' ? 'var(--color-success-dark)' : ($notif->type === 'vtu' ? 'var(--color-primary-dark)' : 'var(--color-warning-dark)') }}; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1rem;">
                            @if($notif->type === 'wallet')
                                <i class="fa-solid fa-wallet"></i>
                            @elseif($notif->type === 'vtu')
                                <i class="fa-solid fa-bolt"></i>
                            @else
                                <i class="fa-solid fa-shield-halved"></i>
                            @endif
                        </div>

                        <div style="flex-grow:1;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <div style="font-weight:700; color:var(--text-primary); font-size:0.9375rem;">{{ $notif->title }}</div>
                                <span style="font-size:0.75rem; color:var(--text-disabled);">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <p style="font-size:0.875rem; color:var(--text-secondary); margin-bottom:8px;">{{ $notif->message }}</p>
                            @if($notif->action_url)
                                <a href="{{ $notif->action_url }}" class="btn btn-outline btn-sm">View Details</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:20px;">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
