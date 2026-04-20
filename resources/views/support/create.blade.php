@extends('layouts.theme')

@section('title', 'Create Support Ticket | Printbuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('support.index') }}" class="hover:text-pink-600 transition flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Tickets
                </a>
            </div>
            <h1 class="text-3xl font-bold text-slate-900">Create Support Ticket</h1>
            <p class="mt-1 text-sm text-slate-500">Our support team will respond to your inquiry within 24 hours</p>
        </div>

        {{-- Form Card --}}
        <div class="card bg-white rounded-2xl shadow-xl border border-slate-100">
            <div class="card-body p-6 sm:p-8">
                <form action="{{ route('support.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Subject --}}
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Subject *</span>
                        </label>
                        <input type="text" name="subject" value="{{ old('subject') }}" 
                            class="input input-bordered w-full focus:input-primary @error('subject') input-error @enderror"
                            placeholder="Brief description of your issue" required />
                        @error('subject') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Category & Priority Row --}}
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Category *</span>
                            </label>
                            <select name="category" class="select select-bordered w-full focus:select-primary @error('category') select-error @enderror" required>
                                <option value="">Select category</option>
                                <option value="general" @selected(old('category') === 'general')>General Inquiry</option>
                                <option value="technical" @selected(old('category') === 'technical')>Technical Issue</option>
                                <option value="billing" @selected(old('category') === 'billing')>Billing & Payment</option>
                                <option value="order" @selected(old('category') === 'order')>Order Status</option>
                                <option value="design" @selected(old('category') === 'design')>Design Assistance</option>
                                <option value="other" @selected(old('category') === 'other')>Other</option>
                            </select>
                            @error('category') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Priority *</span>
                            </label>
                            <select name="priority" class="select select-bordered w-full focus:select-primary @error('priority') select-error @enderror" required>
                                <option value="">Select priority</option>
                                <option value="low" @selected(old('priority') === 'low')>Low - Not urgent</option>
                                <option value="normal" @selected(old('priority') === 'normal')>Normal - Standard response</option>
                                <option value="high" @selected(old('priority') === 'high')>High - Affects my business</option>
                                <option value="urgent" @selected(old('priority') === 'urgent')>Urgent - Critical issue</option>
                            </select>
                            @error('priority') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Message --}}
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Message *</span>
                        </label>
                        <textarea name="message" rows="8" 
                            class="textarea textarea-bordered w-full focus:textarea-primary @error('message') textarea-error @enderror"
                            placeholder="Please describe your issue in detail. Include any relevant order numbers or screenshots if possible." required>{{ old('message') }}</textarea>
                        @error('message') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        <label class="label">
                            <span class="label-text-alt text-slate-400">Minimum 10 characters</span>
                        </label>
                    </div>

                    {{-- Info Box --}}
                    <div class="alert bg-blue-50 border-blue-200 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-blue-800 text-sm">
                            <p class="font-semibold">What happens next?</p>
                            <p class="text-xs mt-1">Our support team will review your ticket and respond within 24 hours. You'll receive email notifications for all replies.</p>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex flex-wrap gap-3 justify-end pt-4">
                        <a href="{{ route('support.index') }}" class="btn btn-outline btn-slate-600">
                            Cancel
                        </a>
                        <button type="submit" class="btn bg-pink-600 hover:bg-pink-700 border-0 text-white shadow-md shadow-pink-200">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Help Section --}}
        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <div class="text-center p-4">
                <div class="h-10 w-10 mx-auto bg-pink-100 rounded-full flex items-center justify-center mb-2">
                    <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-700">Response within 24h</p>
                <p class="text-xs text-slate-400">Weekdays only</p>
            </div>
            <div class="text-center p-4">
                <div class="h-10 w-10 mx-auto bg-cyan-100 rounded-full flex items-center justify-center mb-2">
                    <svg class="h-5 w-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-700">Email notifications</p>
                <p class="text-xs text-slate-400">Stay updated on replies</p>
            </div>
            <div class="text-center p-4">
                <div class="h-10 w-10 mx-auto bg-emerald-100 rounded-full flex items-center justify-center mb-2">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-700">Track progress</p>
                <p class="text-xs text-slate-400">View status anytime</p>
            </div>
        </div>
    </div>
</main>
@endsection