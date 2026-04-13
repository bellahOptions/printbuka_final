<div class="hidden border-b border-slate-100 bg-white md:block">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3 text-sm font-semibold text-slate-600">
        <div class="flex items-center gap-6">
            <a href="#" class="transition hover:text-pink-600">Gift Suggestions</a>
            <a href="{{ route('orders.track') }}" class="transition hover:text-pink-600">Track Order</a>
            <a href="#" class="transition hover:text-pink-600">Cost Calculator</a>
        </div>

        <div class="flex items-center gap-6">
            <span>Call: 08035245784, 09054784526</span>
            <span>Email: sales@printbuka.com.ng</span>
        </div>
    </div>
</div>

<nav class="sticky top-0 z-50 border-b border-slate-100 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-5 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('logo.png') }}" class="h-10 w-auto" alt="Printbuka Logo" />
        </a>

        <div class="hidden items-center gap-8 text-sm font-bold text-slate-700 md:flex">
            <div class="group">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 py-3 transition hover:text-pink-600 focus:text-pink-600">
                    All Products
                    <span class="text-xs">v</span>
                </a>

                <div class="invisible absolute left-0 top-full w-full translate-y-3 border-t border-slate-100 bg-white opacity-0 shadow-2xl shadow-slate-900/10 transition duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                    <div class="mx-auto grid max-w-7xl gap-8 px-6 py-8 lg:grid-cols-[1fr_2fr_1fr] lg:px-8">
                        <div class="rounded-md bg-[#f4fbfb] p-6">
                            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Product Categories</p>
                            <h2 class="mt-3 text-3xl font-black leading-tight text-slate-950">Print, brand and gift from one place.</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Find print products, packaging and branded gifts for business launches, events and team moments.</p>
                            <a href="{{ route('categories.index') }}" class="mt-6 inline-flex rounded-md bg-pink-600 px-4 py-3 text-sm font-black text-white transition hover:bg-pink-700">View Categories</a>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <a href="{{ route('categories.index') }}" class="rounded-md border border-slate-200 p-5 transition hover:border-pink-300 hover:bg-pink-50">
                                <p class="text-xs font-black uppercase tracking-wide text-pink-700">Print</p>
                                <h3 class="mt-2 text-base font-black text-slate-950">Business Essentials</h3>
                                <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">Business cards, letterheads, ID cards and envelopes.</p>
                            </a>
                            <a href="{{ route('categories.index') }}" class="rounded-md border border-slate-200 p-5 transition hover:border-cyan-300 hover:bg-cyan-50">
                                <p class="text-xs font-black uppercase tracking-wide text-cyan-700">Campaigns</p>
                                <h3 class="mt-2 text-base font-black text-slate-950">Marketing Prints</h3>
                                <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">Flyers, posters, brochures, postcards and menus.</p>
                            </a>
                            <a href="{{ route('categories.index') }}" class="rounded-md border border-slate-200 p-5 transition hover:border-emerald-300 hover:bg-emerald-50">
                                <p class="text-xs font-black uppercase tracking-wide text-emerald-700">Packaging</p>
                                <h3 class="mt-2 text-base font-black text-slate-950">Labels and Bags</h3>
                                <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">Stickers, labels, paper bags, courier bags and sleeves.</p>
                            </a>
                            <a href="{{ route('categories.index') }}" class="rounded-md border border-slate-200 p-5 transition hover:border-amber-300 hover:bg-amber-50">
                                <p class="text-xs font-black uppercase tracking-wide text-amber-700">Gifts</p>
                                <h3 class="mt-2 text-base font-black text-slate-950">Branded Gifts</h3>
                                <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">Mugs, shirts, tote bags, notebooks and gift sets.</p>
                            </a>
                        </div>

                        <div class="rounded-md border border-slate-200 p-6">
                            <p class="text-sm font-black uppercase tracking-wide text-slate-500">Popular Now</p>
                            <div class="mt-4 space-y-4">
                                <a href="{{ route('products.index') }}#catalog" class="block">
                                    <span class="block font-black text-slate-950">Flyers</span>
                                    <span class="text-sm font-semibold text-slate-500">From NGN 35,000</span>
                                </a>
                                <a href="{{ route('products.index') }}#catalog" class="block">
                                    <span class="block font-black text-slate-950">Business Cards</span>
                                    <span class="text-sm font-semibold text-slate-500">From NGN 8,500</span>
                                </a>
                                <a href="{{ route('products.index') }}#catalog" class="block">
                                    <span class="block font-black text-slate-950">Branded Mugs</span>
                                    <span class="text-sm font-semibold text-slate-500">For client gifts</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{ route('categories.index') }}" class="transition hover:text-pink-600">Gifts</a>
            <a href="{{ route('partners.create') }}" class="transition hover:text-pink-600">Become a Partner</a>
        </div>

        <div class="hidden flex-1 justify-end lg:flex">
            <div class="group/search relative w-full max-w-xs">
                <button type="button" class="flex min-h-11 w-full items-center justify-between rounded-md border border-slate-200 bg-slate-50 px-4 text-left text-sm font-bold text-slate-500 transition hover:border-pink-300 hover:bg-white hover:text-slate-800">
                    <span>Search products</span>
                    <span class="text-pink-600">/</span>
                </button>
                <div class="invisible absolute right-0 top-full z-50 mt-3 w-[420px] max-w-[calc(100vw-2rem)] translate-y-2 rounded-md border border-slate-100 bg-white p-4 opacity-0 shadow-2xl shadow-slate-900/10 transition duration-200 group-hover/search:visible group-hover/search:translate-y-0 group-hover/search:opacity-100 group-focus-within/search:visible group-focus-within/search:translate-y-0 group-focus-within/search:opacity-100">
                    <p class="mb-3 text-xs font-black uppercase tracking-wide text-pink-700">Quick Search</p>
                    <livewire:product.search variant="nav" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 text-sm font-bold">
            @auth
                @if (auth()->user()->hasAdminAccess())
                    <a href="{{ route('admin.dashboard') }}" class="hidden px-4 py-2 text-slate-700 transition hover:text-pink-600 xl:inline-flex">Admin</a>
                @endif
                <a href="{{ route('dashboard') }}" class="hidden px-4 py-2 text-slate-700 transition hover:text-pink-600 sm:inline-flex">Dashboard</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-md bg-pink-600 px-4 py-2 text-white shadow-sm shadow-pink-200 transition hover:bg-pink-700">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hidden px-4 py-2 text-slate-700 transition hover:text-pink-600 sm:inline-flex">Sign In</a>
                <a href="{{ route('register') }}" class="rounded-md bg-pink-600 px-4 py-2 text-white shadow-sm shadow-pink-200 transition hover:bg-pink-700">Create Account</a>
            @endauth
        </div>
    </div>
</nav>
