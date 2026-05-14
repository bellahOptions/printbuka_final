@extends('layouts.theme')

@section('title', 'Apply for PGTP | Printbuka')
@section('meta_description', 'Register for the Printbuka Graduate Trainee Program and choose your preferred practical training skill path.')

@php
    $showTurnstile = app()->environment('public') && filled(config('services.turnstile.site_key'));
    $registrationClosed = $registrationClosed ?? false;
    $deadline = $deadline ?? now();
    $desiredSkills = [
        'Graphic Design',
        'Packaging Design',
        'Social Media Management',
        'Customer Service',
    ];
@endphp

@if ($showTurnstile)
    @push('head')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush
@endif

@section('content')
    <main class="bg-slate-50 text-slate-950">
        <section class="relative overflow-hidden bg-slate-950 py-16 text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(236,72,153,0.32),transparent_34%),radial-gradient(circle_at_80%_10%,rgba(34,211,238,0.22),transparent_30%)]"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <h1 class="mt-6 text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                        {{ $registrationClosed ? 'PGTP registration has closed.' : 'Apply for the next PGTP cohort.' }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg font-semibold leading-8 text-slate-300">
                        {{ $registrationClosed ? 'Applications for this cohort are no longer being accepted.' : 'Tell us who you are, the skill you want to learn, and why this training matters to your next step.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="py-14">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.78fr_1.22fr] lg:px-8">
                <aside class="h-fit rounded-lg border border-slate-200 bg-white p-6 shadow-sm lg:sticky lg:top-28">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-600">Application checklist</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950">Before you submit</h2>
                    <p class="mt-4 text-sm font-semibold leading-7 text-slate-600">
                        Use an email and WhatsApp number you can access. Shortlisted applicants may be contacted for screening.
                    </p>

                    <div class="mt-6 rounded-lg border border-pink-100 bg-pink-50 p-4" data-countdown data-deadline="{{ $deadline->toIso8601String() }}">
                        <p class="text-xs font-black uppercase tracking-wide text-pink-700">Time left to apply</p>
                        <div class="mt-3 grid grid-cols-4 gap-2 text-center">
                            @foreach (['days' => 'Days', 'hours' => 'Hrs', 'minutes' => 'Mins', 'seconds' => 'Secs'] as $key => $label)
                                <div class="rounded-md bg-white p-2 shadow-sm">
                                    <p data-countdown-{{ $key }} class="text-xl font-black tabular-nums text-slate-950">00</p>
                                    <p class="mt-1 text-[0.6rem] font-black uppercase tracking-wide text-slate-500">{{ $label }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p data-countdown-status class="mt-3 text-xs font-bold leading-5 text-pink-800">
                            Submit before {{ $deadline->format('F j, Y g:i A') }}.
                        </p>
                    </div>

                    <div class="mt-6 space-y-3">
                        @foreach ([
                            'Applications close '.$deadline->format('F j, Y').'.',
                            'Training is free for selected applicants.',
                            'You must be available for the full program.',
                            'Practical assignments are part of the selection process.',
                        ] as $note)
                            <div class="rounded-md bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
                                {{ $note }}
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 rounded-lg bg-slate-950 p-5 text-white">
                        <p class="text-xs font-black uppercase tracking-wide text-cyan-200">Available tracks</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($desiredSkills as $skill)
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-slate-200">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                    @if ($registrationClosed)
                        <div class="rounded-lg border border-pink-100 bg-pink-50 p-6 sm:p-8">
                            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Registration closed</p>
                            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950">This PGTP application window has ended.</h2>
                            <p class="mt-4 text-base font-semibold leading-7 text-slate-700">
                                Applications for this cohort closed on {{ $deadline->format('F j, Y') }}. Thank you for your interest in the Printbuka Graduate Trainee Program.
                            </p>
                            @if (session('closed'))
                                <div class="mt-5 rounded-md border border-pink-200 bg-white p-4 text-sm font-bold text-pink-800">
                                    {{ session('closed') }}
                                </div>
                            @endif
                            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                <a href="{{ route('training') }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-slate-950 px-5 text-sm font-black text-white transition hover:bg-slate-800">
                                    Back to Training Page
                                </a>
                                <a href="{{ route('home') }}" class="inline-flex min-h-12 items-center justify-center rounded-md border border-slate-200 bg-white px-5 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">
                                    Visit Printbuka
                                </a>
                            </div>
                        </div>
                    @else
                    <div class="mb-8">
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-600">Registration form</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Your application details</h2>
                        <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">
                            Fields marked with an asterisk are required.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 rounded-md border border-pink-200 bg-pink-50 p-4 text-sm font-bold text-pink-800">
                            Please check the highlighted fields and try again.
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('training.store') }}" method="POST" class="space-y-7">
                        @csrf

                        <div>
                            <h3 class="text-lg font-black text-slate-950">Personal information</h3>
                            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="first_name" class="text-sm font-black text-slate-800">First name *</label>
                                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('first_name') border-pink-400 @enderror" required data-live-required data-helper="first-name-helper" />
                                    <p id="first-name-helper" class="mt-2 text-xs font-semibold text-slate-500">Use your legal first name.</p>
                                    @error('first_name') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="text-sm font-black text-slate-800">Last name *</label>
                                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('last_name') border-pink-400 @enderror" required data-live-required data-helper="last-name-helper" />
                                    <p id="last-name-helper" class="mt-2 text-xs font-semibold text-slate-500">Use your surname or family name.</p>
                                    @error('last_name') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="date_of_birth" class="text-sm font-black text-slate-800">Date of birth *</label>
                                    <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('date_of_birth') border-pink-400 @enderror" required data-age-helper="dob-helper" />
                                    <p id="dob-helper" class="mt-2 text-xs font-semibold text-slate-500">Applicants must be at least 14 years old.</p>
                                    @error('date_of_birth') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="gender" class="text-sm font-black text-slate-800">Gender</label>
                                    <select id="gender" name="gender" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('gender') border-pink-400 @enderror">
                                        <option value="">Select one</option>
                                        @foreach (['Female', 'Male', 'Prefer not to say'] as $gender)
                                            <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
                                        @endforeach
                                    </select>
                                    @error('gender') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-950">Contact details</h3>
                            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="phone_whatsapp" class="text-sm font-black text-slate-800">Phone / WhatsApp *</label>
                                    <input id="phone_whatsapp" name="phone_whatsapp" type="tel" value="{{ old('phone_whatsapp') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('phone_whatsapp') border-pink-400 @enderror" required data-phone-helper="phone-helper" />
                                    <p id="phone-helper" class="mt-2 text-xs font-semibold text-slate-500">Enter a reachable WhatsApp number, including country code if outside Nigeria.</p>
                                    @error('phone_whatsapp') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="email" class="text-sm font-black text-slate-800">Email address *</label>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('email') border-pink-400 @enderror" required data-email-helper="email-helper" />
                                    <p id="email-helper" class="mt-2 text-xs font-semibold text-slate-500">Use an email you check often.</p>
                                    @error('email') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="city_state" class="text-sm font-black text-slate-800">City / State *</label>
                                    <input id="city_state" name="city_state" type="text" value="{{ old('city_state') }}" placeholder="Lagos, Lagos State" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('city_state') border-pink-400 @enderror" required data-live-required data-helper="city-helper" />
                                    <p id="city-helper" class="mt-2 text-xs font-semibold text-slate-500">Example: Ikeja, Lagos State.</p>
                                    @error('city_state') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="contact_address" class="text-sm font-black text-slate-800">Contact address *</label>
                                    <textarea id="contact_address" name="contact_address" rows="3" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('contact_address') border-pink-400 @enderror" required data-live-required data-helper="address-helper">{{ old('contact_address') }}</textarea>
                                    <p id="address-helper" class="mt-2 text-xs font-semibold text-slate-500">Share enough detail for contact or screening follow-up.</p>
                                    @error('contact_address') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-950">Training preference</h3>
                            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="educational_qualification" class="text-sm font-black text-slate-800">Educational qualification *</label>
                                    <select id="educational_qualification" name="educational_qualification" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('educational_qualification') border-pink-400 @enderror" required data-live-required data-helper="qualification-helper">
                                        <option value="">Select qualification</option>
                                        @foreach (['SSCE', 'OND', 'NCE', 'HND', 'Bachelor degree', 'Master degree', 'Other'] as $qualification)
                                            <option value="{{ $qualification }}" @selected(old('educational_qualification') === $qualification)>{{ $qualification }}</option>
                                        @endforeach
                                    </select>
                                    <p id="qualification-helper" class="mt-2 text-xs font-semibold text-slate-500">Select your highest completed qualification.</p>
                                    @error('educational_qualification') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="desired_skill" class="text-sm font-black text-slate-800">Desired skill *</label>
                                    <select id="desired_skill" name="desired_skill" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('desired_skill') border-pink-400 @enderror" required data-skill-helper="skill-helper">
                                        <option value="">Select a track</option>
                                        @foreach ($desiredSkills as $skill)
                                            <option value="{{ $skill }}" @selected(old('desired_skill') === $skill)>{{ $skill }}</option>
                                        @endforeach
                                    </select>
                                    <p id="skill-helper" class="mt-2 text-xs font-semibold text-slate-500">Pick the track you are most ready to commit to.</p>
                                    @error('desired_skill') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="employment_status" class="text-sm font-black text-slate-800">Current status</label>
                                    <select id="employment_status" name="employment_status" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('employment_status') border-pink-400 @enderror">
                                        <option value="">Select one</option>
                                        @foreach (['Student', 'Fresh graduate', 'Unemployed', 'Employed', 'Self-employed'] as $status)
                                            <option value="{{ $status }}" @selected(old('employment_status') === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('employment_status') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="experience_level" class="text-sm font-black text-slate-800">Experience level</label>
                                    <select id="experience_level" name="experience_level" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('experience_level') border-pink-400 @enderror">
                                        <option value="">Select one</option>
                                        @foreach (['Beginner', 'Some practice', 'Intermediate', 'Already working in this area'] as $level)
                                            <option value="{{ $level }}" @selected(old('experience_level') === $level)>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                    @error('experience_level') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="has_laptop" class="text-sm font-black text-slate-800">Do you have a laptop? *</label>
                                    <select id="has_laptop" name="has_laptop" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('has_laptop') border-pink-400 @enderror" required data-laptop-helper="laptop-helper">
                                        <option value="">Select one</option>
                                        <option value="1" @selected(old('has_laptop') === '1')>Yes</option>
                                        <option value="0" @selected(old('has_laptop') === '0')>No</option>
                                    </select>
                                    <p id="laptop-helper" class="mt-2 text-xs font-semibold text-slate-500">Some tracks may require regular practice outside class hours.</p>
                                    @error('has_laptop') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="availability" class="text-sm font-black text-slate-800">Availability *</label>
                                    <select id="availability" name="availability" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('availability') border-pink-400 @enderror" required data-live-required data-helper="availability-helper">
                                        <option value="">Select one</option>
                                        @foreach (['Weekdays', 'Weekends', 'Flexible', 'Not sure yet'] as $availability)
                                            <option value="{{ $availability }}" @selected(old('availability') === $availability)>{{ $availability }}</option>
                                        @endforeach
                                    </select>
                                    <p id="availability-helper" class="mt-2 text-xs font-semibold text-slate-500">Tell us when you can consistently attend training.</p>
                                    @error('availability') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-950">Final details</h3>
                            <div class="mt-4 grid gap-5">
                                <div>
                                    <label for="portfolio_url" class="text-sm font-black text-slate-800">Portfolio or social link</label>
                                    <input id="portfolio_url" name="portfolio_url" type="url" value="{{ old('portfolio_url') }}" placeholder="https://..." class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('portfolio_url') border-pink-400 @enderror" data-url-helper="portfolio-helper" />
                                    <p id="portfolio-helper" class="mt-2 text-xs font-semibold text-slate-500">Optional: Instagram, Behance, LinkedIn, or any page that shows your work.</p>
                                    @error('portfolio_url') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="motivation" class="text-sm font-black text-slate-800">Why do you want to join this program? *</label>
                                    <textarea id="motivation" name="motivation" rows="5" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('motivation') border-pink-400 @enderror" placeholder="Tell us about your interest, goals, and what you hope to do with this skill." required data-min-length="40" data-max-length="2000" data-helper="motivation-helper">{{ old('motivation') }}</textarea>
                                    <p id="motivation-helper" class="mt-2 text-xs font-semibold text-slate-500">Aim for at least 40 characters. <span data-character-count-for="motivation">0</span>/2000</p>
                                    @error('motivation') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="referral_source" class="text-sm font-black text-slate-800">How did you hear about PGTP?</label>
                                    <input id="referral_source" name="referral_source" type="text" value="{{ old('referral_source') }}" placeholder="Instagram, friend, website, WhatsApp..." class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 @error('referral_source') border-pink-400 @enderror" />
                                    @error('referral_source') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        @if ($showTurnstile)
                            <div>
                                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                                @error('cf-turnstile-response') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                            </div>
                        @elseif (app()->environment('public'))
                            <div class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800">
                                Cloudflare captcha is enabled for public mode, but the Turnstile site key is missing.
                            </div>
                        @endif

                        <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">
                            Submit Application
                        </button>
                    </form>
                    @endif
                </section>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form[action="{{ route('training.store') }}"]');
            const countdown = document.querySelector('[data-countdown]');
            const submitButton = form?.querySelector('button[type="submit"]');

            const helperClasses = {
                neutral: 'mt-2 text-xs font-semibold text-slate-500',
                valid: 'mt-2 text-xs font-semibold text-emerald-700',
                invalid: 'mt-2 text-xs font-semibold text-pink-700',
            };

            const setHelper = (element, message, state = 'neutral') => {
                if (!element) {
                    return;
                }

                element.textContent = message;
                element.className = helperClasses[state] || helperClasses.neutral;
            };

            const setFieldState = (field, isValid) => {
                field.classList.toggle('border-pink-400', !isValid);
                field.classList.toggle('border-emerald-300', isValid);
            };

            const parseDate = (value) => {
                const date = value ? new Date(`${value}T00:00:00`) : null;
                return date instanceof Date && !Number.isNaN(date.getTime()) ? date : null;
            };

            const ageFromDate = (date) => {
                const today = new Date();
                let age = today.getFullYear() - date.getFullYear();
                const hasHadBirthday = today.getMonth() > date.getMonth() || (today.getMonth() === date.getMonth() && today.getDate() >= date.getDate());

                if (!hasHadBirthday) {
                    age -= 1;
                }

                return age;
            };

            const validators = [
                {
                    selector: '[data-live-required]',
                    event: 'input',
                    validate(field) {
                        const helper = document.getElementById(field.dataset.helper);
                        const valid = field.value.trim().length > 0;

                        setFieldState(field, valid);
                        setHelper(helper, valid ? 'Looks good.' : 'This field is required.', valid ? 'valid' : 'invalid');

                        return valid;
                    },
                },
                {
                    selector: '[data-age-helper]',
                    event: 'input',
                    validate(field) {
                        const helper = document.getElementById(field.dataset.ageHelper);
                        const date = parseDate(field.value);

                        if (!date) {
                            setFieldState(field, false);
                            setHelper(helper, 'Select your date of birth.', 'invalid');
                            return false;
                        }

                        const age = ageFromDate(date);
                        const valid = age >= 14;

                        setFieldState(field, valid);
                        setHelper(helper, valid ? `You are ${age} years old.` : 'Applicants must be at least 14 years old.', valid ? 'valid' : 'invalid');

                        return valid;
                    },
                },
                {
                    selector: '[data-phone-helper]',
                    event: 'input',
                    validate(field) {
                        const helper = document.getElementById(field.dataset.phoneHelper);
                        const digits = field.value.replace(/\D/g, '');
                        const valid = digits.length >= 10;

                        setFieldState(field, valid);
                        setHelper(helper, valid ? 'Reachable WhatsApp number captured.' : 'Enter at least 10 digits for your WhatsApp number.', valid ? 'valid' : 'invalid');

                        return valid;
                    },
                },
                {
                    selector: '[data-email-helper]',
                    event: 'input',
                    validate(field) {
                        const helper = document.getElementById(field.dataset.emailHelper);
                        const valid = field.validity.valid && field.value.trim().length > 0;

                        setFieldState(field, valid);
                        setHelper(helper, valid ? 'Email format looks good.' : 'Enter a valid email address.', valid ? 'valid' : 'invalid');

                        return valid;
                    },
                },
                {
                    selector: '[data-skill-helper]',
                    event: 'change',
                    validate(field) {
                        const helper = document.getElementById(field.dataset.skillHelper);
                        const messages = {
                            'Graphic Design': 'Great fit if you enjoy layouts, branding, and visual problem solving.',
                            'Packaging Design': 'Great fit if you like structure, product presentation, and print finishing.',
                            'Social Media Management': 'Great fit if you enjoy content planning, campaigns, and audience growth.',
                            'Customer Service': 'Great fit if you enjoy communication, support, and client success.',
                        };
                        const valid = field.value.trim().length > 0;

                        setFieldState(field, valid);
                        setHelper(helper, valid ? messages[field.value] : 'Choose one training track.', valid ? 'valid' : 'invalid');

                        return valid;
                    },
                },
                {
                    selector: '[data-laptop-helper]',
                    event: 'change',
                    validate(field) {
                        const helper = document.getElementById(field.dataset.laptopHelper);
                        const valid = field.value !== '';
                        const message = field.value === '1'
                            ? 'Good. Plan to practise regularly outside training hours.'
                            : 'Noted. You can still apply, but some tracks may need extra access to practice tools.';

                        setFieldState(field, valid);
                        setHelper(helper, valid ? message : 'Select yes or no.', valid ? 'valid' : 'invalid');

                        return valid;
                    },
                },
                {
                    selector: '[data-url-helper]',
                    event: 'input',
                    validate(field) {
                        const helper = document.getElementById(field.dataset.urlHelper);

                        if (field.value.trim() === '') {
                            setFieldState(field, true);
                            setHelper(helper, 'Optional: add a public link if you have one.', 'neutral');
                            return true;
                        }

                        const valid = field.validity.valid;

                        setFieldState(field, valid);
                        setHelper(helper, valid ? 'Link format looks good.' : 'Start the link with http:// or https://.', valid ? 'valid' : 'invalid');

                        return valid;
                    },
                },
                {
                    selector: '[data-min-length]',
                    event: 'input',
                    validate(field) {
                        const helper = document.getElementById(field.dataset.helper);
                        const count = field.value.trim().length;
                        const min = Number(field.dataset.minLength);
                        const max = Number(field.dataset.maxLength);
                        const counter = document.querySelector(`[data-character-count-for="${field.id}"]`);
                        const valid = count >= min && count <= max;

                        if (counter) {
                            counter.textContent = count;
                        }

                        setFieldState(field, valid);
                        setHelper(helper, valid ? `Strong enough. ${count}/${max}` : `Write at least ${min} characters. ${count}/${max}`, valid ? 'valid' : 'invalid');

                        return valid;
                    },
                },
            ];

            const runValidation = () => {
                if (!form || !submitButton) {
                    return true;
                }

                const results = validators.flatMap((validator) => {
                    return Array.from(form.querySelectorAll(validator.selector)).map((field) => validator.validate(field));
                });
                const valid = results.every(Boolean);

                submitButton.disabled = !valid;
                submitButton.classList.toggle('opacity-60', !valid);
                submitButton.classList.toggle('cursor-not-allowed', !valid);

                return valid;
            };

            validators.forEach((validator) => {
                if (!form) {
                    return;
                }

                form.querySelectorAll(validator.selector).forEach((field) => {
                    field.addEventListener(validator.event, runValidation);
                    field.addEventListener('blur', runValidation);
                });
            });

            form?.addEventListener('submit', (event) => {
                if (!runValidation()) {
                    event.preventDefault();
                }
            });

            if (countdown) {
                const deadline = new Date(countdown.dataset.deadline).getTime();
                const days = countdown.querySelector('[data-countdown-days]');
                const hours = countdown.querySelector('[data-countdown-hours]');
                const minutes = countdown.querySelector('[data-countdown-minutes]');
                const seconds = countdown.querySelector('[data-countdown-seconds]');
                const status = countdown.querySelector('[data-countdown-status]');
                const pad = (value) => String(value).padStart(2, '0');

                const updateCountdown = () => {
                    const distance = deadline - Date.now();

                    if (distance <= 0) {
                        days.textContent = '00';
                        hours.textContent = '00';
                        minutes.textContent = '00';
                        seconds.textContent = '00';

                        if (status) {
                            status.textContent = 'Registration has ended for this cohort.';
                        }

                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.textContent = 'Registration Closed';
                            submitButton.classList.add('opacity-60', 'cursor-not-allowed');
                        }

                        return;
                    }

                    days.textContent = pad(Math.floor(distance / (1000 * 60 * 60 * 24)));
                    hours.textContent = pad(Math.floor((distance / (1000 * 60 * 60)) % 24));
                    minutes.textContent = pad(Math.floor((distance / (1000 * 60)) % 60));
                    seconds.textContent = pad(Math.floor((distance / 1000) % 60));
                };

                updateCountdown();
                window.setInterval(updateCountdown, 1000);
            }

            runValidation();
        });
    </script>
@endsection
