@extends('layouts.auth')

@section('title', (($mode ?? 'customer') === 'staff' ? 'Sign In' : 'Sign In').' | Printbuka')

@section('content')
@php($isStaff = ($mode ?? 'customer') === 'staff')
<div class="flex min-h-screen">

    {{-- ── Left brand panel (hidden on mobile) ────────────────────────── --}}
    <div class="relative hidden w-[42%] shrink-0 flex-col overflow-hidden bg-slate-950 lg:flex"
         style="background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 28px 28px;">

        {{-- Gradient overlay --}}
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-pink-700/20 via-transparent to-cyan-900/20"></div>

        <div class="relative flex h-full flex-col justify-between p-10 xl:p-14">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="inline-flex">
                <img src="{{ asset('logo-dark.svg') }}" alt="Printbuka" class="h-9 w-auto brightness-0 invert">
            </a>

            {{-- Brand copy --}}
            <div class="space-y-6">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-pink-400">
                        {{ $isStaff ? 'Operations' : 'Nigeria\'s Print Platform' }}
                    </p>
                    <h1 class="mt-3 text-4xl font-black leading-tight text-white xl:text-5xl">
                        {!! $isStaff
                            ? 'Manage print<br>operations with<br><span class="text-pink-400">precision.</span>'
                            : 'Print everything<br>you can<br><span class="text-pink-400">imagine.</span>' !!}
                    </h1>
                </div>

                <ul class="space-y-3">
                    @foreach($isStaff
                        ? ['Order & job tracking', 'Customer support tools', 'Production dashboards']
                        : ['Express 24-hr turnaround', 'Premium print quality', 'Nationwide delivery']
                    as $feature)
                    <li class="flex items-center gap-3 text-sm text-slate-300">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-pink-600/20 text-pink-400">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Social proof --}}
            @if (!$isStaff)
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <div class="flex -space-x-2">
                        <div class="h-8 w-8 rounded-full border-2 border-slate-900 bg-cyan-400"></div>
                        <div class="h-8 w-8 rounded-full border-2 border-slate-900 bg-pink-400"></div>
                        <div class="h-8 w-8 rounded-full border-2 border-slate-900 bg-amber-400"></div>
                    </div>
                    <div>
                        <p class="text-sm font-black text-white">10,000+ satisfied customers</p>
                        <p class="text-xs text-slate-400">Rated 4.9 / 5 across Nigeria</p>
                    </div>
                </div>
            </div>
            @else
            <p class="text-xs text-slate-600">Printbuka — internal access only.</p>
            @endif
        </div>
    </div>

    {{-- ── Right form panel ────────────────────────────────────────────── --}}
    <div class="flex flex-1 flex-col">

        {{-- Top bar --}}
        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            {{-- Mobile logo --}}
            <a href="{{ route('home') }}" class="lg:hidden">
                <img src="{{ asset('logo.png') }}" alt="Printbuka" class="h-8 w-auto">
            </a>
            <span class="hidden text-sm text-slate-400 lg:block">
                {{ $isStaff ? 'Secure access' : 'Welcome back' }}
            </span>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 transition hover:text-pink-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to site
            </a>
        </div>

        {{-- Form --}}
        <div class="flex flex-1 items-center justify-center px-6 py-8 sm:px-10">
            <div class="w-full max-w-md">

                <div class="mb-8">
                    <h2 class="text-3xl font-black text-slate-950">Sign in</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $isStaff ? 'Enter your credentials to continue.' : 'Good to see you again. Enter your details below.' }}
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ $storeRoute ?? route('login.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-black text-slate-700">Email address</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('email') border-pink-400 bg-pink-50 @enderror"
                               placeholder="you@example.com" required autocomplete="email">
                        @error('email')
                            <p class="mt-1.5 text-xs font-semibold text-pink-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <div class="mb-1.5 flex items-center justify-between">
                            <label class="text-sm font-black text-slate-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-pink-600 transition hover:text-pink-700">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-16 text-sm font-semibold text-slate-900 outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('password') border-pink-400 bg-pink-50 @enderror"
                                   placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" @click="show = !show"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 transition hover:text-pink-600"
                                    x-text="show ? 'Hide' : 'Show'">Show</button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs font-semibold text-pink-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <label class="flex cursor-pointer items-center gap-3">
                        <input type="checkbox" name="remember" value="1"
                               class="h-4 w-4 rounded border-slate-300 accent-pink-600">
                        <span class="text-sm font-semibold text-slate-600">Keep me signed in for 30 days</span>
                    </label>

                    {{-- Submit --}}
                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-pink-600 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-pink-200 transition hover:bg-pink-700 active:scale-[0.98]">
                        Sign in
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>

                    {{-- Register link --}}
                    <p class="text-center text-sm text-slate-500">
                        {{ $isStaff ? 'Need access?' : 'New here?' }}
                        <a href="{{ $registerRoute ?? route('register') }}"
                           class="font-black text-pink-600 transition hover:text-pink-700">
                            {{ $isStaff ? 'Request an account' : 'Create a free account' }}
                        </a>
                    </p>
                </form>

                {{-- Trust signals --}}
                @if (!$isStaff)
                <div class="mt-10 border-t border-slate-100 pt-6">
                    <div class="flex flex-wrap items-center justify-center gap-6 text-xs font-semibold text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            SSL encrypted
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Privacy protected
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Secure payments
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
