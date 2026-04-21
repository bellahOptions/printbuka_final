<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Services\SupportTicketNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $baseQuery = Ticket::query()
            ->with(['user:id,first_name,last_name,email,role', 'assignedStaff:id,first_name,last_name,email,role'])
            ->when(
                $this->isResolver($user),
                fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('role', '!=', 'customer')),
                fn ($query) => $query->where('user_id', $user->id)
            );

        $tickets = (clone $baseQuery)
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'open' => (clone $baseQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'resolved' => (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }

    public function create(): View
    {
        return view('admin.support.create');
    }

    public function store(Request $request, SupportTicketNotificationService $supportTicketNotificationService): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:general,technical,billing,order,design,other'],
            'priority' => ['required', 'string', 'in:low,normal,high,urgent'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        $ticket = Ticket::query()->create([
            'user_id' => $request->user()->id,
            'ticket_number' => Ticket::generateTicketNumber(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'message' => $validated['message'],
            'status' => 'open',
            'assigned_to' => $this->resolveAssigneeId($request->user()),
        ]);
        $supportTicketNotificationService->notifyTicketRaised($ticket, (int) $request->user()->id);

        return redirect()
            ->route('admin.support.show', $ticket)
            ->with('status', 'Ticket #'.$ticket->ticket_number.' created and routed to super admin / IT.');
    }

    public function show(Request $request, Ticket $ticket): View|RedirectResponse
    {
        if (! $this->canViewTicket($request->user(), $ticket)) {
            return redirect()
                ->route('admin.support.index')
                ->with('warning', 'You do not have permission to view this ticket.');
        }

        $replies = $ticket->replies()
            ->with('user:id,first_name,last_name,email,role')
            ->oldest()
            ->get();

        return view('admin.support.show', compact('ticket', 'replies'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();

        if (! $this->canViewTicket($user, $ticket)) {
            return redirect()
                ->route('admin.support.index')
                ->with('warning', 'You cannot reply to this ticket.');
        }

        if ($ticket->status === 'closed') {
            return redirect()
                ->route('admin.support.show', $ticket)
                ->with('warning', 'This ticket is already closed.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:3'],
        ]);

        TicketReply::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_staff_reply' => true,
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()
            ->route('admin.support.show', $ticket)
            ->with('status', 'Reply added successfully.');
    }

    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $this->canViewTicket($request->user(), $ticket)) {
            return redirect()
                ->route('admin.support.index')
                ->with('warning', 'You cannot close this ticket.');
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()
            ->route('admin.support.index')
            ->with('status', 'Ticket #'.$ticket->ticket_number.' has been closed.');
    }

    private function isResolver(?User $user): bool
    {
        return in_array((string) ($user?->role ?? ''), ['super_admin', 'it'], true);
    }

    private function canViewTicket(?User $user, Ticket $ticket): bool
    {
        if (! $user) {
            return false;
        }

        if ($ticket->user_id === $user->id || $ticket->assigned_to === $user->id) {
            return true;
        }

        return $this->isResolver($user)
            && $ticket->user()->where('role', '!=', 'customer')->exists();
    }

    private function resolveAssigneeId(User $creator): ?int
    {
        return User::query()
            ->where('is_active', true)
            ->whereIn('role', ['super_admin', 'it'])
            ->whereKeyNot($creator->id)
            ->orderByRaw("CASE WHEN role = 'super_admin' THEN 0 ELSE 1 END")
            ->value('id');
    }
}
