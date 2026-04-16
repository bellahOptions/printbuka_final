@extends('layouts.theme')

@section('title', 'Set New Password | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto grid max-w-5xl overflow-hidden rounded-md bg-white shadow-xl shadow-cyan-950/10 lg:grid-cols-[1fr_0.9fr]">
            <div class="p-6 sm:p-10">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Set New Password</p>
                <h1 class="mt-2 text-4xl text-slate-950">Choose a new secure password.</h1>

                <form action="{{ route('password.update') }}" method="POST" class="mt-8 space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="text-sm font-black text-slate-800">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $email) }}"
                            autocomplete="email"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                            required
                        />
                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="password" class="text-sm font-black text-slate-800">New password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                                required
                            />
                            @error('password')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="text-sm font-black text-slate-800">Confirm password</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                                required
                            />
                        </div>
                    </div>

                    <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">
                        Reset Password
                    </button>
                </form>
            </div>

            <div class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Security</p>
                    <h2 class="mt-4 text-4xl leading-tight">Your new password should be unique.</h2>
                    <p class="mt-5 text-sm leading-7 text-slate-300">
                        Use letters, numbers, and avoid reusing old passwords.
                    </p>
                </div>
            </div>
        </section>
    </main>
@endsection
