@php
$username = auth()->check()
    ? auth()->user()->first_name . ' ' . auth()->user()->last_name
    : 'Guest';

$profileImg = auth()->check()
    ? auth()->user()->photo
    : 'default.png';
@endphp

{{-- ===== TOP INFO BAR ===== --}}
<div class="hidden md:block bg-slate-950 text-slate-300 text-xs font-semibold">
    <div class="mx-auto max-w-7xl px-6 py-2 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="{{ route('services.index') }}" class="hover:text-white transition">Services</a>
            <a href="{{ route('orders.track') }}" class="hover:text-white transition">Track Order</a>
            <a href="{{ route('quotes.create') }}" class="hover:text-white transition">Get Free Quote</a>
        </div>
        <div class="flex items-center gap-6">
            <span>📞 {{ $siteSettings['contact_phone'] ?? '08035245784, 09054784526' }}</span>
            <span>✉️ {{ $siteSettings['contact_email'] ?? 'sales@printbuka.com.ng' }}</span>
        </div>
    </div>
</div>

{{-- ===== MAIN NAV ===== --}}
<div class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100 shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="navbar min-h-16 px-0 gap-2">

            {{-- Logo --}}
            <div class="navbar-start">
                {{-- Mobile drawer trigger --}}
                <label for="mobile-drawer" class="btn btn-ghost btn-sm lg:hidden mr-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </label>

                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('logo.png') }}" class="h-9 w-auto" alt="Printbuka" />
                </a>
            </div>

            {{-- Desktop nav links --}}
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal gap-1 px-1 text-sm font-bold text-slate-700">

                    @auth
                        <li><a href="{{ route('invoice.index') }}" class="rounded-lg hover:text-pink-600 hover:bg-pink-50">Invoices & Receipts</a></li>
                    @else
                        {{-- Products dropdown --}}
                        <li>
                            <details class="group">
                                <summary class="rounded-lg hover:text-pink-600 hover:bg-pink-50 cursor-pointer">
                                    All Products
                                    <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </summary>
                                <ul class="bg-white rounded-2xl shadow-2xl shadow-slate-900/10 border border-slate-100 p-4 w-[620px] -left-24 z-50">
                                    <li class="list-none">
                                        <div class="grid grid-cols-[1fr_1.4fr] gap-5 p-2">
                                            <div class="bg-base-200 rounded-xl p-5">
                                                <p class="text-xs font-black uppercase tracking-wide text-pink-600 mb-2">Product Categories</p>
                                                <h3 class="text-xl font-black text-slate-950 leading-snug mb-3">Print, brand and gift from one place.</h3>
                                                <p class="text-xs text-slate-500 leading-relaxed mb-4">Business printing, packaging, event materials and branded gifts — all in one print shop.</p>
                                                <a href="{{ route('categories.index') }}" class="btn btn-sm bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">View Categories</a>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <a href="{{ route('categories.index') }}" class="block rounded-xl border border-slate-200 p-4 hover:border-pink-300 hover:bg-pink-50 transition">
                                                    <p class="text-xs font-black text-pink-600 uppercase mb-1">Print</p>
                                                    <p class="font-black text-slate-950 text-sm">Business Essentials</p>
                                                    <p class="text-xs text-slate-500 mt-1">Cards, letterheads, ID cards, envelopes.</p>
                                                </a>
                                                <a href="{{ route('categories.index') }}" class="block rounded-xl border border-slate-200 p-4 hover:border-cyan-300 hover:bg-cyan-50 transition">
                                                    <p class="text-xs font-black text-cyan-600 uppercase mb-1">Campaigns</p>
                                                    <p class="font-black text-slate-950 text-sm">Marketing Prints</p>
                                                    <p class="text-xs text-slate-500 mt-1">Flyers, posters, brochures, menus.</p>
                                                </a>
                                                <a href="{{ route('categories.index') }}" class="block rounded-xl border border-slate-200 p-4 hover:border-emerald-300 hover:bg-emerald-50 transition">
                                                    <p class="text-xs font-black text-emerald-600 uppercase mb-1">Packaging</p>
                                                    <p class="font-black text-slate-950 text-sm">Labels & Bags</p>
                                                    <p class="text-xs text-slate-500 mt-1">Stickers, labels, bags, sleeves.</p>
                                                </a>
                                                <a href="{{ route('categories.index') }}" class="block rounded-xl border border-slate-200 p-4 hover:border-amber-300 hover:bg-amber-50 transition">
                                                    <p class="text-xs font-black text-amber-600 uppercase mb-1">Gifts</p>
                                                    <p class="font-black text-slate-950 text-sm">Branded Gifts</p>
                                                    <p class="text-xs text-slate-500 mt-1">Mugs, shirts, tote bags, gift sets.</p>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </details>
                        </li>

                        <li><a href="{{ route('services.index') }}" class="rounded-lg hover:text-pink-600 hover:bg-pink-50">Services</a></li>
                        <li><a href="{{ route('partners.create') }}" class="rounded-lg hover:text-pink-600 hover:bg-pink-50">Become a Partner</a></li>
                        <li><a href="{{ route('blog') }}" class="rounded-lg hover:text-pink-600 hover:bg-pink-50">Blog</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Right side --}}
            <div class="navbar-end gap-2">
                {{-- Search --}}
                <div class="hidden lg:flex dropdown dropdown-end">
                    <button tabindex="0" class="btn btn-sm btn-ghost border border-slate-200 bg-slate-50 hover:bg-white text-slate-500 hover:text-slate-800 font-bold gap-2 px-4">
                        Search products
                        <kbd class="kbd kbd-xs text-pink-600 bg-pink-50 border-pink-200">/</kbd>
                    </button>
                    <div tabindex="0" class="dropdown-content z-50 mt-2 w-[400px] bg-white rounded-2xl shadow-2xl shadow-slate-900/10 border border-slate-100 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-pink-600 mb-3">Quick Search</p>
                        <livewire:product.search variant="nav" />
                    </div>
                </div>

               @auth
    <livewire:notification-bell />
    
    {{-- User Dropdown Menu --}}
    <div class="dropdown dropdown-end">
        <label tabindex="0" class="btn btn-ghost btn-sm gap-2 font-bold text-slate-700 hover:text-pink-600">
            <div class="avatar">
                <div class="w-7 rounded-full">
                    <img src="{{ $profileImg ?? asset('favicon.png') }}" alt="{{ $username }}" />
                </div>
            </div>
            <span class="hidden md:inline">{{ $username }}</span>
            <svg class="h-4 w-4 hidden md:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </label>
        
        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow-2xl bg-white rounded-box w-56 mt-2 border border-slate-100">
            {{-- User Info Header --}}
            <li class="menu-title text-slate-400 text-xs border-b border-slate-100 pb-2 mb-2">
                <span>Signed in as</span>
                <span class="text-slate-700 font-bold text-sm block truncate">{{ $username }}</span>
            </li>
            
            {{-- Edit Profile Link --}}
            <li>
                <a href="{{ route('profile.edit') }}" class="gap-3 text-slate-700 hover:text-pink-600 hover:bg-pink-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Edit Profile
                </a>
            </li>
            
            {{-- Support/Tickets Link --}}
            <li>
                <a href="{{ route('support.tickets.index') ?? '#' }}" class="gap-3 text-slate-700 hover:text-pink-600 hover:bg-pink-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Support Tickets
                </a>
            </li>
            
            {{-- Divider --}}
            <li class="border-t border-slate-100 my-1"></li>
            
            {{-- Logout Link --}}
            <li>
                <form action="{{ route('logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left gap-3 text-red-600 hover:bg-red-50 hover:text-red-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
