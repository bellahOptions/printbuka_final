@extends('layouts.auth')

@section('title', 'Reset Password | Printbuka')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12"
     style="background-image: radial-gradient(circle, rgba(219,39,119,0.04) 1px, transparent 1px); background-size: 32px 32px;">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="mb-8 text-center">
            <a href="{{ route('home') }}">
                <img src="{{ asset('logo.png') }}" alt="Printbuka" class="mx-auto h-10 w-auto">
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">

            {{-- Header stripe --}}
            <div class="border-b border-slate-100 bg-slate-950 px-8 py-7 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-pink-600/10">
                    <svg class="h-7 w-7 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-black text-white">Forgot your password?</h1>
                <p class="mt-1 text-sm text-slate-400">No worries — we'll send a reset link to your inbox.</p>
            </div>

            <div class="px-8 py-7">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-sm font-black text-slate-700">Email address</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('email') border-pink-400 bg-pink-50 @enderror"
                               placeholder="you@example.com" required autocomplete="email">
                        @error('email')
                            <p class="mt-1.5 text-xs font-semibold text-pink-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-pink-600 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-pink-200 transition hover:bg-pink-700 active:scale-[0.98]">
                        Send reset link
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 transition hover:text-pink-600">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to sign in
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            We only send reset links to email addresses registered in our system.
        </p>
    </div>
</div>
@endsection
