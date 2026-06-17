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
            /* ── Admin Shell ─────────────────────────────────────── */
            :root {
                --sidebar-w: 264px;
                --topbar-h:  60px;
            }

            /* Sidebar slide on mobile */
            @media (max-width: 1023px) {
                #pb-sidebar { transform: translateX(-100%); }
                #pb-sidebar.open { transform: translateX(0); }
            }
            @media (min-width: 1024px) {
                #pb-sidebar { transform: none !important; }
            }
            #pb-overlay { transition: opacity .25s ease; }

            /* Active nav indicator */
            .pb-nav-item.active {
                background: rgba(190,24,93,0.07);
                color: #be185d;
                font-weight: 600;
            }
            .pb-nav-item.active .pb-nav-icon { color: #be185d; }
            .pb-nav-item.active::before {
                content: '';
                position: absolute; left: 0; top: 50%; transform: translateY(-50%);
                width: 3px; height: 60%; background: #be185d;
                border-radius: 0 3px 3px 0;
            }

            /* Nav group label */
            .pb-nav-group {
                padding: 0.5rem 0.75rem 0.25rem;
                font-size: 0.625rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                color: #94a3b8;
            }

            /* Sidebar scrollbar */
            #pb-sidebar-body::-webkit-scrollbar { width: 4px; }
            #pb-sidebar-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

            /* Topbar gradient line */
            .topbar-line::after {
                content: '';
                position: absolute; bottom: 0; left: 0; right: 0; height: 2px;
                background: linear-gradient(90deg, #be185d 0%, #ec4899 50%, #be185d 100%);
                opacity: 0.6;
            }

            /* Print-brand ink mark in sidebar logo area */
            .ink-mark {
                width: 8px; height: 8px; border-radius: 50%;
                background: linear-gradient(135deg, #be185d, #ec4899);
                display: inline-block;
            }

            /* Scrollable mobile safe area */
            .mobile-topbar { padding-top: env(safe-area-inset-top, 0); }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-900 antialiased">

        @php
            $admin = auth()->user();
            $canSeeStaffMenu = in_array($admin?->role, ['hr', 'super_admin'], true);
            $navLink = function (string|array $patterns) use ($admin): string {
                $active = request()->routeIs(...(array)$patterns);
                return 'pb-nav-item relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-150' . ($active ? ' active' : '');
            };
        @endphp

        {{-- ════════════════════════════════════════════════════════
             MOBILE TOPBAR
        ════════════════════════════════════════════════════════ --}}
        <header class="mobile-topbar sticky top-0 z-50 flex h-[60px] items-center justify-between gap-3
                        border-b border-slate-200/70 bg-white/95 px-4 backdrop-blur-xl lg:hidden">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('logo.png') }}" alt="Printbuka" class="h-8 w-auto">
            </a>
            <div class="flex items-center gap-1.5">
                <livewire:notification-bell />
                <button id="pb-menu-btn" type="button" aria-label="Open menu" aria-expanded="false"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200
                           bg-white text-slate-600 transition hover:border-brand-300 hover:text-brand-700">
                    <svg id="pb-menu-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="pb-menu-close" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </header>

        {{-- Backdrop --}}
        <div id="pb-overlay"
            class="fixed inset-0 z-40 bg-slate-950/50 opacity-0 pointer-events-none lg:hidden"
            aria-hidden="true">
        </div>

        {{-- ════════════════════════════════════════════════════════
             LAYOUT SHELL
        ════════════════════════════════════════════════════════ --}}
        <div class="lg:flex lg:min-h-screen">

            {{-- ─── SIDEBAR ─────────────────────────────────────── --}}
            <aside id="pb-sidebar"
                class="fixed inset-y-0 left-0 z-50 flex w-[264px] flex-col border-r border-slate-200/70
                       bg-white transition-transform duration-300 ease-out
                       lg:static lg:z-auto lg:translate-x-0 lg:min-h-screen">

                {{-- Logo --}}
                <div class="flex h-[60px] shrink-0 items-center justify-between border-b border-slate-100 px-5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                        <img src="{{ asset('logo.png') }}" alt="Printbuka" class="h-9 w-auto">
                        <span class="ink-mark"></span>
                    </a>
                    <button id="pb-sidebar-close" type="button" aria-label="Close"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400
                               transition hover:bg-slate-100 hover:text-slate-700 lg:hidden">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Nav body --}}
                <div id="pb-sidebar-body" class="flex flex-1 flex-col overflow-y-auto px-3 py-4 gap-0.5">

                    {{-- Core navigation --}}
                    <p class="pb-nav-group">Core</p>

                    <a href="{{ route('admin.dashboard') }}" class="{{ $navLink('admin.dashboard') }}">
                        <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" class="{{ $navLink('admin.orders.*') }}">
                        <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Production Jobs</span>
                        @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                            <span class="ml-auto pb-badge pb-badge-warning text-[10px] px-1.5 py-0">{{ $pendingOrdersCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.tasks.index') }}" class="{{ $navLink('admin.tasks.*') }}">
                        <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Today's Tasks</span>
                    </a>

                    <a href="{{ route('admin.support.index') }}" class="{{ $navLink('admin.support.*') }}">
                        <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Support Tickets</span>
                    </a>

                    {{-- CRM --}}
                    @if($admin?->canAdmin('customers.manage') || $admin?->canAdmin('invoices.manage'))
                        <p class="pb-nav-group mt-3">CRM</p>
                    @endif

                    @if($admin?->canAdmin('customers.manage'))
                        <a href="{{ route('admin.customers.index') }}" class="{{ $navLink('admin.customers.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M17 20h5V8a2 2 0 00-2-2h-3m-7 14H5a2 2 0 01-2-2V8a2 2 0 012-2h3m4 14v-4a2 2 0 00-2-2H8a2 2 0 00-2 2v4m6 0h2m-6 0H6m6-14V4a2 2 0 00-2-2H8a2 2 0 00-2 2v2m6 0H6"/>
                            </svg>
                            <span>Customers</span>
                        </a>
                    @endif

                    @if($admin?->canAdmin('invoices.manage'))
                        <a href="{{ route('admin.invoices.index') }}" class="{{ $navLink('admin.invoices.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Invoices</span>
                        </a>
                    @endif

                    @if($admin?->canAdmin('shop-products.manage'))
                        <a href="{{ route('admin.shop-products.index') }}" class="{{ $navLink('admin.shop-products.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>Shop Products</span>
                        </a>
                    @endif

                    @if($admin?->canAdmin('shop-orders.view'))
                        <a href="{{ route('admin.shop-orders.index') }}" class="{{ $navLink('admin.shop-orders.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Shop Orders</span>
                        </a>
                    @endif

                    @if($admin?->canAdmin('finance.view'))
                        <a href="{{ route('admin.finance.index') }}" class="{{ $navLink('admin.finance.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Finance</span>
                        </a>
                    @endif

                    @if($admin?->canAdmin('newsletters.manage'))
                        <a href="{{ route('admin.newsletters.index') }}" class="{{ $navLink('admin.newsletters.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span>Newsletters</span>
                        </a>
                    @endif

                    {{-- ERM --}}
                    @if($canSeeStaffMenu || $admin?->canAdmin('training.manage'))
                        <p class="pb-nav-group mt-3">People (ERM)</p>
                    @endif

                    @if($canSeeStaffMenu)
                        <a href="{{ route('admin.staff.index') }}" class="{{ $navLink('admin.staff.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Staff</span>
                            @if(isset($pendingStaffCount) && $pendingStaffCount > 0)
                                <span class="ml-auto pb-badge pb-badge-danger text-[10px] px-1.5 py-0">{{ $pendingStaffCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if($admin?->canAdmin('training.manage'))
                        <a href="{{ route('admin.training.index') }}" class="{{ $navLink('admin.training.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M12 14l6.16-3.422A12.083 12.083 0 0119 15.5c0 1.41-.241 2.764-.684 4.023C16.561 20.44 14.414 21 12 21s-4.561-.56-6.316-1.477A12.02 12.02 0 015 15.5c0-1.711.356-3.34.999-4.817L12 14z"/>
                            </svg>
                            <span>Training</span>
                        </a>
                    @endif

                    {{-- Admin / ORM --}}
                    @if($admin?->canAdmin('*'))
                        <p class="pb-nav-group mt-3">Operations</p>

                        <a href="{{ route('admin.products.index') }}" class="{{ $navLink(['admin.products.*', 'admin.product-categories.*']) }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Products</span>
                        </a>

                        <a href="{{ route('admin.notifications.index') }}" class="{{ $navLink('admin.notifications.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span>Notifications</span>
                        </a>

                        @if($admin?->role === 'super_admin')
                            <a href="{{ route('admin.advertisements.index') }}" class="{{ $navLink('admin.advertisements.*') }}">
                                <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592L5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                                <span>Advertisements</span>
                            </a>
                        @endif

                        <p class="pb-nav-group mt-3">Settings</p>

                        <a href="{{ route('admin.settings.edit') }}" class="{{ $navLink('admin.settings.*') }}">
                            <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Settings</span>
                        </a>

                        @if($admin?->role === 'super_admin')
                            <a href="{{ route('admin.activity-logs.index') }}" class="{{ $navLink('admin.activity-logs.*') }}">
                                <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M9 17v-2m3 2V7m3 10v-4m3 8H6a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Audit Logs</span>
                            </a>
                            <a href="{{ route('admin.policies.edit') }}" class="{{ $navLink('admin.policies.*') }}">
                                <svg class="pb-nav-icon h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Policies</span>
                            </a>
                        @endif
                    @endif

                    {{-- Bottom spacer --}}
                    <div class="mt-auto pt-6"></div>

                    {{-- User footer --}}
                    <div class="border-t border-slate-100 pt-3">
                        <a href="{{ route('admin.profile.edit') }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-slate-50">
                            @if($admin?->profilePhotoUrl())
                                <img src="{{ $admin->profilePhotoUrl() }}" alt=""
                                     class="h-8 w-8 rounded-full border border-slate-200 object-cover shrink-0">
                            @else
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-800">
                                    {{ $admin?->profileInitials() }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $admin?->displayName() }}</p>
                                <p class="truncate text-xs text-slate-400">
                                    {{ config('printbuka_admin.role_labels.'.$admin?->role, $admin?->role) }}
                                </p>
                            </div>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="mt-1">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                                       text-slate-500 transition hover:bg-red-50 hover:text-red-700">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- ─── MAIN CONTENT AREA ──────────────────────────── --}}
            <div class="flex min-w-0 flex-1 flex-col">

                {{-- Desktop topbar --}}
                <header class="topbar-line relative hidden lg:flex h-[60px] shrink-0 items-center justify-between
                               gap-4 border-b border-slate-200/70 bg-white/95 px-6 backdrop-blur-xl">

                    {{-- Page context --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <div class="pb-status-dot pb-status-online">
                                <span></span><span></span>
                            </div>
                            <span class="text-xs font-semibold text-brand-700">
                                {{ config('printbuka_admin.role_labels.'.$admin?->role, $admin?->role) }}
                            </span>
                        </div>
                        <span class="h-4 w-px bg-slate-200"></span>
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $admin?->displayName() }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex shrink-0 items-center gap-2">
                        @if($admin?->canAdmin('orders.create'))
                            <a href="{{ route('admin.orders.create') }}"
                               class="pb-btn pb-btn-sm bg-brand-700 text-white hover:bg-brand-800
                                      focus-visible:ring-brand-600 shadow-sm h-8 px-3 text-xs font-semibold">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                New Job
                            </a>
                        @endif
                        <livewire:notification-bell />
                        @if($admin?->profilePhotoUrl())
                            <a href="{{ route('admin.profile.edit') }}"
                               class="h-8 w-8 rounded-full overflow-hidden border-2 border-slate-200 hover:border-brand-300 transition">
                                <img src="{{ $admin->profilePhotoUrl() }}" alt="" class="h-full w-full object-cover">
                            </a>
                        @else
                            <a href="{{ route('admin.profile.edit') }}"
                               class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 border-2 border-transparent
                                      text-xs font-bold text-brand-800 hover:border-brand-300 transition">
                                {{ $admin?->profileInitials() }}
                            </a>
                        @endif
                    </div>
                </header>

                {{-- Main --}}
                <main class="flex-1 px-4 py-5 sm:px-6 lg:px-8 lg:py-6">

                    {{-- Breadcrumbs --}}
                    <div class="-mx-4 -mt-5 mb-5 sm:-mx-6 sm:-mt-5 lg:-mx-8 lg:-mt-6 lg:mb-6">
                        @include('layouts.partials.breadcrumbs', [
                            'rootLabel'    => 'Dashboard',
                            'rootRoute'    => 'admin.dashboard',
                            'skipSegments' => ['admin'],
                        ])
                    </div>

                    {{-- Duty-hours modal --}}
                    @if(session('warning'))
                        <div id="pb-duty-modal"
                             class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/60 p-4">
                            <div class="w-full max-w-md pb-card p-6 shadow-2xl">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100">
                                        <svg class="h-5 w-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Access Restricted</p>
                                        <h2 class="mt-1 text-lg font-bold text-slate-900">Viewing disabled during duty hours</h2>
                                        <p class="mt-2 text-sm text-slate-600">{{ session('warning') }}</p>
                                    </div>
                                </div>
                                <div class="mt-5 flex justify-end">
                                    <button type="button" data-close-duty-modal
                                        class="pb-btn pb-btn-md pb-btn-ink text-sm">
                                        Continue to Dashboard
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Flash messages --}}
                    @if(session('status'))
                        <div class="pb-alert pb-alert-success mb-5" role="alert">
                            <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        {{-- Icons sprite --}}
        <x-form-icons />

        <script>
        (() => {
            const btn     = document.getElementById('pb-menu-btn');
            const close   = document.getElementById('pb-sidebar-close');
            const sidebar = document.getElementById('pb-sidebar');
            const overlay = document.getElementById('pb-overlay');
            const openIc  = document.getElementById('pb-menu-open');
            const closeIc = document.getElementById('pb-menu-close');
            const lg      = window.matchMedia('(min-width: 1024px)');

            if (!sidebar) return;

            const openMenu = () => {
                sidebar.classList.add('open');
                overlay.classList.replace('opacity-0','opacity-100');
                overlay.classList.replace('pointer-events-none','pointer-events-auto');
                document.body.style.overflow = 'hidden';
                btn?.setAttribute('aria-expanded','true');
                openIc?.classList.add('hidden');
                closeIc?.classList.remove('hidden');
            };
            const closeMenu = () => {
                sidebar.classList.remove('open');
                overlay.classList.replace('opacity-100','opacity-0');
                overlay.classList.replace('pointer-events-auto','pointer-events-none');
                document.body.style.overflow = '';
                btn?.setAttribute('aria-expanded','false');
                openIc?.classList.remove('hidden');
                closeIc?.classList.add('hidden');
            };

            btn?.addEventListener('click', () =>
                btn.getAttribute('aria-expanded') === 'true' ? closeMenu() : openMenu());
            close?.addEventListener('click', closeMenu);
            overlay?.addEventListener('click', closeMenu);

            sidebar.querySelectorAll('a').forEach(a =>
                a.addEventListener('click', () => { if (!lg.matches) closeMenu(); }));

            lg.addEventListener?.('change', () => { if (lg.matches) closeMenu(); });
        })();

        (() => {
            const modal = document.getElementById('pb-duty-modal');
            if (!modal) return;
            modal.querySelector('[data-close-duty-modal]')
                ?.addEventListener('click', () => modal.remove());
            modal.addEventListener('click', e => { if (e.target === modal) modal.remove(); });
        })();
        </script>

        @stack('scripts')
        @livewireScripts
    </body>
</html>
