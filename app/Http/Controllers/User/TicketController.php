<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tickets = SupportTicket::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('support.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:airtime,data,electricity,cable,billing,general'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'message' => ['required', 'string', 'min:10'],
            'attachment' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $user = Auth::user();

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'attachment' => $attachmentPath,
            'is_admin_reply' => false,
        ]);

        AuditLog::record('ticket_created', "User created support ticket #{$ticket->ticket_number}.", $user->id);

        return redirect()->route('tickets.show', $ticket->id)->with('success', 'Support ticket #' . $ticket->ticket_number . ' submitted successfully. Our team will reply shortly.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['messages.user'])
            ->firstOrFail();

        return view('support.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2'],
            'attachment' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $user = Auth::user();
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($ticket->status === 'closed') {
            return back()->withErrors(['message' => 'This ticket is closed. Please create a new ticket if you need further help.']);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'attachment' => $attachmentPath,
            'is_admin_reply' => false,
        ]);

        $ticket->update([
            'status' => 'in_progress',
            'last_reply_at' => now(),
        ]);

        return back()->with('success', 'Your reply has been sent.');
    }

    public function close($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $ticket->update(['status' => 'closed']);

        AuditLog::record('ticket_closed', "User closed support ticket #{$ticket->ticket_number}.", $user->id);

        return back()->with('success', 'Ticket closed.');
    }
}
