<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">
        <title>@yield('title', $title ?? ($siteSettings['site_name'] ?? config('app.name')))</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            .sidebar-link {
                position: relative;
                overflow: hidden;
            }
            .sidebar-link::before {
                content: '';
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 3px;
                height: 0;
                background: linear-gradient(180deg, #be185d 0%, #9d174d 100%);
                border-radius: 0 4px 4px 0;
                transition: height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar-link:hover::before {
                height: 60%;
            }
            .sidebar-link.active {
                background: linear-gradient(90deg, rgba(190, 24, 93, 0.08) 0%, transparent 100%);
                color: #be185d;
            }
            .sidebar-link.active::before {
                height: 60%;
            }
            .glass-effect {
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }
            .card-hover {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .card-hover:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.05), 0 8px 10px -6px rgb(0 0 0 / 0.02);
                border-color: #fbcfe8;
            }
            .status-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.7rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.025em;
                transition: all 0.2s ease;
            }
            .status-badge::before {
                content: '';
                width: 6px;
                height: 6px;
                border-radius: 50%;
                margin-right: 6px;
                background: currentColor;
                opacity: 0.7;
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            @keyframes pulse {
                0%, 100% { opacity: 0.7; }
                50% { opacity: 0.3; }
            }
            .table-row-hover {
                transition: background-color 0.2s ease;
            }
            .table-row-hover:hover {
                background: linear-gradient(90deg, rgba(249, 168, 212, 0.03) 0%, transparent 100%);
            }
            .btn-primary {
                position: relative;
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .btn-primary::after {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at center, rgba(255,255,255,0.2) 0%, transparent 70%);
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .btn-primary:hover::after {
                opacity: 1;
            }
            .btn-primary:active {
                transform: scale(0.98);
            }
            .fade-in-up {
                animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                opacity: 0;
            }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            .section-delay-1 { animation-delay: 0.05s; }
            .section-delay-2 { animation-delay: 0.1s; }
            .section-delay-3 { animation-delay: 0.15s; }
            .section-delay-4 { animation-delay: 0.2s; }

            /* Sidebar slide-in on mobile */
            #admin-sidebar-panel {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            #admin-sidebar-panel.sidebar-hidden {
                transform: translateX(-100%);
            }
            #admin-sidebar-panel.sidebar-visible {
                transform: translateX(0);
            }

            /* Overlay backdrop */
            #sidebar-overlay {
                transition: opacity 0.3s ease;
            }

            /* Mobile header safe area */
            .mobile-header {
                padding-top: 10px;
            }

            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #be185d; }

            /* Smooth table scroll hint on mobile */
            .table-scroll-container {
                position: relative;
            }
            .table-scroll-container::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 32px;
                background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
                pointer-events: none;
                border-radius: 0 0.75rem 0.75rem 0;
            }
            @media (min-width: 1024px) {
                .table-scroll-container::after { display: none; }
            }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-950 antialiased">

        <!-- ============================================================
             MOBILE: Fixed top navbar + slide-out sidebar drawer
             DESKTOP: Static sidebar rail + content area side-by-side
             ============================================================ -->

        <!-- Mobile Top Bar (hidden on desktop) -->
        <header class="mobile-header sticky top-0 z-50 flex items-center justify-between gap-3 border-b border-slate-200/60 bg-white/90 px-4 py-3 backdrop-blur-xl glass-effect lg:hidden">
            <a href="{{ route('admin.dashboard') }}" class="shrink-0">
                <img src="{{ asset('logo.png') }}" alt="Printbuka" class="h-9 w-auto">
            </a>

            <div class="flex items-center gap-2 min-w-0">
                <!-- Notification bell -->
                <div class="relative shrink-0">
                    <livewire:notification-bell />
                </div>

                <!-- Hamburger -->
                <button
                    id="mobile-sidebar-toggle"
                    type="button"
                    aria-label="Open navigation menu"
                    aria-expanded="false"
                    aria-controls="admin-sidebar-panel"
                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:border-pink-300 hover:text-pink-700 shrink-0"
                >
                    <svg data-open-icon class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg data-close-icon class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Backdrop overlay (mobile only) -->
        <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-slate-950/40 opacity-0 pointer-events-none lg:hidden" aria-hidden="true"></div>

        <!-- Layout wrapper -->
        <div class="lg:flex lg:min-h-screen">

            <!-- ===== SIDEBAR ===== -->
            <!-- Mobile: fixed full-height drawer; Desktop: static sticky rail -->
            <aside
                id="admin-sidebar-panel"
                class="sidebar-hidden fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200/60 bg-white/95 backdrop-blur-xl shadow-2xl lg:shadow-none
                       lg:static lg:sidebar-visible lg:z-auto lg:w-[280px] lg:flex lg:flex-col lg:min-h-screen lg:translate-x-0"
            >
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-5">
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3">
                        <img src="{{ asset('logo.png') }}" alt="Printbuka" class="h-10 w-auto transition-transform duration-300 group-hover:scale-105">
                    </a>
                    <!-- Close button inside drawer (mobile only) -->
                    <button
                        id="sidebar-close-btn"
                        type="button"
                        aria-label="Close navigation menu"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-pink-300 hover:text-pink-700 lg:hidden"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Scrollable nav body -->
                <div class="flex flex-1 flex-col overflow-y-auto px-6 py-6">
                    <p class="text-[0.7rem] font-black uppercase tracking-wider text-slate-400 flex items-center gap-2">
                        <span class="w-6 h-px bg-gradient-to-r from-pink-300 to-transparent"></span>
                        Admin Menu
                    </p>

                    @php
                        $sidebarLinkClass = function ($patterns): string {
                            $patterns = (array) $patterns;
                            return 'sidebar-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-black text-slate-700 transition-all duration-300 hover:bg-gradient-to-r hover:from-pink-50/80 hover:to-transparent hover:text-pink-700'.(request()->routeIs(...$patterns) ? ' active' : '');
                        };
                        $canSeeStaffMenu = in_array(auth()->user()?->role, ['hr', 'super_admin'], true);
                    @endphp

                    <nav class="mt-5 space-y-1.5">
                        <a href="{{ route('admin.dashboard') }}" class="{{ $sidebarLinkClass('admin.dashboard') }}">
                            <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="{{ $sidebarLinkClass('admin.orders.*') }}">
                            <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Jobs
                        </a>
                        <a href="{{ route('admin.support.index') }}" class="{{ $sidebarLinkClass('admin.support.*') }}">
                            <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.806L3 20l1.2-3.6A7.42 7.42 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            IT Support Tickets
                        </a>
                        @if ($canSeeStaffMenu)
                            <a href="{{ route('admin.staff.index') }}" class="{{ $sidebarLinkClass('admin.staff.*') }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Staff
                            </a>
                        @endif
                        @if (auth()->user()?->canAdmin('training.manage'))
                            <a href="{{ route('admin.training.index') }}" class="{{ $sidebarLinkClass('admin.training.*') }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0119 15.5c0 1.41-.241 2.764-.684 4.023C16.561 20.44 14.414 21 12 21s-4.561-.56-6.316-1.477A12.02 12.02 0 015 15.5c0-1.711.356-3.34.999-4.817L12 14z"/>
                                </svg>
                                Training
                            </a>
                        @endif
                        @if (auth()->user()?->canAdmin('customers.manage'))
                            <a href="{{ route('admin.customers.index') }}" class="{{ $sidebarLinkClass('admin.customers.*') }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V8a2 2 0 00-2-2h-3m-7 14H5a2 2 0 01-2-2V8a2 2 0 012-2h3m4 14v-4a2 2 0 00-2-2H8a2 2 0 00-2 2v4m6 0h2m-6 0H6m6-14V4a2 2 0 00-2-2H8a2 2 0 00-2 2v2m6 0H6"/>
                                </svg>
                                Customers
                            </a>
                        @endif
                        @if (auth()->user()?->canAdmin('invoices.manage'))
                            <a href="{{ route('admin.invoices.index') }}" class="{{ $sidebarLinkClass('admin.invoices.*') }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Invoices
                            </a>
                        @endif
                        @if (auth()->user()?->canAdmin('newsletters.manage'))
                            <a href="{{ route('admin.newsletters.index') }}" class="{{ $sidebarLinkClass('admin.newsletters.*') }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Newsletters
                            </a>
                        @endif
                        @if (auth()->user()?->canAdmin('finance.view'))
                            <a href="{{ route('admin.finance.index') }}" class="{{ $sidebarLinkClass('admin.finance.*') }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Finance
                            </a>
                        @endif
                        @if (auth()->user()?->canAdmin('*'))
                            <a href="{{ route('admin.products.index') }}" class="{{ $sidebarLinkClass(['admin.products.*', 'admin.product-categories.*']) }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Products
                            </a>
                            <a href="{{ route('admin.notifications.index') }}" class="{{ $sidebarLinkClass('admin.notifications.*') }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Notifications
                            </a>
                            @if (auth()->user()?->role === 'super_admin')
                                <a href="{{ route('admin.advertisements.index') }}" class="{{ $sidebarLinkClass('admin.advertisements.*') }}">
                                    <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592L5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                    Advertisements
                                </a>
                            @endif
                            <a href="{{ route('admin.settings.edit') }}" class="{{ $sidebarLinkClass('admin.settings.*') }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Settings
                            </a>
                            @if (auth()->user()?->role === 'super_admin')
                                <a href="{{ route('admin.activity-logs.index') }}" class="{{ $sidebarLinkClass('admin.activity-logs.*') }}">
                                    <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2V7m3 10v-4m3 8H6a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>
                                    </svg>
                                    Audit Logs
                                </a>
                                <a href="{{ route('admin.policies.edit') }}" class="{{ $sidebarLinkClass('admin.policies.*') }}">
                                    <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z"/>
                                    </svg>
                                    Policies
                                </a>
                            @endif
                        @endif
                    </nav>

                    <!-- Sidebar Footer -->
                    <div class="mt-auto pt-8">
                        <div class="rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100 p-4 border border-slate-200/60">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-wider">Quick Actions</p>
                            <div class="mt-3 space-y-2">
                                @if (auth()->user()->canAdmin('orders.create'))
                                    <a href="{{ route('admin.orders.create') }}" class="flex items-center gap-2 text-sm font-bold text-pink-700 hover:text-pink-800 transition-colors">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        New Job
                                    </a>
                                @endif
                                <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 text-sm font-bold text-slate-700 hover:text-pink-700 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A8.966 8.966 0 0112 15c2.607 0 4.955 1.11 6.621 2.878M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    My Profile
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2 text-sm font-bold text-slate-700 hover:text-pink-700 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    View All Jobs
                                </a>

                                <!-- Logout inside sidebar on mobile for easy access -->
                                <form action="{{ route('logout') }}" method="POST" class="pt-1">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ===== MAIN CONTENT ===== -->
            <div class="flex min-w-0 flex-1 flex-col">

                <!-- Desktop Header (hidden on mobile — mobile uses the top bar above) -->
                <header class="sticky top-0 z-40 hidden border-b border-slate-200/60 bg-white/80 backdrop-blur-xl glass-effect lg:block">
                    <div class="px-6 py-4 lg:px-8">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                @php($currentAdmin = auth()->user())
                                @if ($currentAdmin?->profilePhotoUrl())
                                    <img src="{{ $currentAdmin->profilePhotoUrl() }}" alt="{{ $currentAdmin->displayName() }}" class="h-12 w-12 shrink-0 rounded-full border border-slate-200 object-cover shadow-sm">
                                @else
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-sm font-black text-slate-700">
                                        {{ $currentAdmin?->profileInitials() }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="relative flex h-2 w-2 shrink-0">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-pink-600"></span>
                                        </span>
                                        <p class="text-[0.65rem] font-black uppercase tracking-wider text-pink-700 truncate">
                                            {{ config('printbuka_admin.role_labels.'.$currentAdmin?->role, $currentAdmin?->role) }}
                                        </p>
                                    </div>
                                    <p class="mt-0.5 text-xl font-black text-slate-950 tracking-tight truncate">{{ $currentAdmin?->displayName() }}</p>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-3">
                                <div class="relative">
                                    <livewire:notification-bell />
                                </div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="btn-primary group relative inline-flex items-center gap-2 rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-black text-white transition-all duration-300 hover:bg-pink-700 hover:shadow-lg hover:shadow-pink-700/20">
                                        <span>Logout</span>
                                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-pink-500 via-pink-600 to-pink-500 animate-pulse"></div>
                </header>

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="-mx-4 -mt-4 mb-4 sm:-mx-6 sm:-mt-6 sm:mb-6 lg:-mx-8 lg:-mt-8 lg:mb-8">
                        @include('layouts.partials.breadcrumbs', [
                            'rootLabel' => 'Dashboard',
                            'rootRoute' => 'admin.dashboard',
                            'skipSegments' => ['admin'],
                        ])
                    </div>
                    @if (session('warning'))
                        <div id="staff-duty-modal" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/65 p-4">
                            <div class="w-full max-w-lg rounded-2xl border border-amber-200 bg-white p-6 shadow-2xl">
                                <p class="text-xs font-black uppercase tracking-wider text-amber-700">Access Restricted</p>
                                <h2 class="mt-2 text-2xl font-black text-slate-950">Website view is disabled during duty hours</h2>
                                <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">{{ session('warning') }}</p>
                                <div class="mt-6 flex justify-end">
                                    <button type="button" data-close-duty-modal class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-black text-white transition hover:bg-slate-700">
                                        Continue To Dashboard
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>

        <script>
            (() => {
                const toggleBtn   = document.getElementById('mobile-sidebar-toggle');
                const closeBtn    = document.getElementById('sidebar-close-btn');
                const panel       = document.getElementById('admin-sidebar-panel');
                const overlay     = document.getElementById('sidebar-overlay');
                const openIcon    = toggleBtn?.querySelector('[data-open-icon]');
                const closeIcon   = toggleBtn?.querySelector('[data-close-icon]');
                const lgQuery     = window.matchMedia('(min-width: 1024px)');

                if (!panel) return;

                const open = () => {
                    panel.classList.remove('sidebar-hidden');
                    panel.classList.add('sidebar-visible');
                    overlay.classList.remove('opacity-0', 'pointer-events-none');
                    overlay.classList.add('opacity-100');
                    document.body.style.overflow = 'hidden';
                    toggleBtn?.setAttribute('aria-expanded', 'true');
                    openIcon?.classList.add('hidden');
                    closeIcon?.classList.remove('hidden');
                };

                const close = () => {
                    panel.classList.add('sidebar-hidden');
                    panel.classList.remove('sidebar-visible');
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                    overlay.classList.remove('opacity-100');
                    document.body.style.overflow = '';
                    toggleBtn?.setAttribute('aria-expanded', 'false');
                    openIcon?.classList.remove('hidden');
                    closeIcon?.classList.add('hidden');
                };

                const handleBreakpoint = () => {
                    if (lgQuery.matches) {
                        // Always show on desktop, reset body scroll
                        panel.classList.remove('sidebar-hidden');
                        panel.classList.add('sidebar-visible');
                        overlay.classList.add('opacity-0', 'pointer-events-none');
                        document.body.style.overflow = '';
                    } else {
                        close();
                    }
                };

                handleBreakpoint();

                toggleBtn?.addEventListener('click', () => {
                    const isOpen = toggleBtn.getAttribute('aria-expanded') === 'true';
                    isOpen ? close() : open();
                });

                closeBtn?.addEventListener('click', close);
                overlay?.addEventListener('click', close);

                // Close drawer when a nav link is tapped on mobile
                panel.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (!lgQuery.matches) close();
                    });
                });

                if (typeof lgQuery.addEventListener === 'function') {
                    lgQuery.addEventListener('change', handleBreakpoint);
                } else {
                    lgQuery.addListener(handleBreakpoint);
                }
            })();

            (() => {
                const modal = document.getElementById('staff-duty-modal');
                if (!modal) return;

                const close = () => modal.remove();

                modal.querySelector('[data-close-duty-modal]')?.addEventListener('click', close);
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        close();
                    }
                });
            })();
        </script>
        <x-form-icons />
        @livewireScripts
    </body>
</html>
