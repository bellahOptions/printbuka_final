<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupportController extends Controller
{
    

    public function index(): View
    {
        $user = Auth::user();
        
        $tickets = Ticket::where('user_id', $user->id)
            ->latest()
            ->paginate(10);
        
        $stats = [
            'total' => Ticket::where('user_id', $user->id)->count(),
            'open' => Ticket::where('user_id', $user->id)->where('status', 'open')->count(),
            'in_progress' => Ticket::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('user_id', $user->id)->where('status', 'resolved')->count(),
        ];
        
        return view('support.index', compact('tickets', 'stats'));
    }

    public function create(): View
    {
        return view('support.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:general,technical,billing,order,design,other',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'message' => 'required|string|min:10',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'ticket_number' => Ticket::generateTicketNumber(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        return redirect()->route('support.show', $ticket)
            ->with('success', 'Ticket #' . $ticket->ticket_number . ' created successfully. Our team will respond shortly.');
    }

    public function show(Ticket $ticket): View|RedirectResponse
    {
        if ($ticket->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('support.index')
                ->with('error', 'You do not have permission to view this ticket.');
        }
        
        $replies = $ticket->replies()->with('user')->latest()->get();
        
        return view('support.show', compact('ticket', 'replies'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('support.index')
                ->with('error', 'You cannot reply to this ticket.');
        }
        
        if ($ticket->status === 'closed') {
            return redirect()->route('support.show', $ticket)
                ->with('error', 'This ticket is closed. Please open a new ticket for further assistance.');
        }
        
        $validated = $request->validate([
            'message' => 'required|string|min:3',
        ]);
        
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_staff_reply' => Auth::user()->isAdmin(),
        ]);
        
        // Update ticket status to in_progress when customer replies
        if (!Auth::user()->isAdmin() && $ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }
        
        return redirect()->route('support.show', $ticket)
            ->with('success', 'Reply sent successfully.');
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('support.index')
                ->with('error', 'You cannot close this ticket.');
        }
        
        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
        
        return redirect()->route('support.index')
            ->with('success', 'Ticket #' . $ticket->ticket_number . ' has been closed.');
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(Request $request, string $id)
    {
        abort(404);
    }

    public function destroy(string $id)
    {
        abort(404);
    }
}