@extends('layouts.admin')

@section('title', 'Create IT Support Ticket | Printbuka')

@section('content')
<main class="mx-auto max-w-4xl">
    <div class="pb-page-header">
        <div>
            <a href="{{ route('admin.support.index') }}" class="inline-flex items-center gap-2 text-sm font-black text-slate-600 hover:text-pink-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Tickets
            </a>
            <h1 class="pb-page-title mt-3">Create IT Support Ticket</h1>
            <p class="pb-page-subtitle">This ticket will be routed to active Process & Technology Manager / IT staff.</p>
        </div>
    </div>

    <div class="pb-card p-6 sm:p-8">
        <form action="{{ route('admin.support.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="pb-field">
                <label class="pb-label">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="pb-input w-full {{ $errors->has('subject') ? 'pb-input-error' : '' }}" required />
                @error('subject') <p class="pb-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="pb-field">
                    <label class="pb-label">Category *</label>
                    <select name="category" class="pb-select w-full {{ $errors->has('category') ? 'pb-input-error' : '' }}" required>
                        <option value="">Select category</option>
                        <option value="technical" @selected(old('category') === 'technical')>Technical Issue</option>
                        <option value="billing" @selected(old('category') === 'billing')>Billing</option>
                        <option value="order" @selected(old('category') === 'order')>Order Flow</option>
                        <option value="design" @selected(old('category') === 'design')>Design / Asset</option>
                        <option value="general" @selected(old('category') === 'general')>General</option>
                        <option value="other" @selected(old('category') === 'other')>Other</option>
                    </select>
                    @error('category') <p class="pb-field-error">{{ $message }}</p> @enderror
                </div>

                <div class="pb-field">
                    <label class="pb-label">Priority *</label>
                    <select name="priority" class="pb-select w-full {{ $errors->has('priority') ? 'pb-input-error' : '' }}" required>
                        <option value="">Select priority</option>
                        <option value="low" @selected(old('priority') === 'low')>Low</option>
                        <option value="normal" @selected(old('priority') === 'normal')>Normal</option>
                        <option value="high" @selected(old('priority') === 'high')>High</option>
                        <option value="urgent" @selected(old('priority') === 'urgent')>Urgent</option>
                    </select>
                    @error('priority') <p class="pb-field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pb-field">
                <label class="pb-label">Details *</label>
                <textarea name="message" rows="8" data-rich-editor class="pb-textarea w-full {{ $errors->has('message') ? 'pb-input-error' : '' }}" required>{{ old('message') }}</textarea>
                @error('message') <p class="pb-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.support.index') }}" class="pb-btn pb-btn-outline">Cancel</a>
                <button type="submit" class="pb-btn pb-btn-primary">Submit Ticket</button>
            </div>
        </form>
    </div>
</main>
@endsection
