@extends('layouts.admin')
@section('title', $vendor->displayName())
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title text-xl font-black mb-1">{{ $vendor->displayName() }}</h1>
            <p class="text-sm text-slate-400 flex items-center gap-2 flex-wrap">
                {{ $vendor->code }} · {{ $vendor->vendor_type }}
                @if($vendor->status === 'active')
                    <span class="pb-badge pb-badge-success">Active</span>
                @elseif($vendor->status === 'blacklisted')
                    <span class="pb-badge pb-badge-danger">Blacklisted</span>
                @else
                    <span class="pb-badge pb-badge-secondary">Inactive</span>
                @endif
                @if($vendor->rating)
                    <span class="text-amber-500 font-bold">{{ str_repeat('★', $vendor->rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - $vendor->rating) }}</span></span>
                @endif
            </p>
        </div>
        @if(auth()->user()?->canAdmin('vendors.manage'))
            <a href="{{ route('admin.vendors.edit', $vendor) }}" class="pb-btn pb-btn-md pb-btn-secondary self-start">
                <x-heroicon-o-pencil class="w-4 h-4" /> Edit Vendor
            </a>
        @endif
    </div>
</div>

@if(session('status'))
    <div class="pb-alert pb-alert-success mb-5">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        <div class="pb-card p-5">
            <div class="flex items-start gap-4">
                @if($vendor->logoUrl())
                    <img src="{{ $vendor->logoUrl() }}" alt="{{ $vendor->displayName() }}"
                         class="w-20 h-20 rounded-xl object-cover border border-slate-200 bg-slate-50 shrink-0" />
                @else
                    <div class="w-20 h-20 rounded-xl bg-pink-50 border border-slate-200 flex items-center justify-center shrink-0">
                        <span class="text-xl font-black text-pink-600">{{ $vendor->initials() }}</span>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-4 flex-1">
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Contact Person</p>
                        <p class="font-bold text-slate-900">{{ $vendor->contact_person ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Category</p>
                        <p class="font-bold text-slate-900">{{ $vendor->category ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Phone</p>
                        <p class="font-bold text-slate-900">
                            @if($vendor->phone)
                                <a href="tel:{{ $vendor->phone }}" class="hover:text-pink-600">{{ $vendor->phone }}</a>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Email</p>
                        <p class="font-bold text-slate-900 truncate">
                            @if($vendor->email)
                                <a href="mailto:{{ $vendor->email }}" class="hover:text-pink-600">{{ $vendor->email }}</a>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-slate-100 text-sm">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Alternate Phone</p>
                    <p class="font-bold text-slate-700">{{ $vendor->alternate_phone ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Website</p>
                    <p class="font-bold text-slate-700 truncate">
                        @if($vendor->website)
                            <a href="{{ $vendor->website }}" target="_blank" rel="noopener" class="hover:text-pink-600">{{ $vendor->website }}</a>
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Added By</p>
                    <p class="font-bold text-slate-700">{{ $vendor->createdBy?->displayName() ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Added</p>
                    <p class="font-bold text-slate-700" title="{{ $vendor->created_at }}">{{ $vendor->created_at->diffForHumans() }}</p>
                </div>
            </div>

            @if($vendor->address || $vendor->city || $vendor->state || $vendor->country)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs font-bold uppercase text-slate-400">Address</p>
                    <p class="text-sm text-slate-700">
                        {{ collect([$vendor->address, $vendor->city, $vendor->state, $vendor->country])->filter()->implode(', ') }}
                    </p>
                </div>
            @endif

            @if($vendor->tagList() !== [])
                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-2">
                    @foreach($vendor->tagList() as $tag)
                        <span class="pb-badge pb-badge-secondary">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif

            @if($vendor->notes)
                <p class="text-sm text-slate-600 mt-4 pt-4 border-t border-slate-100">{{ $vendor->notes }}</p>
            @endif
        </div>

        @if(auth()->user()?->canAdmin('vendors.manage') && ($vendor->tax_id || $vendor->bank_name || $vendor->bank_account_number))
            <div class="pb-card p-5">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider flex items-center gap-2 mb-4">
                    <x-heroicon-o-banknotes class="w-4 h-4 text-pink-500" /> Payment & Compliance
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Tax ID / TIN</p>
                        <p class="font-bold text-slate-700">{{ $vendor->tax_id ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Bank</p>
                        <p class="font-bold text-slate-700">{{ $vendor->bank_name ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Account Name</p>
                        <p class="font-bold text-slate-700">{{ $vendor->bank_account_name ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Account Number</p>
                        <p class="font-bold text-slate-700">{{ $vendor->bank_account_number ?: '—' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="space-y-6">
        @if(auth()->user()?->canAdmin('vendors.manage'))
            <div class="pb-card p-5">
                <form method="POST" action="{{ route('admin.vendors.destroy', $vendor) }}"
                      onsubmit="return confirm('Delete this vendor? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-destructive w-full">
                        <x-heroicon-o-trash class="w-4 h-4" /> Delete Vendor
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@endsection
