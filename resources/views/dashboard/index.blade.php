@extends('layouts.theme')

@section('title', 'Welcome ' . auth()->user()->first_name . ' ' . auth()->user()->last_name)

@section('content')
    <main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-12 text-slate-900">
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Welcome Header (DaisyUI Card) --}}
            <div class="card bg-gradient-to-r from-slate-900 to-slate-800 shadow-xl text-white">
                <div class="card-body p-6 sm:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="badge badge-ghost bg-cyan-500/20 text-cyan-300 border-0 mb-3">Customer Dashboard</div>
                            <h1 class="text-3xl font-bold lg:text-4xl">Welcome back, {{ auth()->user()->first_name }}! 👋</h1>
                            <p class="mt-2 max-w-2xl text-sm text-slate-300">Manage your Printbuka account, track orders, and access exclusive print deals from one dashboard.</p>
                        </div>
                            {{--
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn bg-white/10 hover:bg-white/20 border-0 text-white font-semibold">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </form>--}}
                    </div>
                </div>
            </div>

            {{-- Stats Grid (DaisyUI Stats) --}}
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total Spent Card --}}
                <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5 transition hover:shadow-lg">
                    <div class="stat-figure text-pink-500">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="stat-title text-slate-500">Total Spent</div>
                    <div class="stat-value text-2xl text-slate-900">₦{{ number_format($totalSpent, 2) }}</div>
                    <div class="stat-desc text-slate-400">Lifetime orders value</div>
                </div>

                {{-- Orders Card --}}
                <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5 transition hover:shadow-lg">
                    <div class="stat-figure text-cyan-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div class="stat-title text-slate-500">Active Orders</div>
                    <div class="stat-value text-2xl text-slate-900">{{ $orders }}</div>
                    <div class="stat-desc text-slate-400">{{ $completedOrders }} completed total</div>
                </div>

                {{-- Pending Invoices Card --}}
                <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5 transition hover:shadow-lg">
                    <div class="stat-figure text-amber-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="stat-title text-slate-500">Pending Invoices</div>
                    <div class="stat-value text-2xl text-slate-900">{{ $pendingInvoices }}</div>
                    <div class="stat-desc text-amber-600">Awaiting payment</div>
                </div>

                {{-- Active Quotes Card --}}
                <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5 transition hover:shadow-lg">
                    <div class="stat-figure text-emerald-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="stat-title text-slate-500">Active Quotes</div>
                    <div class="stat-value text-2xl text-slate-900">{{ $activeQuotes }}</div>
                    <div class="stat-desc text-slate-400">Pending approval</div>
                </div>
            </div>

            {{-- Two Column Layout --}}
            <div class="mt-8 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                {{-- Recent Orders Section --}}
                <section class="card bg-white rounded-2xl shadow-md border border-slate-100">
                    <div class="card-body p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="badge bg-pink-100 text-pink-700 border-0 mb-2">Order History</div>
                                <h2 class="text-2xl font-bold text-slate-900">Recent Orders</h2>
                                <p class="text-sm text-slate-500 mt-1">Track your latest print jobs</p>
                            </div>
                            <a href="{{ route('products.index') }}" class="btn btn-primary bg-pink-600 hover:bg-pink-700 border-0 text-white shadow-md">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                New Order
                            </a>
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse ($recentOrders as $order)
                                <div class="flex sm:grid sm:grid-cols-1 sm:flex-col items-center justify-between p-4 rounded-xl bg-slate-50 hover:bg-slate-100 transition">
                                    <div class="flex items-center gap-4 sm:w-full">
                                        <div class="h-12 w-12 rounded-lg bg-pink-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-slate-900">
                                                <a href="{{ route('orders.show', $order) }}" class="hover:text-pink-600 transition">
                                                    Order {{ $order->displayNumber() }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-slate-500">
                                                {{ $order->created_at ? $order->created_at->format('M d, Y') : 'Date not set' }} 
                                                • {{ $order->quantity ?? 1 }} item(s)
                                            </p>
                                            @if($order->product)
                                                <p class="text-xs text-slate-400 mt-1">{{ $order->product->name ?? 'Product' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-slate-900">₦{{ number_format($order->total_price ?? 0, 2) }}</p>
                                        <span class="badge badge-sm 
                                            @if($order->status === 'completed') badge-success 
                                            @elseif($order->status === 'processing') badge-warning 
                                            @elseif($order->status === 'cancelled') badge-error 
                                            @else badge-info @endif">
                                            {{ ucfirst($order->status ?? 'pending') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <div class="h-20 w-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <p class="text-slate-500">No orders yet</p>
                                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline border-pink-600 text-pink-600 hover:bg-pink-600 hover:text-white mt-3">Start Shopping</a>
                                </div>
                            @endforelse
                        </div>

                        @if($orders > 5)
                            <div class="mt-4 text-center">
                                <a href="{{ route('orders.index') }}" class="link link-hover text-pink-600 font-semibold">View All Orders →</a>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Quick Actions Sidebar --}}
                <aside class="card bg-white rounded-2xl shadow-md border border-slate-100">
                    <div class="card-body p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="h-8 w-8 rounded-lg bg-cyan-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold uppercase tracking-wide text-cyan-700">Quick Actions</p>
                        </div>
                        
                        <div class="space-y-3">
                            <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-pink-300 hover:bg-pink-50 transition group">
                                <span class="font-semibold text-slate-700 group-hover:text-pink-700">Edit Profile</span>
                                <svg class="h-4 w-4 text-slate-400 group-hover:text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            
                            <a href="{{ route('products.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-pink-600 text-white hover:bg-pink-700 transition shadow-md">
                                <span class="font-semibold">Browse Full Catalog</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            
                            <a href="{{ route('products.index') }}#categories" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-cyan-300 hover:bg-cyan-50 transition group">
                                <span class="font-semibold text-slate-700 group-hover:text-cyan-700">Shop by Category</span>
                                <svg class="h-4 w-4 text-slate-400 group-hover:text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </a>
                            
                            @if(Route::has('quotes.create'))
                            <a href="{{ route('quotes.create') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition group">
                                <span class="font-semibold text-slate-700 group-hover:text-emerald-700">Request Bulk Quote</span>
                                <svg class="h-4 w-4 text-slate-400 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                </svg>
                            </a>
                            @endif
                            
                            <a href="{{ route('support.tickets.create') ?? '#' }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-purple-300 hover:bg-purple-50 transition group">
                                <span class="font-semibold text-slate-700 group-hover:text-purple-700">Contact Support</span>
                                <svg class="h-4 w-4 text-slate-400 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </a>
                        </div>

                        {{-- Helpful Tip --}}
                        <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-100">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800">Need a custom design?</p>
                                    <p class="text-xs text-amber-700 mt-1">Our design team can help bring your ideas to life. Request a free consultation!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- Services Showcase --}}
            <div class="mt-8">
                <div class="card bg-gradient-to-r from-pink-50 to-cyan-50 rounded-2xl shadow-sm border border-pink-100">
                    <div class="card-body p-6">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-bold text-slate-900">Our Printing Services</h3>
                            <p class="text-sm text-slate-600">Professional printing at your fingertips</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="text-center">
                                <div class="h-10 w-10 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold mt-2">Direct Image</p>
                            </div>
                            <div class="text-center">
                                <div class="h-10 w-10 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="h-5 w-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold mt-2">UV-DTF</p>
                            </div>
                            <div class="text-center">
                                <div class="h-10 w-10 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold mt-2">Laser Engraving</p>
                            </div>
                            <div class="text-center">
                                <div class="h-10 w-10 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold mt-2">Business Stationery</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection