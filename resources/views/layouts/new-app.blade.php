<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme:light;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    @php
        $siteName     = $siteSettings['site_name'] ?? config('app.name', 'Printbuka');
        $pageTitle    = trim(strip_tags($__env->yieldContent('title', $siteName)));
        $metaDesc     = trim(strip_tags($__env->yieldContent('meta_description', "Printbuka is Nigeria's online print shop for quality printing, branded gifts, and specialist production services.")));
        $canonicalUrl = $__env->yieldContent('canonical_url', url()->current());
        $ogImage      = $__env->yieldContent('og_image', asset('logo-04.svg'));
        $ogType       = $__env->yieldContent('og_type', 'website');
    @endphp
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="robots" content="{{ $__env->yieldContent('meta_robots', 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1') }}">
    <meta name="author" content="{{ $siteName }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="{{ $__env->yieldContent('twitter_card', 'summary_large_image') }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <title>{{ $pageTitle }}</title>

    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#EC268F">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>
<body class="bg-white text-slate-950 antialiased">

    <x-public-advertisements :placements="['popup', 'top_banner', 'floating_card']" />

    {{-- Announcement bar --}}
    <div class="bg-[#EC268F] text-white text-xs text-center py-2 px-4 font-medium tracking-wide">
        Free delivery on orders over ₦50,000 &nbsp;·&nbsp; Nationwide shipping in 3–7 days
        <a href="{{ route('shop.index') }}" class="underline underline-offset-2 ml-2 hover:text-pink-100">Shop now →</a>
    </div>

    {{-- Main nav --}}
    <nav id="main-nav" class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    <img src="{{ asset('prn-old-logo.svg') }}" alt="{{ $siteName }}" class="h-9">
                </a>

                {{-- Desktop nav links --}}
                <div class="hidden lg:flex items-center gap-1">

                    <a href="{{ route('home') }}"
                       class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-[#EC268F] rounded-md transition-colors">
                        Home
                    </a>

                    {{-- Products dropdown --}}
                    <div class="relative group">
                        <button class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 hover:text-[#EC268F] rounded-md transition-colors">
                            Products
                            <x-heroicon-o-chevron-down class="w-3.5 h-3.5 transition-transform group-hover:rotate-180" />
                        </button>
                        <div class="absolute top-full left-0 mt-1 w-52 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-1 group-hover:translate-y-0 transition-all duration-200 z-50">
                            <div class="p-2">
                                <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-pink-50 hover:text-[#EC268F] transition-colors text-sm text-gray-700">
                                    <x-heroicon-o-squares-2x2 class="w-4 h-4 text-gray-400" />
                                    All Categories
                                </a>
                                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-pink-50 hover:text-[#EC268F] transition-colors text-sm text-gray-700">
                                    <x-heroicon-o-tag class="w-4 h-4 text-gray-400" />
                                    Browse Products
                                </a>
                                <a href="{{ route('shop.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-pink-50 hover:text-[#EC268F] transition-colors text-sm text-gray-700">
                                    <x-heroicon-o-shopping-bag class="w-4 h-4 text-gray-400" />
                                    Ready-Made Shop
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Services dropdown --}}
                    <div class="relative group">
                        <button class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 hover:text-[#EC268F] rounded-md transition-colors">
                            Services
                            <x-heroicon-o-chevron-down class="w-3.5 h-3.5 transition-transform group-hover:rotate-180" />
                        </button>
                        <div class="absolute top-full left-0 mt-1 w-56 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-1 group-hover:translate-y-0 transition-all duration-200 z-50">
                            <div class="p-2">
                                <a href="{{ route('services.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-pink-50 hover:text-[#EC268F] transition-colors text-sm text-gray-700">
                                    <x-heroicon-o-printer class="w-4 h-4 text-gray-400" />
                                    Direct Image Printing
                                </a>
                                <a href="{{ route('services.show', 'uv-dtf') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-pink-50 hover:text-[#EC268F] transition-colors text-sm text-gray-700">
                                    <x-heroicon-o-sparkles class="w-4 h-4 text-gray-400" />
                                    UV-DTF Transfer
                                </a>
                                <a href="{{ route('services.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-pink-50 hover:text-[#EC268F] transition-colors text-sm text-gray-700">
                                    <x-heroicon-o-swatch class="w-4 h-4 text-gray-400" />
                                    DTF Printing
                                </a>
                                <a href="{{ route('services.show', 'laser-engraving') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-pink-50 hover:text-[#EC268F] transition-colors text-sm text-gray-700">
                                    <x-heroicon-o-bolt class="w-4 h-4 text-gray-400" />
                                    Laser Engraving
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('blog') }}"
                       class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-[#EC268F] rounded-md transition-colors">
                        Blog
                    </a>

                    <a href="{{ route('shop.index') }}"
                       class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-[#EC268F] rounded-md transition-colors">
                        Shop
                    </a>

                    <a href="{{ route('services.index') }}"
                       class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-[#EC268F] rounded-md transition-colors">
                        Contact
                    </a>
                </div>

                {{-- Desktop CTA --}}
                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="text-sm font-medium text-gray-700 hover:text-[#EC268F] px-3 py-2 transition-colors">
                            My Account
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                                Log out
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-700 hover:text-[#EC268F] px-3 py-2 transition-colors">
                            Log in
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-1.5 bg-[#EC268F] hover:bg-pink-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm shadow-pink-200">
                            Get Started
                            <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    @endauth
                </div>

                {{-- Mobile hamburger --}}
                <button id="nav-toggle"
                        aria-label="Toggle menu"
                        aria-expanded="false"
                        class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                    <x-heroicon-o-bars-3 class="w-5 h-5" id="nav-icon-open" />
                    <x-heroicon-o-x-mark class="w-5 h-5 hidden" id="nav-icon-close" />
                </button>

            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-100 bg-white">
            <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-pink-50 hover:text-[#EC268F] rounded-lg transition-colors">Home</a>
                <div class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-2">Products</div>
                <a href="{{ route('categories.index') }}" class="block px-3 py-2.5 text-sm text-gray-700 hover:bg-pink-50 hover:text-[#EC268F] rounded-lg transition-colors pl-6">All Categories</a>
                <a href="{{ route('products.index') }}" class="block px-3 py-2.5 text-sm text-gray-700 hover:bg-pink-50 hover:text-[#EC268F] rounded-lg transition-colors pl-6">Browse Products</a>
                <a href="{{ route('shop.index') }}" class="block px-3 py-2.5 text-sm text-gray-700 hover:bg-pink-50 hover:text-[#EC268F] rounded-lg transition-colors pl-6">Ready-Made Shop</a>
                <div class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-2">Services</div>
                <a href="{{ route('services.index') }}" class="block px-3 py-2.5 text-sm text-gray-700 hover:bg-pink-50 hover:text-[#EC268F] rounded-lg transition-colors pl-6">Direct Image Printing</a>
                <a href="{{ route('services.show', 'uv-dtf') }}" class="block px-3 py-2.5 text-sm text-gray-700 hover:bg-pink-50 hover:text-[#EC268F] rounded-lg transition-colors pl-6">UV-DTF Transfer</a>
                <a href="{{ route('services.index') }}" class="block px-3 py-2.5 text-sm text-gray-700 hover:bg-pink-50 hover:text-[#EC268F] rounded-lg transition-colors pl-6">DTF Printing</a>
                <a href="{{ route('services.show', 'laser-engraving') }}" class="block px-3 py-2.5 text-sm text-gray-700 hover:bg-pink-50 hover:text-[#EC268F] rounded-lg transition-colors pl-6">Laser Engraving</a>
                <a href="{{ route('blog') }}" class="block px-3 py-2.5 text-sm text-gray-700 hover:bg-pink-50 hover:text-[#EC268F] rounded-lg transition-colors">Blog</a>
                <div class="pt-3 pb-1 border-t border-gray-100 flex flex-col gap-2 mt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-pink-50 rounded-lg transition-colors">My Account</a>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors text-center border border-gray-200">Log in</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2.5 text-sm font-semibold text-white bg-[#EC268F] hover:bg-pink-700 rounded-lg transition-colors text-center">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @auth
    @include('layouts.partials.breadcrumbs', ['rootLabel' => 'Home', 'rootRoute' => 'home'])
    @endauth

    <div id="app">
        @yield('content')
    </div>

    <x-public-advertisements :placements="['inline_banner', 'footer_banner']" />

    @include('layouts.guest.footer')

    <x-turnstile-auto />
    <x-form-icons />
    @livewireScripts
    <x-shop-popup />

    <script>
        (function () {
            const toggle    = document.getElementById('nav-toggle');
            const menu      = document.getElementById('mobile-menu');
            const iconOpen  = document.getElementById('nav-icon-open');
            const iconClose = document.getElementById('nav-icon-close');
            if (!toggle) return;
            toggle.addEventListener('click', function () {
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', String(!expanded));
                menu.classList.toggle('hidden');
                iconOpen.classList.toggle('hidden');
                iconClose.classList.toggle('hidden');
            });
        })();
    </script>
</body>
</html>
