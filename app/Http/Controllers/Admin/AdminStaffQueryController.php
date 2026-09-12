<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StaffQueryIssuedMail;
use App\Models\StaffQuery;
use App\Models\StaffQueryComment;
use App\Models\User;
use App\Notifications\StaffPushNotification;
use App\Support\ExecutiveAlert;
use App\Support\ReferenceCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminStaffQueryController extends Controller
{
    public function index(): View
    {
        abort_unless(request()->user()?->canAdmin('staff.queries') || request()->user()?->canAdmin('*'), 403);

        return view('admin.staff-queries.index', [
            'filters' => [
                'status' => request('status'),
                'type' => request('type'),
                'search' => request('search'),
            ],
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
            'description'       => ['required', 'string', 'max:20000'],
            'cc_emails'         => ['nullable', 'string', 'max:2000', $this->emailListRule()],
            'bcc_emails'        => ['nullable', 'string', 'max:2000', $this->emailListRule()],
            'response_due_date' => ['nullable', 'date', 'after_or_equal:query_date'],
        ]);

        $query = StaffQuery::query()->create([
            ...$validated,
            'issued_by_id' => $request->user()->id,
            'query_number' => $this->generateQueryNumber(),
            'status'       => 'pending',
        ]);

        $this->sendQueryEmail($query);
        $this->notifyStaffQueryIssued($query);

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
            'query' => $query->load('staff', 'issuedBy', 'resolvedBy', 'comments.user'),
        ]);
    }

    public function comment(Request $request, StaffQuery $query): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('staff.queries') || $request->user()?->canAdmin('*'), 403);

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:5000'],
        ]);

        $comment = StaffQueryComment::query()->create([
            'staff_query_id' => $query->id,
            'user_id'        => $request->user()->id,
            'comment'        => $validated['comment'],
        ]);

        $this->notifyStaffQueryCommented($query, $comment, $request->user());

        return back()->with('status', 'Comment added.')->withFragment('comments');
    }

    public function respond(Request $request, StaffQuery $query): RedirectResponse
    {
        abort_unless($request->user()?->id === $query->staff_id, 403);
        abort_if($query->staff_responded_at !== null, 422, 'You have already responded to this query.');

        $validated = $request->validate([
            'staff_response' => ['required', 'string', 'max:20000'],
        ]);

        $query->forceFill([
            'staff_response'     => $validated['staff_response'],
            'staff_responded_at' => now(),
            'status'             => 'responded',
        ])->save();

        $this->notifyStaffQueryResponded($query, $request->user());

        return back()->with('status', 'Your response has been recorded.');
    }

    public function resend(Request $request, StaffQuery $query): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('staff.queries') || $request->user()?->canAdmin('*'), 403);

        $sent = $this->sendQueryEmail($query);

        return back()->with(
            $sent ? 'status' : 'warning',
            $sent
                ? 'Query '.$query->query_number.' email resent to '.$query->staff?->displayName().'.'
                : 'Could not resend — '.$query->staff?->displayName().' has no email address on file.'
        );
    }

    public function close(Request $request, StaffQuery $query): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('staff.queries') || $request->user()?->canAdmin('*'), 403);

        $validated = $request->validate([
            'resolution_notes' => ['nullable', 'string', 'max:8000'],
        ]);

        $query->forceFill([
            'status'           => 'closed',
            'resolution_notes' => $validated['resolution_notes'] ?? null,
            'resolved_by_id'   => $request->user()->id,
            'resolved_at'      => now(),
        ])->save();

        $this->notifyStaffQueryClosed($query);

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

    private function sendQueryEmail(StaffQuery $query): bool
    {
        $email = $query->load('staff')->staff?->email ?? '';
        if (! filled($email)) return false;

        try {
            Mail::to($email)
                ->cc($query->ccList())
                ->bcc($query->bccList())
                ->send(new StaffQueryIssuedMail($query));

            $query->forceFill([
                'email_last_sent_at' => now(),
                'email_send_count'   => $query->email_send_count + 1,
            ])->save();

            return true;
        } catch (\Throwable $e) {
            Log::error('Staff query email failed.', ['query_id' => $query->id, 'message' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Validation rule closure rejecting a comma/semicolon/newline-separated
     * string if it contains anything that isn't a valid email address.
     */
    private function emailListRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            foreach (preg_split('/[,;\n]+/', (string) $value) ?: [] as $token) {
                $email = trim($token);

                if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $fail("\"{$email}\" is not a valid email address.");
                }
            }
        };
    }

    private function notifyStaffQueryIssued(StaffQuery $query): void
    {
        $staff = $query->staff;
        if (! $staff) return;

        try {
            $staff->notify(new StaffPushNotification(
                title: 'Query Issued: '.$query->query_number,
                body: $query->subject,
                type: 'staff_query_issued',
                data: [
                    'query_id'   => $query->id,
                    'query_type' => $query->query_type,
                    'action_url' => route('admin.staff-queries.show', $query),
                ],
            ));
        } catch (\Throwable $e) {
            Log::error('Staff query push notification failed.', ['query_id' => $query->id, 'message' => $e->getMessage()]);
        }

        ExecutiveAlert::send(
            title: 'Staff Query Issued: '.$query->query_number,
            body: ($query->issuedBy?->displayName() ?: 'A staff member').' issued a '.$query->typeLabel().' to '.($staff->displayName()).': '.$query->subject,
            type: 'staff_query_issued_executive',
            data: ['query_id' => $query->id, 'action_url' => route('admin.staff-queries.show', $query)],
            excludeUserId: $query->issued_by_id,
        );
    }

    private function notifyStaffQueryResponded(StaffQuery $query, User $respondent): void
    {
        $issuedBy = $query->issuedBy;
        if (! $issuedBy) return;

        try {
            $issuedBy->notify(new StaffPushNotification(
                title: 'Query Response Received',
                body: $respondent->displayName().' responded to '.$query->query_number,
                type: 'staff_query_responded',
                data: [
                    'query_id'   => $query->id,
                    'action_url' => route('admin.staff-queries.show', $query),
                ],
            ));
        } catch (\Throwable $e) {
            Log::error('Staff query response push notification failed.', ['query_id' => $query->id, 'message' => $e->getMessage()]);
        }
    }

    private function notifyStaffQueryCommented(StaffQuery $query, StaffQueryComment $comment, User $commenter): void
    {
        $issuedBy = $query->issuedBy;

        if ($issuedBy && $issuedBy->id !== $commenter->id) {
            try {
                $issuedBy->notify(new StaffPushNotification(
                    title: 'New Comment on '.$query->query_number,
                    body: $commenter->displayName().' commented: '.Str::limit(strip_tags($comment->comment), 100),
                    type: 'staff_query_commented',
                    data: [
                        'query_id'   => $query->id,
                        'action_url' => route('admin.staff-queries.show', $query).'#comments',
                    ],
                ));
            } catch (\Throwable $e) {
                Log::error('Staff query comment notification failed.', ['query_id' => $query->id, 'message' => $e->getMessage()]);
            }
        }

        ExecutiveAlert::send(
            title: 'Comment on Staff Query '.$query->query_number,
            body: $commenter->displayName().' commented on a query for '.($query->staff?->displayName() ?: 'a staff member').'.',
            type: 'staff_query_commented_executive',
            data: ['query_id' => $query->id, 'action_url' => route('admin.staff-queries.show', $query).'#comments'],
            excludeUserId: $commenter->id,
        );
    }

    private function notifyStaffQueryClosed(StaffQuery $query): void
    {
        $staff = $query->staff;
        if (! $staff) return;

        try {
            $staff->notify(new StaffPushNotification(
                title: 'Query Closed: '.$query->query_number,
                body: $query->resolution_notes ?: 'This query has been closed.',
                type: 'staff_query_closed',
                data: [
                    'query_id'   => $query->id,
                    'action_url' => route('admin.staff-queries.show', $query),
                ],
            ));
        } catch (\Throwable $e) {
            Log::error('Staff query closed push notification failed.', ['query_id' => $query->id, 'message' => $e->getMessage()]);
        }
    }
}
