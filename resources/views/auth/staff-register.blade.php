@extends('layouts.auth')

@section('title', 'Request Team Account | Printbuka')

@section('content')
<div class="flex min-h-screen">

    {{-- ── Left brand panel ────────────────────────────────────────────── --}}
    <div class="relative hidden w-[42%] shrink-0 flex-col overflow-hidden bg-slate-950 lg:flex"
         style="background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 28px 28px;">

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-cyan-900/20 via-transparent to-pink-700/20"></div>

        <div class="relative flex h-full flex-col justify-between p-10 xl:p-14">
            <a href="{{ route('home') }}" class="inline-flex">
                <img src="{{ asset('logo-dark.svg') }}" alt="Printbuka" class="h-9 w-auto brightness-0 invert">
            </a>

            <div class="space-y-6">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-cyan-400">Printbuka Operations</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight text-white xl:text-5xl">
                        Join the<br>production<br><span class="text-cyan-400">team.</span>
                    </h1>
                    <p class="mt-4 text-sm leading-relaxed text-slate-400">
                        Submit your details for review. Once approved, you'll receive your access credentials via email.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-cyan-500/20">
                            <svg class="h-4 w-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-white">Approval required</p>
                            <p class="mt-1 text-xs text-slate-400">All accounts are reviewed and activated by a designated administrator. Roles are assigned after approval.</p>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-xs text-slate-700">Printbuka — internal access only.</p>
        </div>
    </div>

    {{-- ── Right form panel ────────────────────────────────────────────── --}}
    <div class="flex flex-1 flex-col">

        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            <a href="{{ route('home') }}" class="lg:hidden">
                <img src="{{ asset('logo.png') }}" alt="Printbuka" class="h-8 w-auto">
            </a>
            <span class="hidden text-sm text-slate-400 lg:block">Request access</span>
            <a href="{{ route('staff.login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 transition hover:text-pink-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Sign in instead
            </a>
        </div>

        <div class="flex flex-1 items-start justify-center px-6 py-6 sm:px-10">
            <div class="w-full max-w-lg">

                <div class="mb-7">
                    <h2 class="text-3xl font-black text-slate-950">Request an account</h2>
                    <p class="mt-1 text-sm text-slate-500">Complete the form and an administrator will review your request.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-pink-200 bg-pink-50 p-4 text-sm text-pink-800">
                        <p class="font-black">Please fix the highlighted fields.</p>
                        <ul class="mt-2 list-disc space-y-0.5 pl-5 font-semibold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('staff.register.store') }}" method="POST" class="space-y-4"
                      x-data="{ showP: false, showC: false }">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">First name <span class="text-pink-600">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('first_name') border-pink-400 bg-pink-50 @enderror"
                                   required>
                            @error('first_name') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Last name <span class="text-pink-600">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('last_name') border-pink-400 bg-pink-50 @enderror"
                                   required>
                            @error('last_name') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Phone <span class="text-pink-600">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('phone') border-pink-400 @enderror"
                                   required>
                            @error('phone') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Date of birth <span class="text-pink-600">*</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('date_of_birth') border-pink-400 @enderror"
                                   required>
                            @error('date_of_birth') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-black text-slate-700">Residential address <span class="text-pink-600">*</span></label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('address') border-pink-400 @enderror"
                               required>
                        @error('address') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-black text-slate-700">Email address <span class="text-pink-600">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('email') border-pink-400 bg-pink-50 @enderror"
                               required>
                        @error('email') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Password <span class="text-pink-600">*</span></label>
                            <div class="relative">
                                <input :type="showP ? 'text' : 'password'" name="password"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-16 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('password') border-pink-400 @enderror"
                                       required>
                                <button type="button" @click="showP = !showP"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 hover:text-pink-600"
                                        x-text="showP ? 'Hide' : 'Show'">Show</button>
                            </div>
                            @error('password') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-black text-slate-700">Confirm password <span class="text-pink-600">*</span></label>
                            <div class="relative">
                                <input :type="showC ? 'text' : 'password'" name="password_confirmation"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-16 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                                       required>
                                <button type="button" @click="showC = !showC"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 hover:text-pink-600"
                                        x-text="showC ? 'Hide' : 'Show'">Show</button>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-xs text-slate-500">
                        Your role and department will be assigned by an administrator after your request is approved.
                    </div>

                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-950 px-6 py-3.5 text-sm font-black text-white shadow-lg transition hover:bg-slate-800 active:scale-[0.98]">
                        Submit request
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
