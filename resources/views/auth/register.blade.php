@extends('layouts.auth')

@section('title', 'Create Account | Printbuka')

@section('content')
<div class="flex min-h-screen">

    {{-- ── Left brand panel ────────────────────────────────────────────── --}}
    <div class="relative hidden w-[42%] shrink-0 flex-col overflow-hidden bg-slate-950 lg:flex"
         style="background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 28px 28px;">

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-pink-700/20 via-transparent to-cyan-900/20"></div>

        <div class="relative flex h-full flex-col justify-between p-10 xl:p-14">
            <a href="{{ route('home') }}" class="inline-flex">
                <img src="{{ asset('logo-dark.svg') }}" alt="Printbuka" class="h-9 w-auto brightness-0 invert">
            </a>

            <div class="space-y-8">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-pink-400">Join Printbuka</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight text-white xl:text-5xl">
                        Your creative<br>workspace<br><span class="text-pink-400">starts here.</span>
                    </h1>
                </div>

                <div class="grid gap-4">
                    @foreach ([
                        ['50+', 'Product categories', 'From flyers to custom gifts'],
                        ['24hr', 'Express production', 'Rush orders available'],
                        ['10k+', 'Happy businesses', 'Across Nigeria'],
                    ] as [$stat, $label, $desc])
                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <p class="text-2xl font-black text-cyan-400">{{ $stat }}</p>
                        <p class="text-sm font-black text-white">{{ $label }}</p>
                        <p class="text-xs text-slate-400">{{ $desc }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <blockquote class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                <p class="text-sm italic text-slate-300">"Best print quality in Lagos. Their DTF transfers are unmatched!"</p>
                <p class="mt-2 text-xs font-black text-slate-500">— Adeola O., Creative Director</p>
            </blockquote>
        </div>
    </div>

    {{-- ── Right form panel ────────────────────────────────────────────── --}}
    <div class="flex flex-1 flex-col">

        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            <a href="{{ route('home') }}" class="lg:hidden">
                <img src="{{ asset('logo.png') }}" alt="Printbuka" class="h-8 w-auto">
            </a>
            <span class="hidden text-sm text-slate-400 lg:block">Step 1 of 1</span>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 transition hover:text-pink-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to site
            </a>
        </div>

        <div class="flex flex-1 items-start justify-center px-6 py-6 sm:px-10">
            <div class="w-full max-w-lg">

                <div class="mb-7">
                    <h2 class="text-3xl font-black text-slate-950">Create your account</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-black text-pink-600 transition hover:text-pink-700">Sign in</a>
                    </p>
                </div>

                <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Name row --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">First name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('first_name') border-pink-400 bg-pink-50 @enderror"
                                   placeholder="John" required>
                            @error('first_name') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Last name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('last_name') border-pink-400 bg-pink-50 @enderror"
                                   placeholder="Doe" required>
                            @error('last_name') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Contact row --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Phone / WhatsApp</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">+234</span>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-14 pr-4 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('phone') border-pink-400 @enderror"
                                       placeholder="8012345678" required>
                            </div>
                            @error('phone') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Company <span class="font-medium text-slate-400">(optional)</span></label>
                            <input type="text" name="companyName" value="{{ old('companyName') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                                   placeholder="Your Brand Ltd">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-black text-slate-700">Email address</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('email') border-pink-400 bg-pink-50 @enderror"
                               placeholder="hello@yourcompany.com" required>
                        @error('email') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password row --}}
                    <div class="grid gap-4 sm:grid-cols-2" x-data="{ showP: false, showC: false }">
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Password</label>
                            <div class="relative">
                                <input :type="showP ? 'text' : 'password'" id="password" name="password"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-16 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('password') border-pink-400 @enderror"
                                       placeholder="Min. 8 characters" required>
                                <button type="button" @click="showP = !showP"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 hover:text-pink-600"
                                        x-text="showP ? 'Hide' : 'Show'">Show</button>
                            </div>
                            @error('password') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Confirm password</label>
                            <div class="relative">
                                <input :type="showC ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-16 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                                       placeholder="Repeat password" required>
                                <button type="button" @click="showC = !showC"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 hover:text-pink-600"
                                        x-text="showC ? 'Hide' : 'Show'">Show</button>
                            </div>
                        </div>
                    </div>

                    {{-- Terms --}}
                    <label class="flex cursor-pointer items-start gap-3 pt-1">
                        <input type="checkbox" class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 accent-pink-600" required>
                        <span class="text-sm text-slate-600">
                            I agree to the
                            <a href="{{ route('policies.terms') }}" target="_blank" class="font-semibold text-pink-600 hover:underline">Terms</a>,
                            <a href="{{ route('policies.privacy') }}" target="_blank" class="font-semibold text-pink-600 hover:underline">Privacy Policy</a>, and
                            <a href="{{ route('policies.refund') }}" target="_blank" class="font-semibold text-pink-600 hover:underline">Refund Policy</a>.
                        </span>
                    </label>

                    {{-- Submit --}}
                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-pink-600 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-pink-200 transition hover:bg-pink-700 active:scale-[0.98]">
                        Create free account
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </form>

                <div class="mt-8 border-t border-slate-100 pt-5">
                    <div class="flex flex-wrap items-center justify-center gap-6 text-xs font-semibold text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            SSL encrypted
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Secure payments
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Free forever
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
