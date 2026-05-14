<?php

namespace App\Http\Controllers;

use App\Mail\TrainingApplicationConfirmationMail;
use App\Mail\TrainingApplicationSubmittedMail;
use App\Models\Training;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
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
            'email' => ['required', 'email', 'max:255'],
            'contact_address' => ['required', 'string', 'max:1000'],
            'city_state' => ['required', 'string', 'max:255'],
            'educational_qualification' => ['required', 'string', 'max:255'],
            'desired_skill' => ['required', 'string', Rule::in($desiredSkills)],
            'employment_status' => ['nullable', 'string', 'max:255'],
            'experience_level' => ['nullable', 'string', 'max:255'],
            'has_laptop' => ['required', 'boolean'],
            'availability' => ['required', 'string', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'motivation' => ['required', 'string', 'max:2000'],
            'referral_source' => ['nullable', 'string', 'max:255'],
        ];

        if (app()->environment('public')) {
            $rules['cf-turnstile-response'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        if (app()->environment('public')) {
            $this->validateTurnstile($request);
        }

        unset($validated['cf-turnstile-response']);

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

    private function validateTurnstile(Request $request): void
    {
        $secret = config('services.turnstile.secret_key');

        if (! $secret) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Captcha verification is not configured.',
            ]);
        }

        $response = Http::asForm()
            ->timeout(10)
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]);

        if (! $response->ok() || ! $response->json('success')) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Captcha verification failed. Please try again.',
            ]);
        }
    }

    private function applicationNotificationRecipients(): array
    {
        return [
            'ahmed@printbuka.com.ng',
            'tundealeiloart@gmail.com',
        ];
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
