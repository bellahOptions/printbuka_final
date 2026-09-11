@extends('layouts.admin')

@section('title', 'Product Categories | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
            <div class="pb-page-header">
                <div>
                    <h1 class="pb-page-title">Categories</h1>
                    <p class="pb-page-subtitle">Product Category Management</p>
                </div>
                <a href="{{ route('admin.product-categories.create') }}" class="pb-btn pb-btn-md pb-btn-primary self-start">Create Category</a>
            </div>
            @if (session('status'))
                <div class="pb-alert pb-alert-success mb-6">{{ session('status') }}</div>
            @endif
            <div>
                <livewire:admin.product-categories-table />
            </div>
    </div>
@endsection
