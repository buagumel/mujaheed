<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Services\MailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('user')->latest('last_reply_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(20);

        $counts = [
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'counts'));
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'messages.user'])->findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2'],
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
            'attachment' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $ticket = SupportTicket::with('user')->findOrFail($id);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'attachment' => $attachmentPath,
            'is_admin_reply' => true,
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'last_reply_at' => now(),
        ]);

        // Send In-App Notification to Customer
        AppNotification::create([
            'user_id' => $ticket->user_id,
            'title' => 'Support Ticket Reply',
            'message' => "Our support team replied to your ticket #{$ticket->ticket_number}: {$ticket->subject}",
            'type' => 'info',
            'is_read' => false,
        ]);

        // Send Email to Customer
        if ($ticket->user && $ticket->user->email) {
            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; padding: 24px; border: 1px solid #e0e0e0; border-radius: 12px;'>
                <h3 style='color: #1877F2; margin-top: 0;'>New Reply on Ticket #{$ticket->ticket_number}</h3>
                <p><strong>Subject:</strong> {$ticket->subject}</p>
                <div style='background: #F4F6F8; padding: 16px; border-radius: 8px; border-left: 4px solid #1877F2; margin: 16px 0;'>
                    <p style='margin: 0; white-space: pre-wrap; color: #1C252E;'>{$validated['message']}</p>
                </div>
                <p style='font-size: 13px; color: #637381;'>You can log in to your account dashboard to view and reply to this ticket.</p>
            </div>";

            MailConfigService::sendRaw($ticket->user->email, "Support Update on Ticket #{$ticket->ticket_number}", $html);
        }

        AuditLog::record('admin_ticket_reply', "Admin replied to ticket #{$ticket->ticket_number}.", Auth::id());

        return back()->with('success', 'Reply submitted and customer notified successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->update($validated);

        AuditLog::record('ticket_status_updated', "Admin updated ticket #{$ticket->ticket_number} status to {$validated['status']}.", Auth::id());

        return back()->with('success', 'Ticket updated successfully.');
    }
}
