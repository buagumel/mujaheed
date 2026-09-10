<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiSupportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tickets = $request->user()->supportTickets()->latest()->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $tickets,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:payment,vtu,account,other'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
            'message' => ['required', 'string'],
        ]);

        $user = $request->user();

        $ticket = $user->supportTickets()->create([
            'ticket_id' => 'TKT-' . strtoupper(Str::random(8)),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_admin' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Support ticket created successfully.',
            'data' => $ticket->load('messages'),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $ticket = $request->user()->supportTickets()->with('messages.user')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $ticket,
        ]);
    }

    public function reply(Request $request, int $id): JsonResponse
    {
        $ticket = $request->user()->supportTickets()->findOrFail($id);

        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $msg = $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_admin' => false,
        ]);

        $ticket->update(['status' => 'open']);

        return response()->json([
            'status' => 'success',
            'message' => 'Reply added successfully.',
            'data' => $msg,
        ]);
    }
}
