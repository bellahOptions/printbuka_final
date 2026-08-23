<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InternalMemoMail;
use App\Models\InternalMemo;
use App\Models\User;
use App\Support\EmailBlockRenderer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminMemoController extends Controller
{
    public function index(): View
    {
        return view('admin.memos.index', [
            'memos' => InternalMemo::query()->with('sentBy')->latest('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.memos.create', [
            'roles' => collect(config('printbuka_admin.roles', []))
                ->keys()
                ->reject(fn (string $role) => $role === 'customer')
                ->values(),
            'staffList' => User::query()
                ->where('role', '!=', 'customer')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->get(['id', 'first_name', 'last_name', 'role']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'blocks' => ['required', 'string'],
            'recipient_type' => ['required', 'in:all,role,individual'],
            'recipient_roles' => ['nullable', 'array'],
            'recipient_roles.*' => ['string'],
            'recipient_user_ids' => ['nullable', 'array'],
            'recipient_user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $blocks = json_decode($validated['blocks'], true);
        abort_unless(is_array($blocks), 422, 'Invalid block data.');

        $recipients = $this->resolveRecipients($validated);
        abort_if($recipients->isEmpty(), 422, 'No matching recipients found for this memo.');

        $memo = InternalMemo::query()->create([
            'subject' => $validated['subject'],
            'blocks' => $blocks,
            'recipient_scope' => [
                'type' => $validated['recipient_type'],
                'roles' => $validated['recipient_roles'] ?? [],
                'user_ids' => $validated['recipient_user_ids'] ?? [],
            ],
            'recipient_count' => $recipients->count(),
            'sent_by_id' => $request->user()->id,
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($recipients as $staff) {
            try {
                Mail::to($staff->email)->send(new InternalMemoMail($memo, $staff));
                $sent++;
            } catch (\Throwable $exception) {
                $failed++;

                Log::error('Internal memo email failed.', [
                    'memo_id' => $memo->id,
                    'staff_id' => $staff->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $memo->update([
            'emails_sent' => $sent,
            'emails_failed' => $failed,
            'sent_at' => now(),
        ]);

        return redirect()
            ->route('admin.memos.show', $memo)
            ->with('status', 'Memo sent to '.$sent.' of '.$recipients->count().' recipient(s).');
    }

    public function show(InternalMemo $memo): View
    {
        return view('admin.memos.show', [
            'memo' => $memo->load('sentBy'),
        ]);
    }

    public function preview(Request $request): View
    {
        $blocks = json_decode((string) $request->query('blocks', '[]'), true) ?: [];

        $sampleVariables = collect(['staff_name', 'company_name'])
            ->mapWithKeys(fn (string $variable) => [$variable => '['.str($variable)->replace('_', ' ')->title()->value().']'])
            ->all();

        return view('admin.memos.preview', [
            'bodyHtml' => EmailBlockRenderer::render($blocks, $sampleVariables),
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return Collection<int, User>
     */
    private function resolveRecipients(array $validated): Collection
    {
        return match ($validated['recipient_type']) {
            'all' => User::query()
                ->where('role', '!=', 'customer')
                ->where('is_active', true)
                ->whereNotNull('email')
                ->get(),
            'role' => User::query()
                ->whereIn('role', $validated['recipient_roles'] ?? [])
                ->where('is_active', true)
                ->whereNotNull('email')
                ->get(),
            'individual' => User::query()
                ->whereIn('id', $validated['recipient_user_ids'] ?? [])
                ->where('role', '!=', 'customer')
                ->where('is_active', true)
                ->whereNotNull('email')
                ->get(),
            default => new Collection(),
        };
    }
}
