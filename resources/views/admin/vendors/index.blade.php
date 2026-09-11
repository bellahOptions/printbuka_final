@extends('layouts.admin')
@section('title', 'Vendor Directory')
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title text-xl font-black mb-2">Vendor Directory</h1>
            <p class="text-sm text-slate-400">Suppliers, engineers, contractors and every other business we purchase from or work with.</p>
        </div>
        @if(auth()->user()?->canAdmin('vendors.manage'))
            <a href="{{ route('admin.vendors.create') }}"
               class="pb-btn pb-btn-md pb-btn-primary self-start">
                <x-heroicon-o-plus class="w-4 h-4" /> Add Vendor
            </a>
        @endif
    </div>
</div>

@if(session('status'))
    <div class="pb-alert pb-alert-success mb-5">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <a href="{{ route('admin.vendors.index') }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ !request()->hasAny(['status', 'vendor_type']) ? 'ring-2 ring-pink-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 group-hover:bg-pink-50 transition">
            <x-heroicon-o-building-storefront class="w-5 h-5 text-slate-500 group-hover:text-pink-600 transition" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-slate-400">Total Vendors</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.vendors.index', ['status' => 'active']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('status') === 'active' ? 'ring-2 ring-emerald-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
            <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-emerald-600">Active</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['active']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.vendors.index', ['status' => 'inactive']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('status') === 'inactive' ? 'ring-2 ring-slate-400' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
            <x-heroicon-o-pause-circle class="w-5 h-5 text-slate-500" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-slate-400">Inactive</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['inactive']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.vendors.index', ['status' => 'blacklisted']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('status') === 'blacklisted' ? 'ring-2 ring-red-500' : '' }}">
        <div class="w-10 h-10 rounded-xl {{ $stats['blacklisted'] > 0 ? 'bg-red-50' : 'bg-slate-100' }} flex items-center justify-center shrink-0">
            <x-heroicon-o-no-symbol class="w-5 h-5 {{ $stats['blacklisted'] > 0 ? 'text-red-500' : 'text-slate-400' }}" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase {{ $stats['blacklisted'] > 0 ? 'text-red-600' : 'text-slate-400' }}">Blacklisted</p>
            <p class="text-2xl font-black {{ $stats['blacklisted'] > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ number_format($stats['blacklisted']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.vendors.index', ['vendor_type' => 'Supplier']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('vendor_type') === 'Supplier' ? 'ring-2 ring-blue-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
            <x-heroicon-o-truck class="w-5 h-5 text-blue-600" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-blue-600">Suppliers</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['suppliers']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.vendors.index', ['vendor_type' => 'Engineer']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('vendor_type') === 'Engineer' ? 'ring-2 ring-violet-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
            <x-heroicon-o-wrench-screwdriver class="w-5 h-5 text-violet-600" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-violet-600">Engineers</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['engineers']) }}</p>
        </div>
    </a>
</div>

{{-- Smart Search + Filters --}}
<form method="GET" action="{{ route('admin.vendors.index') }}" class="pb-card p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[220px]">
        <label class="pb-label text-xs">Smart Search</label>
        <input type="text" name="search" value="{{ $filters['search'] }}"
               placeholder="Name, company, contact, email, phone, category or tag..."
               class="pb-input w-full" />
    </div>
    <div class="min-w-[170px]">
        <label class="pb-label text-xs">Vendor Type</label>
        <select name="vendor_type" class="pb-select w-full">
            <option value="">All Types</option>
            @foreach($vendorTypes as $type)
                <option value="{{ $type }}" @selected($filters['vendorType'] === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    @if($categories->isNotEmpty())
        <div class="min-w-[170px]">
            <label class="pb-label text-xs">Category</label>
            <select name="category" class="pb-select w-full">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" @selected($filters['category'] === $cat)>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="min-w-[150px]">
        <label class="pb-label text-xs">Sort</label>
        <select name="sort" class="pb-select w-full">
            <option value="name" @selected($filters['sort'] === 'name')>Name (A–Z)</option>
            <option value="recent" @selected($filters['sort'] === 'recent')>Recently Added</option>
            <option value="rating" @selected($filters['sort'] === 'rating')>Highest Rated</option>
        </select>
    </div>
    <button type="submit" class="pb-btn pb-btn-md pb-btn-secondary">
        <x-heroicon-o-magnifying-glass class="w-4 h-4" /> Search
    </button>
    @if($filters['search'] !== '' || $filters['vendorType'] !== '' || $filters['category'] !== '' || $filters['status'] !== '' || $filters['sort'] !== 'name')
        <a href="{{ route('admin.vendors.index') }}" class="pb-btn pb-btn-md pb-btn-ghost">Clear</a>
    @endif
    @if($filters['status'] !== '')
        <input type="hidden" name="status" value="{{ $filters['status'] }}" />
    @endif
</form>

<div class="pb-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="pb-table w-full">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Contact</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($vendor->logoUrl())
                                    <img src="{{ $vendor->logoUrl() }}" alt="{{ $vendor->displayName() }}"
                                         class="w-10 h-10 rounded-lg object-cover border border-slate-200 bg-slate-50 shrink-0" />
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-pink-50 border border-slate-200 flex items-center justify-center shrink-0">
                                        <span class="text-xs font-black text-pink-600">{{ $vendor->initials() }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-slate-900">{{ $vendor->displayName() }}</p>
                                    <p class="text-xs text-slate-400">{{ $vendor->code }}{{ $vendor->contact_person ? ' · '.$vendor->contact_person : '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="pb-badge pb-badge-info">{{ $vendor->vendor_type }}</span></td>
                        <td class="text-sm text-slate-600">{{ $vendor->category ?: '—' }}</td>
                        <td class="text-sm text-slate-600">
                            {{ $vendor->phone ?: ($vendor->email ?: '—') }}
                        </td>
                        <td>
                            @if($vendor->rating)
                                <span class="text-amber-500 font-bold text-sm">{{ str_repeat('★', $vendor->rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - $vendor->rating) }}</span></span>
                            @else
                                <span class="text-slate-300 text-sm">—</span>
                            @endif
                        </td>
                        <td>
                            @if($vendor->status === 'active')
                                <span class="pb-badge pb-badge-success">Active</span>
                            @elseif($vendor->status === 'blacklisted')
                                <span class="pb-badge pb-badge-danger">Blacklisted</span>
                            @else
                                <span class="pb-badge pb-badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.vendors.show', $vendor) }}" class="pb-btn pb-btn-sm pb-btn-ghost">View</a>
                            @if(auth()->user()?->canAdmin('vendors.manage'))
                                <a href="{{ route('admin.vendors.edit', $vendor) }}" class="pb-btn pb-btn-sm pb-btn-ghost">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="pb-empty">No vendors found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $vendors->links() }}
</div>

@endsection
