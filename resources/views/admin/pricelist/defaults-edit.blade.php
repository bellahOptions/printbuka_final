@extends('layouts.admin')

@section('title', 'Default Option Pricing | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.pricelist.index') }}" class="hover:text-pink-600">Pricelist</a>
            <span>/</span>
            <span class="font-semibold text-slate-700">Default Option Prices</span>
        </div>
        <h1 class="mt-2 text-3xl font-black text-slate-950">Default option prices</h1>
        <p class="mt-2 text-sm text-slate-500">Used as the fallback price list for any product that doesn't set its own option pricing.</p>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.pricelist.defaults.update') }}" method="POST" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            @include('admin.pricelist._option-groups')

            <div class="flex justify-end">
                <button type="submit" class="rounded-md bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save defaults</button>
            </div>
        </form>
    </div>
@endsection