@else
    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm font-bold text-slate-700 hover:text-pink-600 hidden sm:inline-flex">Sign In</a>
    <a href="{{ route('register') }}" class="btn btn-sm bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Create Account</a>
@endauth
            </div>

        </div>
    </div>
</div>

{{-- ===== MOBILE DRAWER ===== --}}
<input id="mobile-drawer" type="checkbox" class="drawer-toggle" />
<div class="drawer-side z-[60]">
    <label for="mobile-drawer" class="drawer-overlay"></label>
    <div class="bg-white min-h-full w-72 p-5 shadow-2xl">
        <div class="flex items-center justify-between mb-8">
            <img src="{{ asset('logo.png') }}" class="h-8 w-auto" alt="Printbuka" />
            <label for="mobile-drawer" class="btn btn-ghost btn-sm btn-circle">✕</label>
        </div>

        <ul class="menu menu-lg gap-1 p-0 font-bold text-slate-700">
            <li><a href="{{ route('products.index') }}" class="hover:text-pink-600 hover:bg-pink-50">All Products</a></li>
            <li><a href="{{ route('categories.index') }}" class="hover:text-pink-600 hover:bg-pink-50">Categories</a></li>
            <li><a href="{{ route('services.index') }}" class="hover:text-pink-600 hover:bg-pink-50">Services</a></li>
            <li><a href="{{ route('quotes.create') }}" class="hover:text-pink-600 hover:bg-pink-50">Get a Quote</a></li>
            <li><a href="{{ route('orders.track') }}" class="hover:text-pink-600 hover:bg-pink-50">Track Order</a></li>
            <li><a href="{{ route('partners.create') }}" class="hover:text-pink-600 hover:bg-pink-50">Become a Partner</a></li>
            <li><a href="{{ route('blog') }}" class="hover:text-pink-600 hover:bg-pink-50">Blog</a></li>
        </ul>

        <div class="divider"></div>

        @auth
            <a href="{{ route('profile.edit') }}" class="btn btn-outline btn-block font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 mb-3">My Profile</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black btn-block">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline btn-block font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 mb-3">Sign In</a>
            <a href="{{ route('register') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black btn-block">Create Account</a>
        @endauth

        <div class="mt-6 text-xs text-slate-400 space-y-1">
            <p>📞 {{ $siteSettings['contact_phone'] ?? '08035245784' }}</p>
            <p>✉️ {{ $siteSettings['contact_email'] ?? 'sales@printbuka.com.ng' }}</p>
        </div>
    </div>
</div>