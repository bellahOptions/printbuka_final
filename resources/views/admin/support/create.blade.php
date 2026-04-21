@extends('layouts.admin')

@section('title', 'Create IT Support Ticket | Printbuka')

@section('content')
<main class="mx-auto max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.support.index') }}" class="inline-flex items-center gap-2 text-sm font-black text-slate-600 hover:text-pink-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Tickets
        </a>
        <h1 class="mt-3 text-2xl font-black text-slate-950">Create IT Support Ticket</h1>
        <p class="text-sm font-semibold text-slate-500">This ticket will be routed to active Super Admin / IT staff.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <form action="{{ route('admin.support.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-black text-slate-700">Subject *</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="input input-bordered border-slate-200 w-full @error('subject') input-error @enderror" required />
                @error('subject') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-black text-slate-700">Category *</span></label>
                    <select name="category" class="select select-bordered border-slate-200 w-full @error('category') select-error @enderror" required>
                        <option value="">Select category</option>
                        <option value="technical" @selected(old('category') === 'technical')>Technical Issue</option>
                        <option value="billing" @selected(old('category') === 'billing')>Billing</option>
                        <option value="order" @selected(old('category') === 'order')>Order Flow</option>
                        <option value="design" @selected(old('category') === 'design')>Design / Asset</option>
                        <option value="general" @selected(old('category') === 'general')>General</option>
                        <option value="other" @selected(old('category') === 'other')>Other</option>
                    </select>
                    @error('category') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-black text-slate-700">Priority *</span></label>
                    <select name="priority" class="select select-bordered border-slate-200 w-full @error('priority') select-error @enderror" required>
                        <option value="">Select priority</option>
                        <option value="low" @selected(old('priority') === 'low')>Low</option>
                        <option value="normal" @selected(old('priority') === 'normal')>Normal</option>
                        <option value="high" @selected(old('priority') === 'high')>High</option>
                        <option value="urgent" @selected(old('priority') === 'urgent')>Urgent</option>
                    </select>
                    @error('priority') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-black text-slate-700">Details *</span></label>
                <textarea name="message" rows="8" class="textarea textarea-bordered border-slate-200 w-full @error('message') textarea-error @enderror" required>{{ old('message') }}</textarea>
                @error('message') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.support.index') }}" class="btn btn-outline border-slate-300 font-black">Cancel</a>
                <button type="submit" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Submit Ticket</button>
            </div>
        </form>
    </div>
</main>
@endsection
