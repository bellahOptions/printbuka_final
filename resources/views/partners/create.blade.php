@extends('layouts.theme')

@section('title', 'Become a Partner | Printbuka')

@section('content')
    <main class="bg-white text-slate-900">
        <section class="overflow-hidden bg-[#f4fbfb] py-16">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.95fr_1.05fr] lg:px-8">
                <div class="flex flex-col justify-center">
                    <p class="inline-flex w-fit rounded-md bg-white px-4 py-2 text-sm font-black text-pink-700 shadow-sm">Partner with Printbuka</p>
                    <h1 class="mt-5 max-w-3xl text-5xl leading-tight text-slate-950 sm:text-6xl">Sell more gifts without running production.</h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">For event organisers, gifting businesses, brand consultants and corporate gift vendors who need a reliable production and custom delivery partner behind the scenes.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#partner-form" class="rounded-md bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">Apply to Partner</a>
                        <a href="{{ route('products.index') }}#categories" class="rounded-md border border-slate-200 bg-white px-6 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">View Gift Products</a>
                    </div>
                </div>

                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=1200&q=80"
                        alt="Branded gift items prepared for delivery"
                        class="h-[500px] w-full rounded-md object-cover shadow-2xl shadow-cyan-900/10"
                    />
                    <div class="absolute bottom-6 left-6 max-w-sm rounded-md bg-white p-5 shadow-xl">
                        <p class="text-sm font-black text-pink-700">Built for client work</p>
                        <p class="mt-2 text-2xl font-black text-slate-950">Your brand faces the client. Printbuka handles production and custom delivery.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-5 md:grid-cols-3">
                    <div class="rounded-md border border-slate-200 p-6">
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">01</p>
                        <h2 class="mt-4 text-2xl font-black text-slate-950">Bring the client brief</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Send product needs, branding details, quantity, delivery plan and timeline.</p>
                    </div>
                    <div class="rounded-md border border-slate-200 p-6">
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">02</p>
                        <h2 class="mt-4 text-2xl font-black text-slate-950">We produce and package</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Printbuka handles production, finishing, quality checks and customised delivery packaging designed for your business.</p>
                    </div>
                    <div class="rounded-md border border-slate-200 p-6">
                        <p class="text-sm font-black uppercase tracking-wide text-emerald-700">03</p>
                        <h2 class="mt-4 text-2xl font-black text-slate-950">You serve clients faster</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">You stay focused on client relationships while we help you deliver branded gifts and print orders with speed.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-slate-950 py-16 text-white">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Who this is for</p>
                    <h2 class="mt-2 text-4xl">A production partner for gifting-led businesses.</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300">If your clients ask for branded mugs, shirts, gift boxes, event giveaways, packaging, prints or custom delivery presentation, this program gives you a faster way to fulfil those requests.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <h3 class="font-black">Event Organisers</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Gift packs, attendee kits, banners, programmes and branded event items.</p>
                    </div>
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <h3 class="font-black">Gifting Vendors</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Production support for mugs, shirts, notebooks, tote bags and curated gift sets.</p>
                    </div>
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <h3 class="font-black">Brand Consultants</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">A fulfilment partner for branded merchandise, packaging and campaign materials.</p>
                    </div>
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <h3 class="font-black">Corporate Procurement</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Reliable production and delivery support for staff and client gifting.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="partner-form" class="bg-slate-50 py-16">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.75fr_1.25fr] lg:px-8">
                <aside class="h-fit rounded-md bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Partner Application</p>
                    <h2 class="mt-3 text-3xl text-slate-950">Tell us about your business.</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600">Share the kind of clients you serve, the gift and print products they request, and how you want custom delivery packaging to work for your brand.</p>
                    <div class="mt-6 space-y-3 text-sm font-bold text-slate-700">
                        <p class="rounded-md bg-[#f4fbfb] px-4 py-3">Production handled by Printbuka</p>
                        <p class="rounded-md bg-[#f4fbfb] px-4 py-3">Custom packaging support</p>
                        <p class="rounded-md bg-[#f4fbfb] px-4 py-3">Faster client fulfilment</p>
                    </div>
                </aside>

                <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('partners.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="business_name" class="text-sm font-black text-slate-800">Business name</label>
                                <input id="business_name" name="business_name" type="text" value="{{ old('business_name') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required />
                                @error('business_name')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contact_name" class="text-sm font-black text-slate-800">Contact name</label>
                                <input id="contact_name" name="contact_name" type="text" value="{{ old('contact_name') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required />
                                @error('contact_name')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="email" class="text-sm font-black text-slate-800">Email address</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required />
                                @error('email')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="text-sm font-black text-slate-800">Phone number</label>
                                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required />
                                @error('phone')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-3">
                            <div>
                                <label for="business_type" class="text-sm font-black text-slate-800">Business type</label>
                                <select id="business_type" name="business_type" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required>
                                    <option value="">Select one</option>
                                    <option value="Event Organiser" @selected(old('business_type') === 'Event Organiser')>Event Organiser</option>
                                    <option value="Gifting Business" @selected(old('business_type') === 'Gifting Business')>Gifting Business</option>
                                    <option value="Brand Consultant" @selected(old('business_type') === 'Brand Consultant')>Brand Consultant</option>
                                    <option value="Corporate Procurement" @selected(old('business_type') === 'Corporate Procurement')>Corporate Procurement</option>
                                    <option value="Other" @selected(old('business_type') === 'Other')>Other</option>
                                </select>
                                @error('business_type')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="city" class="text-sm font-black text-slate-800">City</label>
                                <input id="city" name="city" type="text" value="{{ old('city') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                                @error('city')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="client_volume" class="text-sm font-black text-slate-800">Client volume</label>
                                <select id="client_volume" name="client_volume" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                    <option value="">Select range</option>
                                    <option value="1-5 monthly clients" @selected(old('client_volume') === '1-5 monthly clients')>1-5 monthly clients</option>
                                    <option value="6-15 monthly clients" @selected(old('client_volume') === '6-15 monthly clients')>6-15 monthly clients</option>
                                    <option value="16-30 monthly clients" @selected(old('client_volume') === '16-30 monthly clients')>16-30 monthly clients</option>
                                    <option value="30+ monthly clients" @selected(old('client_volume') === '30+ monthly clients')>30+ monthly clients</option>
                                </select>
                                @error('client_volume')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="services_needed" class="text-sm font-black text-slate-800">What products or services do your clients request?</label>
                            <textarea id="services_needed" name="services_needed" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Mugs, shirts, gift boxes, branded packaging, event kits, flyers, banners..." required>{{ old('services_needed') }}</textarea>
                            @error('services_needed')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="delivery_packaging_needs" class="text-sm font-black text-slate-800">Custom delivery packaging needs</label>
                            <textarea id="delivery_packaging_needs" name="delivery_packaging_needs" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Tell us how you want delivery packaging to represent your brand.">{{ old('delivery_packaging_needs') }}</textarea>
                            @error('delivery_packaging_needs')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="text-sm font-black text-slate-800">Anything else we should know?</label>
                            <textarea id="message" name="message" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Submit Partner Request</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
@endsection
