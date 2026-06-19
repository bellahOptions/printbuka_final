@extends('layouts.auth')

@section('title', 'Verify Your Email | Printbuka')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12"
     style="background-image: radial-gradient(circle, rgba(219,39,119,0.04) 1px, transparent 1px); background-size: 32px 32px;">

    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <a href="{{ route('home') }}">
                <img src="{{ asset('logo.png') }}" alt="Printbuka" class="mx-auto h-10 w-auto">
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">

            <div class="border-b border-slate-100 bg-slate-950 px-8 py-8 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-400/10">
                    <svg class="h-8 w-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-black text-white">Check your inbox</h1>
                <p class="mt-2 text-sm leading-relaxed text-slate-400">
                    We sent a verification link to your email address. Click it to activate your account and start ordering.
                </p>
            </div>

            <div class="px-8 py-7 space-y-5">
                @if (session('status'))
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 text-sm text-amber-800">
                    <p class="font-black">Didn't receive the email?</p>
                    <p class="mt-1 font-medium text-amber-700">Check your spam/junk folder first, then use the button below to resend.</p>
                </div>

                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-pink-600 px-6 py-3.5 text-sm font-black text-pink-600 transition hover:bg-pink-600 hover:text-white active:scale-[0.98]">
                        Resend verification email
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 transition hover:text-pink-600">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Return to sign in
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            Need help? <a href="{{ route('home') }}" class="text-pink-600 hover:underline">Contact support</a>
        </p>
    </div>
</div>
@endsection
