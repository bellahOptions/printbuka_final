@extends('layouts.admin')

@section('title', 'Default Option Pricing | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.pricelist.index') }}" class="hover:text-pink-600">Pricelist</a>
            <span>/</span>
            <span class="font-semibold text-slate-700">Default Option Prices</span>
        </div>
        <h1 class="pb-page-title mt-2">Default option prices</h1>
        <p class="pb-page-subtitle">Used as the fallback price list for any product that doesn't set its own option pricing.</p>

        @if (session('status'))
            <div class="mt-6 pb-alert pb-alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.pricelist.defaults.update') }}" method="POST" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            @include('admin.pricelist._option-groups')

            <div class="flex justify-end">
                <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">Save defaults</button>
            </div>
        </form>
    </div>
@endsection
