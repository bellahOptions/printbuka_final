@extends('layouts.admin')

@section('title', 'Pricelist | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Pricelist</p>
                <h1 class="mt-2 text-4xl text-slate-950">Product & service pricing.</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">Search a product or service to quote a client, or update pricing. Changes apply immediately across quotes, orders, and invoices.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('admin.large-format.calculator') }}" class="rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                    Large format calculator
                </a>
                @if (auth()->user()?->canAdmin('large_format.manage'))
                    <a href="{{ route('admin.large-format.index') }}" class="rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                        Large format rates
                    </a>
                @endif
                <a href="{{ route('admin.pricelist.custom.index') }}" class="rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                    Custom price list items
                </a>
                <a href="{{ route('admin.pricelist.defaults.edit') }}" class="rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                    Edit default option prices
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-8">
            <livewire:pricelist.search />
        </div>

        <div class="mt-10 rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Order surcharges</h2>
            <p class="mt-1 text-sm text-slate-500">Applied on top of unit pricing for express and sample orders.</p>

            <form action="{{ route('admin.pricelist.surcharges.update') }}" method="POST" class="mt-5 grid gap-5 sm:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="text-sm font-black text-slate-700">Express Order Surcharge (₦)</label>
                    <input type="number" step="0.01" min="0" name="express_order_surcharge"
                        value="{{ old('express_order_surcharge', $surcharges['express_order_surcharge']) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" />
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-black text-slate-700">Sample Order Surcharge (₦)</label>
                    <input type="number" step="0.01" min="0" name="sample_order_surcharge"
                        value="{{ old('sample_order_surcharge', $surcharges['sample_order_surcharge']) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" />
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save surcharges</button>
                </div>
            </form>
        </div>
    </div>
@endsection
