@extends('layouts.theme')

@section('title', 'Forgot Password | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto grid max-w-5xl overflow-hidden rounded-md bg-white shadow-xl shadow-cyan-950/10 lg:grid-cols-[1fr_0.9fr]">
            <div class="p-6 sm:p-10">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Password Reset</p>
                <h1 class="mt-2 text-4xl text-slate-950">Reset your account password.</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Enter your email address and we will send you a secure reset link.
                </p>

                @if (session('status'))
                    <p class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                        {{ session('status') }}
                    </p>
                @endif

                <form action="{{ route('password.email') }}" method="POST" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="text-sm font-black text-slate-800">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                            required
                        />
                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">
                        Send Password Reset Link
                    </button>
                </form>

                <p class="mt-5 text-sm font-bold text-slate-600">
                    Remembered your password?
                    <a href="{{ route('login') }}" class="font-black text-pink-700 hover:text-pink-800">Go back to login</a>.
                </p>
            </div>

            <div class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Account Recovery</p>
                    <h2 class="mt-4 text-4xl leading-tight">Keep your Printbuka account secure.</h2>
                    <p class="mt-5 text-sm leading-7 text-slate-300">
                        We only send reset links to registered email addresses.
                    </p>
                </div>
            </div>
        </section>
    </main>
@endsection
