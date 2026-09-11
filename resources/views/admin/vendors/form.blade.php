@extends('layouts.admin')
@section('title', $vendor ? 'Edit Vendor' : 'Add Vendor')
@section('content')

<div class="pb-page-header">
    <h1 class="pb-page-title text-xl font-black mb-2">{{ $vendor ? 'Edit Vendor' : 'Add Vendor' }}</h1>
    <p class="text-sm text-slate-400">{{ $vendor ? 'Update details for '.$vendor->displayName().'.' : 'Register a new supplier, engineer, contractor or service provider.' }}</p>
</div>

<form method="POST"
      action="{{ $vendor ? route('admin.vendors.update', $vendor) : route('admin.vendors.store') }}"
      enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    @csrf
    @if($vendor)
        @method('PUT')
    @endif

    <div class="lg:col-span-2 space-y-6">
        <div class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
            <div class="p-5 space-y-5">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider">Vendor Details</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="pb-label">Name</label>
                        <input type="text" name="name" value="{{ old('name', $vendor?->name) }}" required
                               class="pb-input w-full @error('name') pb-input-error @enderror" />
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pb-label">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $vendor?->company_name) }}"
                               placeholder="Optional, if different from name"
                               class="pb-input w-full" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="pb-label">Vendor Type</label>
                        <select name="vendor_type" required class="pb-select w-full">
                            @foreach($vendorTypes as $type)
                                <option value="{{ $type }}" @selected(old('vendor_type', $vendor?->vendor_type ?? 'Supplier') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pb-label">Category</label>
                        <input type="text" name="category" value="{{ old('category', $vendor?->category) }}"
                               placeholder="e.g. Paper & Substrate, Electrical, IT Support"
                               list="vendor-categories"
                               class="pb-input w-full" />
                        <datalist id="vendor-categories">
                            @foreach($categories ?? [] as $cat)
                                <option value="{{ $cat }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="pb-label">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $vendor?->contact_person) }}"
                           class="pb-input w-full" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="pb-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $vendor?->email) }}"
                               class="pb-input w-full @error('email') pb-input-error @enderror" />
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pb-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $vendor?->phone) }}"
                               class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">Alternate Phone</label>
                        <input type="text" name="alternate_phone" value="{{ old('alternate_phone', $vendor?->alternate_phone) }}"
                               class="pb-input w-full" />
                    </div>
                </div>

                <div>
                    <label class="pb-label">Website</label>
                    <input type="url" name="website" value="{{ old('website', $vendor?->website) }}"
                           placeholder="https://"
                           class="pb-input w-full @error('website') pb-input-error @enderror" />
                    @error('website') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="pb-label">Address</label>
                    <input type="text" name="address" value="{{ old('address', $vendor?->address) }}"
                           class="pb-input w-full" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="pb-label">City</label>
                        <input type="text" name="city" value="{{ old('city', $vendor?->city) }}" class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">State</label>
                        <input type="text" name="state" value="{{ old('state', $vendor?->state) }}" class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">Country</label>
                        <input type="text" name="country" value="{{ old('country', $vendor?->country) }}" class="pb-input w-full" />
                    </div>
                </div>

                <div>
                    <label class="pb-label">Tags</label>
                    <input type="text" name="tags" value="{{ old('tags', $vendor?->tags) }}"
                           placeholder="Comma-separated, e.g. reliable, fast-turnaround, bulk-paper"
                           class="pb-input w-full" />
                    <p class="text-xs text-slate-400 mt-1">Tags are searchable from the vendor directory.</p>
                </div>

                <div>
                    <label class="pb-label">Notes</label>
                    <textarea name="notes" rows="3" class="pb-textarea w-full">{{ old('notes', $vendor?->notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Financial / Compliance --}}
        <div class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
            <div class="p-5 space-y-5">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider flex items-center gap-2">
                    <x-heroicon-o-banknotes class="w-4 h-4 text-pink-500" /> Payment & Compliance
                </h2>

                <div>
                    <label class="pb-label">Tax ID / TIN</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $vendor?->tax_id) }}" class="pb-input w-full" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="pb-label">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $vendor?->bank_name) }}" class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">Account Name</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $vendor?->bank_account_name) }}" class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">Account Number</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $vendor?->bank_account_number) }}" class="pb-input w-full" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Logo --}}
        <div class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
            <div class="p-5 space-y-4">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider flex items-center gap-2">
                    <x-heroicon-o-photo class="w-4 h-4 text-pink-500" /> Logo / Photo
                </h2>

                <livewire:uploads.secure-image-upload
                    :key="'vendor-logo-'.($vendor?->id ?: 'create')"
                    input-name="logo_path"
                    directory="vendors/logos"
                    :max-size-kb="4096"
                    :max-files="1"
                    :multiple="false"
                    :initial-path="old('logo_path')"
                />
                <p class="text-xs text-slate-400">JPG, PNG or WebP · max 4 MB</p>
                @error('logo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                @error('logo_path') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror

                @if($vendor?->logo)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs font-bold text-slate-500 mb-2">Current Logo</p>
                        <img src="{{ $vendor->logoUrl() }}" alt="{{ $vendor->displayName() }}"
                             class="h-24 w-24 rounded-lg border border-slate-200 object-cover bg-white" />
                        <label class="flex items-center gap-2 mt-3 text-xs text-slate-500">
                            <input type="checkbox" name="remove_logo" value="1" class="checkbox checkbox-xs" />
                            Remove current logo
                        </label>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="pb-label">Status</label>
                    <select name="status" required class="pb-select w-full">
                        <option value="active" @selected(old('status', $vendor?->status ?? 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $vendor?->status) === 'inactive')>Inactive</option>
                        <option value="blacklisted" @selected(old('status', $vendor?->status) === 'blacklisted')>Blacklisted</option>
                    </select>
                </div>

                <div>
                    <label class="pb-label">Rating</label>
                    <select name="rating" class="pb-select w-full">
                        <option value="">Not rated</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected((string) old('rating', $vendor?->rating) === (string) $i)>{{ str_repeat('★', $i).str_repeat('☆', 5 - $i) }}</option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="pb-btn pb-btn-md pb-btn-primary w-full">
                    <x-heroicon-o-check class="w-4 h-4" />
                    {{ $vendor ? 'Save Changes' : 'Add Vendor' }}
                </button>
                <a href="{{ route('admin.vendors.index') }}" class="pb-btn pb-btn-md pb-btn-ghost w-full">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

@endsection
