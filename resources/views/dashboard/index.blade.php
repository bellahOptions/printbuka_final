@extends('layouts.theme')
@php
    $username = auth()->user()->first_name ." ". auth()->user()->last_name  ;

@endphp
@section('title', 'Welcome ' . $username)

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-5 rounded-md bg-slate-950 p-6 text-white sm:flex-row sm:items-center sm:justify-between lg:p-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Dashboard</p>
                    <h1 class="mt-2 text-4xl">Welcome, {{ auth()->user()->first_name }}.</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">Manage your Printbuka account, browse product options and keep an eye on orders from here.</p>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-md bg-white px-5 py-3 text-sm font-black text-slate-950 transition hover:bg-pink-100">Logout</button>
                </form>
            </div>

            <div class="mt-8">
                <livewire:dashboard.user-notifications />
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Products</p>
                    <p class="mt-3 text-5xl font-black text-slate-950">0</p>
                    <p class="mt-2 text-sm text-slate-600">catalog items available</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Orders</p>
                    <p class="mt-3 text-5xl font-black text-slate-950">{{ $orders }}</p>
                    <p class="mt-2 text-sm text-slate-600">orders recorded</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-emerald-700">Account</p>
                    <p class="mt-3 text-xl font-black text-slate-950">{{ auth()->user()->email }}</p>
                    <p class="mt-2 text-sm text-slate-600">signed in email</p>
                </div>
            </div>

            <div class="mt-8 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Order Management</p>
                            <h2 class="mt-2 text-3xl text-slate-950">Pending Orders</h2>
                        </div>
                        <a href="{{ route('products.index') }}" class="rounded-md border border-slate-200 px-4 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Make new Order</a>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($recentOrders as $product)
                            <article class="rounded-md border border-slate-100 p-4">
                                <h3 class="font-black text-slate-950">
                                    <a href="{{ route('products.show', $product) }}" class="transition hover:text-pink-700">{{ $product->name }}</a>
                                </h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $product->short_description }}</p>
                                <p class="mt-2 text-sm font-black text-pink-700">NGN {{ number_format($product->price, 2) }}</p>
                            </article>
                        @empty
                            <p class="rounded-md border border-dashed border-slate-300 p-5 text-sm text-slate-600">No products yet. Please hold while our staff upload new products</p>
                        @endforelse
                    </div>
                </section>

                <aside class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Quick Actions</p>
                    <div class="mt-5 space-y-3">
                        <a href="{{ route('profile.edit') }}" class="block rounded-md border border-slate-200 px-5 py-4 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Edit Profile</a>
                        @if (auth()->user()->hasAdminAccess())
                            <a href="{{ route('admin.dashboard') }}" class="block rounded-md bg-slate-950 px-5 py-4 text-sm font-black text-white transition hover:bg-pink-700">Open Admin Workflow</a>
                        @endif
                        <a href="{{ route('products.index') }}#catalog" class="block rounded-md bg-pink-600 px-5 py-4 text-sm font-black text-white transition hover:bg-pink-700">Browse Catalog</a>
                        <a href="{{ route('products.index') }}#categories" class="block rounded-md border border-slate-200 px-5 py-4 text-sm font-black text-slate-800 transition hover:border-cyan-300 hover:text-cyan-700">Explore Categories</a>
                        <a href="{{ route('quotes.create') }}" class="block rounded-md border border-slate-200 px-5 py-4 text-sm font-black text-slate-800 transition hover:border-emerald-300 hover:text-emerald-700">Request a Quote</a>
                    </div>
                </aside>
            </div>
        </section>
    </main>
@endsection
