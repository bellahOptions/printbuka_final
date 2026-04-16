@extends('layouts.theme')

@section('title', 'Register | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto grid max-w-6xl overflow-hidden rounded-md bg-white shadow-xl shadow-cyan-950/10 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="p-6 sm:p-10">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Create Account</p>
                <h1 class="mt-2 text-4xl text-slate-950">Start printing with Printbuka.</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">Already have an account? <a href="{{ route('login') }}" class="font-black text-pink-700 hover:text-pink-800">Login here</a>.</p>

                <form w action="{{ route('register.store') }}" method="POST" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="text-sm font-black text-slate-800">First name</label>
                        <input
                            id="first_name"
                            name="first_name"
                            type="text"
                            value="{{ old('first_name') }}"
                            autocomplete="name"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                            required
                        />
                        @error('first_name')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                     <div>
                        <label for="last_name" class="text-sm font-black text-slate-800">Last name</label>
                        <input
                            id="last_name"
                            name="last_name"
                            type="text"
                            value="{{ old('last_name') }}"
                            autocomplete="name"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                            required
                        />
                        @error('name')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 grid-cols-2">
                         <div>
                        <label for="phone" class="text-sm font-black text-slate-800">Phone/Whatsapp</label>
                        <input
                            id="phone"
                            name="phone"
                            type="text"
                            value="{{ old('phone') }}"
                            autocomplete="phone"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                            required
                        />
                        @error('phone')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                     <div>
                        <label for="company" class="text-sm font-black text-slate-800">COmpany Name</label>
                        <input
                            id="comapny"
                            name="companyName"
                            type="text"
                            value="{{ old('first_name') }}"
                            autocomplete="companyName"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                            required
                        />
                        @error('name')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    </div>
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

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="password" class="text-sm font-black text-slate-800">Password</label>
                            <div class="relative mt-2">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="new-password"
                                    class="min-h-12 w-full rounded-md border border-slate-200 px-4 pr-28 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                                    required
                                />
                                <button
                                    type="button"
                                    data-password-toggle
                                    data-target="password"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-50"
                                >
                                    Show
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="text-sm font-black text-slate-800">Confirm password</label>
                            <div class="relative mt-2">
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    class="min-h-12 w-full rounded-md border border-slate-200 px-4 pr-28 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                                    required
                                />
                                <button
                                    type="button"
                                    data-password-toggle
                                    data-target="password_confirmation"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-50"
                                >
                                    Show
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs font-bold leading-6 text-slate-500">
                        By creating an account, you agree to our
                        <a href="{{ route('policies.terms') }}" class="font-black text-pink-700 hover:text-pink-800">Terms & Conditions</a>,
                        <a href="{{ route('policies.privacy') }}" class="font-black text-pink-700 hover:text-pink-800">Privacy Policy</a>, and
                        <a href="{{ route('policies.refund') }}" class="font-black text-pink-700 hover:text-pink-800">Refund Policy</a>.
                    </p>

                    <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Create Account</button>
                </form>
            </div>

            <div class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Your dashboard</p>
                    <h2 class="mt-4 text-5xl leading-tight">Keep print jobs, gifts and packaging in one place.</h2>
                </div>
                <div class="grid gap-3 text-sm font-bold">
                    <div class="rounded-md bg-white px-4 py-3 text-slate-950">Save product choices</div>
                    <div class="rounded-md bg-white px-4 py-3 text-slate-950">Track orders</div>
                    <div class="rounded-md bg-white px-4 py-3 text-slate-950">Request branded gifts</div>
                </div>
            </div>
        </section>
    </main>

    <script>
        (() => {
            document.querySelectorAll('[data-password-toggle]').forEach((button) => {
                const input = document.getElementById(button.dataset.target || '');
                if (!input) {
                    return;
                }

                button.addEventListener('click', () => {
                    const shouldShow = input.type === 'password';
                    input.type = shouldShow ? 'text' : 'password';
                    button.textContent = shouldShow ? 'Hide' : 'Show';
                });
            });
        })();
    </script>
@endsection
