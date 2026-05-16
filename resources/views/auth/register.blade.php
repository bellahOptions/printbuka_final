{{-- 
    PrintBuka Registration Page - DaisyUI Redesign
    Maintains brand identity while improving conversion flow
--}}

@extends('layouts.theme')

@section('title', 'Create Account | PrintBuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-[#f4fbfb] to-white px-4 py-12 sm:px-6 lg:px-8">
    {{-- Hero Section --}}
    <div class="mx-auto max-w-7xl text-center mb-8 lg:mb-12">
        <div class="inline-flex items-center gap-2 rounded-full bg-pink-50 px-4 py-1.5 text-sm font-medium text-pink-700">
            🚀 Join the Print Revolution
        </div>
        <h1 class="mt-6 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">
            Start Your <span class="text-pink-600">Printing Journey</span>
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600">
            Create an account to save designs, track orders, and access exclusive print deals.
        </p>
    </div>

    <div class="mx-auto max-w-5xl">
        <div class="grid overflow-hidden rounded-2xl bg-white shadow-2xl shadow-slate-200/50 lg:grid-cols-5">
            
            {{-- Left Column - Benefits (DaisyUI Stats) --}}
            <div class="hidden lg:block lg:col-span-2 bg-gradient-to-br from-slate-900 to-slate-800 p-8 text-white">
                <div class="flex h-full flex-col justify-between">
                    <div class="space-y-8">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-cyan-300">Why Join PrintBuka?</p>
                            <h2 class="mt-3 text-2xl font-bold">Your Creative Workspace Awaits</h2>
                        </div>
                        
                        {{-- Stats Grid (DaisyUI Stats) --}}
                        <div class="grid gap-4">
                            <div class="stat bg-white/10 rounded-xl p-4">
                                <div class="stat-value text-3xl text-cyan-300">50+</div>
                                <div class="stat-title text-white/80 text-sm">Product Categories</div>
                                <div class="stat-desc text-white/60 text-xs">From flyers to custom gifts</div>
                            </div>
                            <div class="stat bg-white/10 rounded-xl p-4">
                                <div class="stat-value text-3xl text-pink-300">24hr</div>
                                <div class="stat-title text-white/80 text-sm">Express Production</div>
                                <div class="stat-desc text-white/60 text-xs">Rush orders available</div>
                            </div>
                            <div class="stat bg-white/10 rounded-xl p-4">
                                <div class="stat-value text-3xl text-cyan-300">10k+</div>
                                <div class="stat-title text-white/80 text-sm">Happy Businesses</div>
                                <div class="stat-desc text-white/60 text-xs">Across Nigeria</div>
                            </div>
                        </div>
                    </div>

                    {{-- Testimonial Preview --}}
                    <div class="mt-6 rounded-xl bg-white/10 p-4">
                        <div class="flex items-center gap-2 text-cyan-300 text-sm">Rated 4.9/5 by customers</div>
                        <p class="mt-2 text-sm italic text-white/80">"Best print quality in Lagos. Their DTF transfers are unmatched!"</p>
                        <p class="mt-2 text-xs text-white/60">— Adeola O., Creative Director</p>
                    </div>
                </div>
            </div>

            {{-- Right Column - Registration Form --}}
            <div class="lg:col-span-3 p-6 sm:p-8 lg:p-10">
                {{-- Mobile Header --}}
                <div class="mb-6 lg:hidden">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-600">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="text-xl font-black tracking-tight text-slate-900">PrintBuka</span>
                    </div>
                </div>

                {{-- Form Header --}}
                <div class="mb-6">
                    <p class="text-sm font-bold uppercase tracking-wider text-pink-600">Get Started</p>
                    <h3 class="mt-1 text-2xl font-bold text-slate-900">Create Free Account</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="link link-hover font-bold text-pink-600">Sign in here</a>
                    </p>
                </div>

                {{-- Registration Form --}}
                <form action="{{ route('register.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Name Row (DaisyUI Grid) --}}
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold text-slate-700">First Name</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" 
                                class="input input-bordered w-full focus:input-primary @error('first_name') input-error @enderror"
                                placeholder="John" required />
                            @error('first_name') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold text-slate-700">Last Name</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" 
                                class="input input-bordered w-full focus:input-primary @error('last_name') input-error @enderror"
                                placeholder="Doe" required />
                            @error('last_name') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Contact Row --}}
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold text-slate-700">Phone / WhatsApp</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">+234</span>
                                <input type="tel" name="phone" value="{{ old('phone') }}" 
                                    class="input input-bordered w-full pl-14 focus:input-primary @error('phone') input-error @enderror"
                                    placeholder="8012345678" required />
                            </div>
                            @error('phone') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold text-slate-700">Company Name</span></label>
                            <input type="text" name="companyName" value="{{ old('companyName') }}" 
                                class="input input-bordered w-full focus:input-primary"
                                placeholder="Your Brand Ltd" />
                        </div>
                    </div>

                    {{-- Email Field --}}
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold text-slate-700">Email Address</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                            class="input input-bordered w-full focus:input-primary @error('email') input-error @enderror"
                            placeholder="hello@yourcompany.com" required />
                        @error('email') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password Row --}}
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold text-slate-700">Password</span></label>
                            <div class="relative">
                                <input id="password" name="password" type="password" 
                                    class="input input-bordered w-full pr-24 focus:input-primary @error('password') input-error @enderror"
                                    placeholder="••••••••" required />
                                <button type="button" data-password-toggle data-target="password"
                                    class="btn btn-ghost btn-xs absolute right-2 top-1/2 -translate-y-1/2">Show</button>
                            </div>
                            @error('password') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold text-slate-700">Confirm Password</span></label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" type="password" 
                                    class="input input-bordered w-full pr-24 focus:input-primary"
                                    placeholder="••••••••" required />
                                <button type="button" data-password-toggle data-target="password_confirmation"
                                    class="btn btn-ghost btn-xs absolute right-2 top-1/2 -translate-y-1/2">Show</button>
                            </div>
                        </div>
                    </div>

                    {{-- Terms Agreement --}}
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="checkbox checkbox-sm checkbox-pink-600" required />
                            <span class="label-text text-slate-600 text-sm">
                                I agree to the 
                                <a href="{{ route('policies.terms') }}" class="link link-hover text-pink-600">Terms</a>, 
                                <a href="{{ route('policies.privacy') }}" class="link link-hover text-pink-600">Privacy</a>, and 
                                <a href="{{ route('policies.refund') }}" class="link link-hover text-pink-600">Refund</a> policies.
                            </span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-block bg-pink-600 hover:bg-pink-700 border-pink-600 text-white font-bold shadow-md shadow-pink-200 mt-6">
                        Create Free Account
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </form>

                {{-- Trust Badge --}}
                <div class="mt-6 flex items-center justify-center gap-4 text-xs text-slate-400">
                    <span class="flex items-center gap-1">🔒 SSL Secure</span>
                    <span class="flex items-center gap-1">💳 Secure Payments</span>
                    <span class="flex items-center gap-1">🚚 Fast Delivery</span>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    (() => {
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            const input = document.getElementById(button.dataset.target || '');
            if (!input) return;
            button.addEventListener('click', () => {
                const shouldShow = input.type === 'password';
                input.type = shouldShow ? 'text' : 'password';
                button.textContent = shouldShow ? 'Hide' : 'Show';
            });
        });
    })();
</script>
@endsection
