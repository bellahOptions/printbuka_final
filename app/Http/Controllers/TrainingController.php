<?php

namespace App\Http\Controllers;

use App\Mail\TrainingApplicationConfirmationMail;
use App\Mail\TrainingApplicationDecisionMail;
use App\Mail\TrainingApplicationSubmittedMail;
use App\Models\Training;
use App\Support\Turnstile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function index(): View
    {
        return view('training.index');
    }

    public function register(): View
    {
        return view('training.register', [
            'deadline' => $this->registrationDeadline(),
            'registrationClosed' => $this->registrationIsClosed(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('email')) {
            $request->merge([
                'email' => str((string) $request->input('email'))->lower()->trim()->toString(),
            ]);
        }

        if ($this->registrationIsClosed()) {
            return redirect()
                ->route('training.apply')
                ->with('closed', 'Registration for this PGTP cohort has ended. Applications closed on May 29, 2026.');
        }

        $desiredSkills = $this->desiredSkills();

        $rules = [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'date_of_birth' => ['required', 'date', 'before:-14 years'],
            'gender' => ['nullable', 'string', Rule::in(['Female', 'Male', 'Prefer not to say'])],
            'phone_whatsapp' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('trainings', 'email')],
            'contact_address' => ['required', 'string', 'max:1000'],
            'city_state' => ['required', 'string', 'max:255'],
            'educational_qualification' => ['required', 'string', 'max:255'],
            'desired_skill' => ['required', 'string', Rule::in($desiredSkills)],
            'employment_status' => ['nullable', 'string', 'max:255'],
            'experience_level' => ['nullable', 'string', 'max:255'],
            'has_laptop' => ['required', 'boolean'],
            'availability' => ['required', 'string', 'max:255'],
            'portfolio_url' => ['required', 'url', 'max:500'],
            'motivation' => ['required', 'string', 'max:2000'],
            'referral_source' => ['nullable', 'string', 'max:255'],
        ];

        if (Turnstile::enabled()) {
            $rules['cf-turnstile-response'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        unset($validated['cf-turnstile-response']);
        $validated['email'] = str($validated['email'])->lower()->trim()->toString();

        if (Training::query()->whereRaw('LOWER(email) = ?', [$validated['email']])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This email has already submitted a PGTP application.',
            ]);
        }

        if ($this->isEmployedApplicant($validated['employment_status'] ?? null)) {
            $message = $this->employedApplicantRejectionMessage();
            $application = Training::create([
                ...$validated,
                'status' => Training::STATUS_REJECTED,
                'decision_note' => $message,
                'decided_at' => now(),
            ]);

            Mail::to($application->email)->send(new TrainingApplicationDecisionMail($application));

            return back()->with('status', $message);
        }

        $application = Training::create($validated);

        Mail::to($application->email)->send(new TrainingApplicationConfirmationMail($application));

        foreach ($this->applicationNotificationRecipients() as $recipient) {
            Mail::to($recipient)->send(new TrainingApplicationSubmittedMail($application));
        }

        return back()->with('status', 'Your PGTP application has been submitted. The Printbuka team will review it and contact you shortly.');
    }

    private function desiredSkills(): array
    {
        return [
            'Graphic Design',
            'Packaging Design',
            'Social Media Management',
            'Customer Service',
        ];
    }

    private function applicationNotificationRecipients(): array
    {
        return [
            'ahmed@printbuka.com.ng',
            'tundealeiloart@gmail.com',
        ];
    }

    private function isEmployedApplicant(?string $employmentStatus): bool
    {
        return strcasecmp(trim((string) $employmentStatus), 'Employed') === 0;
    }

    private function employedApplicantRejectionMessage(): string
    {
        return 'Thank you for your interest in PGTP. This offer is only open to unemployed persons, so your application cannot proceed for this cohort.';
    }

    private function registrationDeadline(): Carbon
    {
        return Carbon::create(2026, 5, 29, 23, 59, 59, config('app.timezone'));
    }

    private function registrationIsClosed(): bool
    {
        return now()->greaterThan($this->registrationDeadline());
    }
}
