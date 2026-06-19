@extends('layouts.auth')

@section('title', 'Set New Password | Printbuka')

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

            <div class="border-b border-slate-100 bg-slate-950 px-8 py-7 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500/10">
                    <svg class="h-7 w-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-black text-white">Set a new password</h1>
                <p class="mt-1 text-sm text-slate-400">Choose something strong and unique.</p>
            </div>

            <div class="px-8 py-7">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST" class="space-y-5" x-data="{ showP: false, showC: false }">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label class="mb-1.5 block text-sm font-black text-slate-700">Email address</label>
                        <input type="email" name="email" value="{{ old('email', $email) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('email') border-pink-400 @enderror"
                               required>
                        @error('email') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-black text-slate-700">New password</label>
                        <div class="relative">
                            <input :type="showP ? 'text' : 'password'" name="password"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-16 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100 @error('password') border-pink-400 @enderror"
                                   placeholder="Min. 8 characters" required>
                            <button type="button" @click="showP = !showP"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 hover:text-pink-600"
                                    x-text="showP ? 'Hide' : 'Show'">Show</button>
                        </div>
                        @error('password') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-black text-slate-700">Confirm new password</label>
                        <div class="relative">
                            <input :type="showC ? 'text' : 'password'" name="password_confirmation"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-16 text-sm font-semibold outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                                   placeholder="Repeat password" required>
                            <button type="button" @click="showC = !showC"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 hover:text-pink-600"
                                    x-text="showC ? 'Hide' : 'Show'">Show</button>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-xs text-slate-500 space-y-1">
                        <p class="font-black text-slate-700">Password requirements</p>
                        <p>• At least 8 characters</p>
                        <p>• Mix of letters and numbers</p>
                        <p>• Avoid commonly used passwords</p>
                    </div>

                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-pink-600 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-pink-200 transition hover:bg-pink-700 active:scale-[0.98]">
                        Update password
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
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
    </div>
</div>
@endsection
