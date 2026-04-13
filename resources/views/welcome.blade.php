@extends('layouts.theme')
@section('title', 'Printbuka | Online Print Shop in Nigeria')
@section('content')
    <main role="main" class="bg-white text-slate-900">
        <section class="hero overflow-hidden bg-[#f4fbfb]">
            <div class="mx-auto grid min-h-[620px] max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-16">
                <div class="max-w-3xl">
                    <p class="mb-4 inline-flex rounded-md bg-white px-4 py-2 text-sm font-bold text-pink-700 shadow-sm">Quality prints. Delivered nationwide.</p>
                    <h1 class="max-w-3xl text-5xl leading-tight text-slate-950 sm:text-6xl lg:text-7xl">Print what your brand needs today.</h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">Business cards, flyers, stickers, packaging and branded gifts made with sharp colour, clean finishing and reliable delivery.</p>

                    <div class="mt-8">
                        <livewire:product.search />
                    </div>

                    <div class="mt-8 grid max-w-2xl gap-3 text-sm font-bold text-slate-700 sm:grid-cols-3">
                        <div class="rounded-md bg-white px-4 py-3 shadow-sm">3-7 day delivery</div>
                        <div class="rounded-md bg-white px-4 py-3 shadow-sm">Free file checks</div>
                        <div class="rounded-md bg-white px-4 py-3 shadow-sm">Bulk order pricing</div>
                    </div>
                </div>

                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1200&q=80"
                        alt="Colourful print materials on a design desk"
                        class="h-[460px] w-full rounded-md object-cover shadow-2xl shadow-cyan-900/10"
                    />
                    <div class="absolute bottom-6 left-6 max-w-xs rounded-md bg-white p-5 shadow-xl">
                        <p class="text-sm font-bold text-pink-700">Popular now</p>
                        <p class="mt-2 text-2xl font-black text-slate-950">Flyers from NGN 35,000</p>
                        <p class="mt-2 text-sm text-slate-600">Launch offers, church events, menus and product promos.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Product Categories</p>
                        <h2 class="mt-2 text-4xl text-slate-950">Print, brand and gift from one place.</h2>
                        <p class="mt-3 text-base leading-7 text-slate-600">Choose the job you need today, from campaign materials to branded items your customers and team can actually use.</p>
                    </div>
                    <a href="#" class="inline-flex w-fit rounded-md border border-slate-200 px-5 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Explore Categories</a>
                </div>

                <div class="mt-8 grid gap-5 lg:grid-cols-4">
                    <a href="#" class="group relative min-h-[360px] overflow-hidden rounded-md bg-slate-950 lg:col-span-2">
                        <img src="https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=1200&q=80" alt="Branded gifts and desk items" class="absolute inset-0 h-full w-full object-cover opacity-70 transition duration-500 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-slate-950/35"></div>
                        <div class="relative flex h-full flex-col justify-end p-6 text-white sm:p-8">
                            <p class="mb-3 w-fit rounded-md bg-white px-3 py-1 text-sm font-black text-pink-700">Core Service</p>
                            <h3 class="text-3xl font-black">Branded Gifts</h3>
                            <p class="mt-3 max-w-xl text-sm leading-6 text-white/90">Mugs, shirts, tote bags, notebooks, hampers and corporate gift sets for clients, events and staff appreciation.</p>
                            <div class="mt-5 flex flex-wrap gap-2 text-xs font-bold">
                                <span class="rounded-md bg-white/90 px-3 py-2 text-slate-950">Mugs</span>
                                <span class="rounded-md bg-white/90 px-3 py-2 text-slate-950">T-shirts</span>
                                <span class="rounded-md bg-white/90 px-3 py-2 text-slate-950">Gift sets</span>
                                <span class="rounded-md bg-white/90 px-3 py-2 text-slate-950">Tote bags</span>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="group overflow-hidden rounded-md border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-lg">
                        <img src="https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80" alt="Business stationery prints" class="h-44 w-full object-cover transition duration-500 group-hover:scale-105" />
                        <div class="p-5">
                            <h3 class="font-black text-slate-950">Business Essentials</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Business cards, letterheads, envelopes, ID cards and office documents.</p>
                        </div>
                    </a>

                    <a href="#" class="group overflow-hidden rounded-md border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-lg">
                        <img src="https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80" alt="Marketing flyers and posters" class="h-44 w-full object-cover transition duration-500 group-hover:scale-105" />
                        <div class="p-5">
                            <h3 class="font-black text-slate-950">Marketing Prints</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Flyers, posters, brochures, postcards, catalogues and menus.</p>
                        </div>
                    </a>

                    <a href="#" class="group overflow-hidden rounded-md border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-lg">
                        <img src="https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80" alt="Branded packaging materials" class="h-44 w-full object-cover transition duration-500 group-hover:scale-105" />
                        <div class="p-5">
                            <h3 class="font-black text-slate-950">Packaging</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Stickers, labels, paper bags, courier bags and product sleeves.</p>
                        </div>
                    </a>

                    <a href="#" class="group overflow-hidden rounded-md border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-lg">
                        <img src="https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80" alt="Event materials on conference chairs" class="h-44 w-full object-cover transition duration-500 group-hover:scale-105" />
                        <div class="p-5">
                            <h3 class="font-black text-slate-950">Event Materials</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Banners, roll-ups, name tags, programmes and branded giveaways.</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Popular Products</p>
                        <h2 class="mt-2 text-4xl text-slate-950">Start with the print essentials.</h2>
                    </div>
                    <a href="#" class="inline-flex w-fit rounded-md border border-slate-200 px-5 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">See All Products</a>
                </div>

                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <article class="rounded-md border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <img src="https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80" alt="Printed business cards" class="h-48 w-full rounded-t-md object-cover" />
                        <div class="p-5">
                            <h3 class="text-lg font-black text-slate-950">Business Cards</h3>
                            <p class="mt-2 text-sm text-slate-600">Premium cards for founders, teams and sales reps.</p>
                            <p class="mt-4 text-sm font-bold text-slate-500">starting at</p>
                            <p class="text-xl font-black text-pink-700">NGN 8,500 per 100</p>
                        </div>
                    </article>

                    <article class="rounded-md border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <img src="https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80" alt="Flyers and posters on a table" class="h-48 w-full rounded-t-md object-cover" />
                        <div class="p-5">
                            <h3 class="text-lg font-black text-slate-950">Flyers</h3>
                            <p class="mt-2 text-sm text-slate-600">Bright handouts for campaigns, launches and events.</p>
                            <p class="mt-4 text-sm font-bold text-slate-500">starting at</p>
                            <p class="text-xl font-black text-pink-700">NGN 35,000 per 500</p>
                        </div>
                    </article>

                    <article class="rounded-md border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <img src="https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80" alt="Branded stickers and packaging" class="h-48 w-full rounded-t-md object-cover" />
                        <div class="p-5">
                            <h3 class="text-lg font-black text-slate-950">Stickers</h3>
                            <p class="mt-2 text-sm text-slate-600">Labels and seals for packaging that feels finished.</p>
                            <p class="mt-4 text-sm font-bold text-slate-500">starting at</p>
                            <p class="text-xl font-black text-pink-700">NGN 12,000 per 100</p>
                        </div>
                    </article>

                    <article class="rounded-md border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <img src="https://images.unsplash.com/photo-1586282391129-76a6df230234?auto=format&fit=crop&w=900&q=80" alt="Printed brochures" class="h-48 w-full rounded-t-md object-cover" />
                        <div class="p-5">
                            <h3 class="text-lg font-black text-slate-950">Brochures</h3>
                            <p class="mt-2 text-sm text-slate-600">Folded print pieces for menus, catalogues and guides.</p>
                            <p class="mt-4 text-sm font-bold text-slate-500">starting at</p>
                            <p class="text-xl font-black text-pink-700">NGN 65,000 per 200</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-slate-950 py-16 text-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Why Printbuka</p>
                    <h2 class="mt-2 text-4xl">Prints that arrive ready to work.</h2>
                    <p class="mt-4 text-slate-300">Every order gets checked before production, printed on the right stock and finished for the job it needs to do.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <p class="text-3xl font-black text-pink-700">01</p>
                        <h3 class="mt-4 font-black">Fast Turnaround</h3>
                        <p class="mt-2 text-sm text-slate-600">Most orders move from approved artwork to delivery within days.</p>
                    </div>
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <p class="text-3xl font-black text-cyan-700">02</p>
                        <h3 class="mt-4 font-black">Sharp Finishing</h3>
                        <p class="mt-2 text-sm text-slate-600">Matte, gloss, folding and die-cut options for a polished result.</p>
                    </div>
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <p class="text-3xl font-black text-emerald-700">03</p>
                        <h3 class="mt-4 font-black">Helpful Support</h3>
                        <p class="mt-2 text-sm text-slate-600">Talk to a real print team before your money goes to press.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#f4fbfb] py-16">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                <img
                    src="https://images.unsplash.com/photo-1525909002-1b05e0c869d8?auto=format&fit=crop&w=1100&q=80"
                    alt="Designer arranging printed stationery"
                    class="h-[420px] w-full rounded-md object-cover shadow-xl shadow-slate-200"
                />
                <div class="flex flex-col justify-center">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Trusted by growing teams</p>
                    <h2 class="mt-2 text-4xl text-slate-950">Your print partner for launch week, campaign week and every week after.</h2>
                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-4xl font-black text-slate-950">15k+</p>
                            <p class="mt-1 text-sm font-bold text-slate-600">orders handled</p>
                        </div>
                        <div>
                            <p class="text-4xl font-black text-slate-950">24h</p>
                            <p class="mt-1 text-sm font-bold text-slate-600">file review</p>
                        </div>
                        <div>
                            <p class="text-4xl font-black text-slate-950">36</p>
                            <p class="mt-1 text-sm font-bold text-slate-600">states served</p>
                        </div>
                    </div>
                    <a href="#" class="mt-8 inline-flex w-fit rounded-md bg-slate-950 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">Start an Order</a>
                </div>
            </div>
        </section>
    </main>
@endsection
