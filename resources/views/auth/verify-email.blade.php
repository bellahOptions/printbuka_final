@extends('layouts.theme')

@section('title', 'Verify Email | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto grid max-w-4xl overflow-hidden rounded-md bg-white shadow-xl shadow-cyan-950/10 lg:grid-cols-[1fr_0.9fr]">
            <div class="p-6 sm:p-10">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Email Verification</p>
                <h1 class="mt-2 text-4xl text-slate-950">Verify your email before continuing.</h1>
                <p class="mt-4 text-sm leading-6 text-slate-600">
                    Open your email and click the verification link. If you did not receive it, request a new one below.
                </p>

                @if (session('status'))
                    <p class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                        {{ session('status') }}
                    </p>
                @endif

                <div class="mt-8">
                    <form action="{{ route('verification.send') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="text-sm font-black text-slate-800">Email address</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $email ?? '') }}"
                                autocomplete="email"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                                required
                            />
                            @error('email')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="min-h-12 rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">
                            Resend Verification Email
                        </button>
                    </form>
                </div>
                <p class="mt-4 text-sm font-bold text-slate-600">
                    Already verified?
                    <a href="{{ route('login') }}" class="font-black text-pink-700 hover:text-pink-800">Sign in here</a>.
                </p>
            </div>

            <div class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Security</p>
                    <h2 class="mt-4 text-4xl leading-tight">Verified accounts protect your orders and staff tools.</h2>
                    <p class="mt-5 text-sm leading-7 text-slate-300">This applies to both customer and admin/staff accounts.</p>
                </div>
            </div>
        </section>
    </main>
@endsection
