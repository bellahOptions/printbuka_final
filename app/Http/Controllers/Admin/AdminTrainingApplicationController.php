<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TrainingApplicationDecisionMail;
use App\Models\Training;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminTrainingApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $skill = $request->string('skill')->toString();
        $search = $request->string('search')->toString();

        $applications = Training::query()
            ->with('decidedBy:id,first_name,last_name,email')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($skill !== '', fn ($query) => $query->where('desired_skill', $skill))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone_whatsapp', 'like', '%'.$search.'%');
                });
            })
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'accepted' THEN 1 WHEN 'rejected' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.training.index', [
            'applications' => $applications,
            'stats' => [
                'total' => Training::query()->count(),
                'pending' => Training::query()->where('status', Training::STATUS_PENDING)->count(),
                'accepted' => Training::query()->where('status', Training::STATUS_ACCEPTED)->count(),
                'rejected' => Training::query()->where('status', Training::STATUS_REJECTED)->count(),
            ],
            'statuses' => $this->statuses(),
            'skills' => $this->skills(),
            'status' => $status,
            'skill' => $skill,
            'search' => $search,
        ]);
    }

    public function show(Training $training): View
    {
        $training->load('decidedBy:id,first_name,last_name,email');

        return view('admin.training.show', [
            'application' => $training,
            'statuses' => $this->statuses(),
        ]);
    }

    public function decide(Request $request, Training $training): RedirectResponse
    {
        if (! $training->isPending()) {
            return back()->with('status', $training->fullName().' already has a final decision: '.$training->statusLabel().'.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in([Training::STATUS_ACCEPTED, Training::STATUS_REJECTED])],
            'decision_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $training->forceFill([
            'status' => $validated['status'],
            'decision_note' => $validated['decision_note'] ?? null,
            'decided_at' => now(),
            'decided_by_id' => $request->user()?->id,
        ])->save();

        Mail::to($training->email)->send(new TrainingApplicationDecisionMail($training->fresh('decidedBy')));

        return back()->with('status', $training->fullName().' has been marked as '.$training->statusLabel().' and notified by email.');
    }

    private function statuses(): array
    {
        return [
            Training::STATUS_PENDING => 'Pending',
            Training::STATUS_ACCEPTED => 'Accepted',
            Training::STATUS_REJECTED => 'Rejected',
        ];
    }

    private function skills(): array
    {
        return [
            'Graphic Design',
            'Packaging Design',
            'Social Media Management',
            'Customer Service',
        ];
    }
}
