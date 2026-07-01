<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StaffQueryIssuedMail;
use App\Models\StaffQuery;
use App\Models\User;
use App\Support\ReferenceCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminStaffQueryController extends Controller
{
    public function index(): View
    {
        abort_unless(request()->user()?->canAdmin('staff.queries') || request()->user()?->canAdmin('*'), 403);

        $q = StaffQuery::query()->with(['staff', 'issuedBy']);

        if ($status = request('status')) {
            $q->where('status', $status);
        }
        if ($type = request('type')) {
            $q->where('query_type', $type);
        }
        if ($search = request('search')) {
            $q->where(function ($sq) use ($search) {
                $sq->where('subject', 'like', "%{$search}%")
                    ->orWhere('query_number', 'like', "%{$search}%")
                    ->orWhereHas('staff', fn ($u) => $u->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        return view('admin.staff-queries.index', [
            'queries' => $q->latest()->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()?->canAdmin('staff.queries') || $request->user()?->canAdmin('*'), 403);

        return view('admin.staff-queries.create', [
            'staffList'   => User::query()->where('role', '!=', 'customer')->where('is_active', true)->orderBy('first_name')->get(),
            'queryTypes'  => $this->queryTypes(),
            'preselected' => $request->query('staff_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('staff.queries') || $request->user()?->canAdmin('*'), 403);

        $validated = $request->validate([
            'staff_id'          => ['required', 'exists:users,id'],
            'query_date'        => ['required', 'date'],
            'query_type'        => ['required', 'string', 'in:'.implode(',', StaffQuery::$types)],
            'subject'           => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string', 'max:5000'],
            'response_due_date' => ['nullable', 'date', 'after_or_equal:query_date'],
        ]);

        $query = StaffQuery::query()->create([
            ...$validated,
            'issued_by_id' => $request->user()->id,
            'query_number' => $this->generateQueryNumber(),
            'status'       => 'pending',
        ]);

        $this->sendQueryEmail($query);

        return redirect()
            ->route('admin.staff-queries.show', $query)
            ->with('status', 'Query '.$query->query_number.' issued successfully.');
    }

    public function show(StaffQuery $query): View
    {
        abort_unless(
            request()->user()?->canAdmin('staff.queries')
            || request()->user()?->canAdmin('*')
            || request()->user()?->id === $query->staff_id,
            403
        );

        return view('admin.staff-queries.show', [
            'query' => $query->load('staff', 'issuedBy', 'resolvedBy'),
        ]);
    }

    public function respond(Request $request, StaffQuery $query): RedirectResponse
    {
        abort_unless($request->user()?->id === $query->staff_id, 403);
        abort_if($query->staff_responded_at !== null, 422, 'You have already responded to this query.');

        $validated = $request->validate([
            'staff_response' => ['required', 'string', 'max:5000'],
        ]);

        $query->forceFill([
            'staff_response'     => $validated['staff_response'],
            'staff_responded_at' => now(),
            'status'             => 'responded',
        ])->save();

        return back()->with('status', 'Your response has been recorded.');
    }

    public function close(Request $request, StaffQuery $query): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('staff.queries') || $request->user()?->canAdmin('*'), 403);

        $validated = $request->validate([
            'resolution_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $query->forceFill([
            'status'           => 'closed',
            'resolution_notes' => $validated['resolution_notes'] ?? null,
            'resolved_by_id'   => $request->user()->id,
            'resolved_at'      => now(),
        ])->save();

        return back()->with('status', 'Query '.$query->query_number.' has been closed.');
    }

    private function queryTypes(): array
    {
        return StaffQuery::$types;
    }

    private function generateQueryNumber(): string
    {
        $year = now()->year;
        $last = StaffQuery::query()
            ->whereYear('created_at', $year)
            ->latest()
            ->value('query_number');

        $seq = 1;
        if ($last && preg_match('/QRY-'.$year.'-(\d+)/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return 'QRY-'.$year.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    private function sendQueryEmail(StaffQuery $query): void
    {
        $email = $query->load('staff')->staff?->email ?? '';
        if (! filled($email)) return;

        try {
            Mail::to($email)->send(new StaffQueryIssuedMail($query));
        } catch (\Throwable $e) {
            Log::error('Staff query email failed.', ['query_id' => $query->id, 'message' => $e->getMessage()]);
        }
    }
}
