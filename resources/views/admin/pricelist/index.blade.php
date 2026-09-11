@extends('layouts.admin')

@section('title', 'Pricelist | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="pb-page-header">
            <div>
                <p class="pb-label">Pricelist</p>
                <h1 class="pb-page-title">Product & service pricing</h1>
                <p class="pb-page-subtitle max-w-2xl">Search a product or service to quote a client, or update pricing. Changes apply immediately across quotes, orders, and invoices.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('admin.large-format.calculator') }}" class="pb-btn pb-btn-md pb-btn-outline">
                    Large format calculator
                </a>
                @if (auth()->user()?->canAdmin('large_format.manage'))
                    <a href="{{ route('admin.large-format.index') }}" class="pb-btn pb-btn-md pb-btn-outline">
                        Large format rates
                    </a>
                @endif
                <a href="{{ route('admin.pricelist.custom.index') }}" class="pb-btn pb-btn-md pb-btn-outline">
                    Custom price list items
                </a>
                <a href="{{ route('admin.pricelist.defaults.edit') }}" class="pb-btn pb-btn-md pb-btn-outline">
                    Edit default option prices
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 pb-alert pb-alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div>
            <livewire:pricelist.search />
        </div>

        <div class="mt-10 pb-card p-6">
            <h2 class="pb-section-title">Order surcharges</h2>
            <p class="pb-section-subtitle">Applied on top of unit pricing for express and sample orders.</p>

            <form action="{{ route('admin.pricelist.surcharges.update') }}" method="POST" class="mt-5 grid gap-5 sm:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="pb-field">
                    <label class="pb-label">Express Order Surcharge (₦)</label>
                    <input type="number" step="0.01" min="0" name="express_order_surcharge"
                        value="{{ old('express_order_surcharge', $surcharges['express_order_surcharge']) }}"
                        class="pb-input" />
                </div>
                <div class="pb-field">
                    <label class="pb-label">Sample Order Surcharge (₦)</label>
                    <input type="number" step="0.01" min="0" name="sample_order_surcharge"
                        value="{{ old('sample_order_surcharge', $surcharges['sample_order_surcharge']) }}"
                        class="pb-input" />
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">Save surcharges</button>
                </div>
            </form>
        </div>
    </div>
@endsection
