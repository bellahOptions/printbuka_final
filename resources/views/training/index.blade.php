@extends('layouts.theme')

@section('title', 'Printbuka Graduate Trainee Program')
@section('meta_description', 'Apply for the Printbuka Graduate Trainee Program and learn practical design, packaging, production, social media, and customer service skills for free.')

@section('content')
    @php
        $deadline = '2026-05-29T23:59:59+01:00';
        $tracks = [
            [
                'title' => 'Graphic Design',
                'description' => 'Build sharp layouts, brand assets, print-ready files, and campaign visuals using industry design tools.',
                'accent' => 'bg-pink-50 text-pink-700 border-pink-100',
                'icon' => 'M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42',
            ],
            [
                'title' => 'Packaging Design',
                'description' => 'Learn structural design, dielines, product branding, prototyping, and production handoff.',
                'accent' => 'bg-cyan-50 text-cyan-700 border-cyan-100',
                'icon' => 'M21 7.5 12 2.25 3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25m0-14.25L3 7.5m9 5.25v9m0 0L3 16.5v-9',
            ],
            [
                'title' => 'Social Media Management',
                'description' => 'Plan content, manage campaigns, automate workflows, and report on marketing performance.',
                'accent' => 'bg-amber-50 text-amber-700 border-amber-100',
                'icon' => 'm15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z',
            ],
            [
                'title' => 'Customer Service',
                'description' => 'Handle enquiries, manage client expectations, resolve issues, and support order success.',
                'accent' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                'icon' => 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.941 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.683 2.72 9.094 9.094 0 0 0 3.742.478m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
            ],
        ];

        $benefits = [
            ['value' => '100%', 'label' => 'Free training', 'copy' => 'No tuition fees and no hidden charges.'],
            ['value' => '6', 'label' => 'Months', 'copy' => 'Structured hands-on learning with real production exposure.'],
            ['value' => '4', 'label' => 'Career tracks', 'copy' => 'Choose a path that matches your interest and strengths.'],
            ['value' => '1', 'label' => 'Launchpad', 'copy' => 'Top trainees get priority consideration for Printbuka roles.'],
        ];

        $faqs = [
            ['question' => 'Is it really free?', 'answer' => 'Yes, 100%. There are no fees to apply or participate.'],
            ['question' => 'Where is the training held?', 'answer' => "Training is fully onsite at Printbuka's facility in Lagos."],
            ['question' => 'How long is the program?', 'answer' => '6 months.'],
            ['question' => 'Do I need prior experience?', 'answer' => 'No prior experience is required - just a willingness to learn.'],
            ['question' => 'What happens after I complete the program?', 'answer' => 'You receive a certificate of completion, and outstanding trainees are considered for opportunities within Printbuka.'],
            ['question' => 'Can I apply for more than one track?', 'answer' => 'Yes - you can indicate your preferred track(s) on the application form.'],
        ];

        $howItWorks = [
            ['step' => '1', 'title' => 'Apply Online', 'copy' => 'Fill out the short application form before May 29th.'],
            ['step' => '2', 'title' => 'Get Screened', 'copy' => 'Our team reviews your application and reaches out to shortlisted candidates.'],
            ['step' => '3', 'title' => 'Begin Training', 'copy' => 'Show up, learn from the best, and build skills that last a lifetime.'],
            ['step' => '4', 'title' => 'Graduate & Grow', 'copy' => 'Complete the 6-month program, earn your certificate, and launch your career.'],
        ];
    @endphp

    <main class="overflow-hidden bg-slate-50">
        <section class="relative bg-slate-950 text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(236,72,153,0.34),transparent_34%),radial-gradient(circle_at_80%_20%,rgba(34,211,238,0.22),transparent_30%)]"></div>
            <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-20">
                <div class="flex flex-col justify-center">
                    <p class="mb-5 w-max rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-wide text-cyan-200">
                        Printbuka Graduate Trainee Program
                    </p>
                    <h1 class="max-w-4xl text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                        Start a career in print, design, and production.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg font-semibold leading-8 text-slate-300">
                        A free 6-month practical program for fresh graduates and SSCE holders ready to build real, in-demand skills with mentors inside a working print business.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('training.apply') }}" class="inline-flex items-center justify-center rounded-lg bg-pink-600 px-7 py-3.5 text-sm font-black text-white shadow-lg shadow-pink-950/30 transition hover:bg-pink-700">
                            Apply Now
                        </a>
                        <a href="#tracks" class="inline-flex items-center justify-center rounded-lg border border-white/15 bg-white/10 px-7 py-3.5 text-sm font-black text-white transition hover:bg-white/15">
                            Explore Tracks
                        </a>
                    </div>
                    <div class="mt-8 grid max-w-2xl grid-cols-2 gap-3 sm:grid-cols-4">
                        <div>
                            <p class="text-2xl font-black text-white">Free</p>
                            <p class="text-sm font-semibold text-slate-400">No tuition</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-white">6 months</p>
                            <p class="text-sm font-semibold text-slate-400">Hands-on</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-white">Mentors</p>
                            <p class="text-sm font-semibold text-slate-400">Industry-led</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-white">Career</p>
                            <p class="text-sm font-semibold text-slate-400">Ready skills</p>
                        </div>
                    </div>
                </div>

                <div class="self-center rounded-lg border border-white/10 bg-white p-5 text-slate-950 shadow-2xl shadow-black/30">
                    <div class="rounded-lg bg-slate-950 p-5 text-white">
                        <p class="text-xs font-black uppercase tracking-wide text-cyan-200">Application deadline</p>
                        <h2 class="mt-2 text-3xl font-black">May 29, 2026</h2>
                        <p class="mt-2 text-sm font-semibold text-slate-300">Applications close at 11:59 PM WAT.</p>
                    </div>

                    <div data-countdown data-deadline="{{ $deadline }}" class="mt-4 grid grid-cols-4 gap-2 text-center">
                        @foreach (['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Mins', 'seconds' => 'Secs'] as $key => $label)
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                                <p data-countdown-{{ $key }} class="text-2xl font-black tabular-nums text-slate-950 sm:text-3xl">00</p>
                                <p class="mt-1 text-[0.65rem] font-black uppercase tracking-wide text-slate-500">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 rounded-lg border border-pink-100 bg-pink-50 p-4">
                        <p data-countdown-status class="text-sm font-bold leading-6 text-pink-800">
                            The next cohort is open. Submit your application before the timer reaches zero.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($benefits as $benefit)
                        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-4xl font-black text-pink-600">{{ $benefit['value'] }}</p>
                            <h3 class="mt-3 text-lg font-black text-slate-950">{{ $benefit['label'] }}</h3>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">{{ $benefit['copy'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
                <p class="text-sm font-black uppercase tracking-wide text-pink-600">Your career can start here</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                    Real skills, real mentors, real production work.
                </h2>
                <p class="mt-5 text-lg font-semibold leading-8 text-slate-600">
                    We do not teach theory in isolation. You will learn how creative work moves from client brief to design, approval, production, delivery, and customer care.
                </p>
            </div>
        </section>

        <section id="tracks" class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-600">What you will learn</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Choose a practical training path.</h2>
                    <p class="mt-4 text-base font-semibold leading-7 text-slate-600">Every track is built around work clients and employers already need.</p>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($tracks as $track)
                        <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-lg border {{ $track['accent'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $track['icon'] }}" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-950">{{ $track['title'] }}</h3>
                            <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">{{ $track['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.85fr_1.15fr] lg:px-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-pink-600">Who should apply</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Built for hungry beginners.</h2>
                    <p class="mt-4 text-base font-semibold leading-7 text-slate-600">
                        You do not need years of experience. You need commitment, curiosity, discipline, and the willingness to practise until your work becomes strong.
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-black text-slate-950">Eligibility</h3>
                        <ul class="mt-4 space-y-3 text-sm font-semibold leading-6 text-slate-600">
                            <li>Fresh graduates, SSCE holders, and early-career applicants.</li>
                            <li>Applicants who can commit to the full training period.</li>
                            <li>People interested in design, print, branding, media, or customer experience.</li>
                        </ul>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-black text-slate-950">What you get</h3>
                        <ul class="mt-4 space-y-3 text-sm font-semibold leading-6 text-slate-600">
                            <li>Mentorship from practising print and branding professionals.</li>
                            <li>Portfolio-building projects and practical assignments.</li>
                            <li>Certificate of completion for successful trainees.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-slate-950 py-16 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-200">Program journey</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">From application to career readiness.</h2>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach ([['01', 'Apply', 'Submit your application before the deadline.'], ['02', 'Train', 'Learn through guided practice and production tasks.'], ['03', 'Launch', 'Graduate with stronger skills and clearer direction.']] as [$step, $title, $copy])
                            <div class="rounded-lg border border-white/10 bg-white/5 p-5">
                                <p class="text-sm font-black text-pink-300">{{ $step }}</p>
                                <h3 class="mt-3 text-lg font-black">{{ $title }}</h3>
                                <p class="mt-2 text-sm font-semibold leading-6 text-slate-300">{{ $copy }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-600">How It Works</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Four steps from application to growth.</h2>
                    <p class="mt-4 text-base font-semibold leading-7 text-slate-600">
                        The process is simple, practical, and designed to help serious applicants move quickly.
                    </p>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($howItWorks as $item)
                        <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-950 text-lg font-black text-white">
                                {{ $item['step'] }}
                            </div>
                            <h3 class="mt-5 text-xl font-black text-slate-950">{{ $item['title'] }}</h3>
                            <p class="mt-3 text-sm font-semibold leading-7 text-slate-600">{{ $item['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-slate-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr]">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-pink-600">Frequently Asked Questions</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Answers before you apply.</h2>
                        <p class="mt-4 text-base font-semibold leading-7 text-slate-600">
                            A few quick details about cost, location, duration, and what comes after the program.
                        </p>
                    </div>

                    <div class="grid gap-4">
                        @foreach ($faqs as $faq)
                            <details class="group rounded-lg border border-slate-200 bg-white p-5 shadow-sm" @if ($loop->first) open @endif>
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left text-base font-black text-slate-950">
                                    <span>{{ $faq['question'] }}</span>
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xl leading-none text-pink-600 transition group-open:rotate-45">+</span>
                                </summary>
                                <p class="mt-4 text-sm font-semibold leading-7 text-slate-600">{{ $faq['answer'] }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-16">
            <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
                <p class="text-sm font-black uppercase tracking-wide text-pink-600">Applications are open</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Ready to build a skill you can use?</h2>
                <p class="mt-4 text-base font-semibold leading-7 text-slate-600">
                    The deadline is May 29, 2026. Apply now and take the first serious step toward a practical career in print, design, and customer experience.
                </p>
                <a href="{{ route('training.apply') }}" class="mt-8 inline-flex items-center justify-center rounded-lg bg-pink-600 px-8 py-3.5 text-sm font-black text-white transition hover:bg-pink-700">
                    Apply Before Deadline
                </a>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const countdown = document.querySelector('[data-countdown]');

            if (!countdown) {
                return;
            }

            const deadline = new Date(countdown.dataset.deadline).getTime();
            const days = countdown.querySelector('[data-countdown-days]');
            const hours = countdown.querySelector('[data-countdown-hours]');
            const minutes = countdown.querySelector('[data-countdown-minutes]');
            const seconds = countdown.querySelector('[data-countdown-seconds]');
            const status = document.querySelector('[data-countdown-status]');

            const pad = (value) => String(value).padStart(2, '0');

            const updateCountdown = () => {
                const distance = deadline - Date.now();

                if (distance <= 0) {
                    days.textContent = '00';
                    hours.textContent = '00';
                    minutes.textContent = '00';
                    seconds.textContent = '00';

                    if (status) {
                        status.textContent = 'Applications for this cohort have closed.';
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
        });
    </script>
@endsection
