@extends('layouts.admin')

@section('title', 'Manage '.$order->displayNumber().' | Printbuka')

@section('content')
    @php($admin = auth()->user())
    @php($canEditStatus = collect($workflowPhases)->contains(fn ($phase) => $admin->canAdmin((string) ($phase['permission'] ?? ''))) || $admin->canAdmin('workflow.approve') || $admin->canAdmin('*'))
    @php($canEditDelivery = $admin->canAdmin('delivery.update') || $admin->canAdmin('*'))
    @php($canEditClientReview = $admin->canAdmin('client_review.update') || $admin->canAdmin('*'))
    @php($canVerifyOrders = $admin->canAdmin('orders.verify') || $admin->canAdmin('*'))
    <div class="mx-auto max-w-7xl space-y-6">
        <!-- Hero Section -->
        <div class="fade-in-up rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-8 text-white shadow-xl">
            <div class="flex items-center gap-2 mb-4">
                <a href="{{ route('admin.orders.index') }}" class="group inline-flex items-center gap-2 text-sm font-black text-cyan-300 transition-colors hover:text-cyan-200">
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Job Tracker
                </a>
            </div>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-4xl font-black tracking-tight lg:text-5xl">{{ $order->job_order_number ?? $order->displayNumber() }}</h1>
                        <span class="inline-flex items-center rounded-full bg-cyan-500/20 px-3 py-1 text-xs font-black uppercase tracking-wider text-cyan-300 border border-cyan-500/30">
                            {{ $order->status }}
                        </span>
                    </div>
                    <p class="max-w-3xl text-base leading-relaxed text-slate-300">
                        {{ $order->customer_name }} · {{ $order->product?->name ?? 'Custom order' }} · {{ $order->invoice?->invoice_number ?? 'Invoice pending' }} · Created by {{ $order->creatorAdmin?->displayName() ?? $order->briefReceiver?->displayName() ?? 'System' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        @if (session('status'))
            <div class="fade-in-up rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-bold text-emerald-800">{{ session('status') }}</p>
                </div>
            </div>
        @endif
        @if (session('warning'))
            <div class="fade-in-up rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm font-bold text-amber-800">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.orders.update', $order) }}" method="POST" enctype="multipart/form-data" class="fade-in-up section-delay-1 grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
            @csrf
            @method('PUT')

            <!-- Main Form Section -->
            <section class="space-y-6">
                @if ($admin->canAdmin('orders.intake'))
                    <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-pink-100 to-pink-50 border border-pink-200">
                                <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Phase 1 — Intake</h2>
                                <p class="text-sm text-slate-500">Initial job intake and assignment</p>
                            </div>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Job Order #</label><input name="job_order_number" value="{{ old('job_order_number', $order->job_order_number) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Channel</label><select name="channel" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800">@foreach ($channels as $channel)<option @selected(old('channel', $order->channel) === $channel)>{{ $channel }}</option>@endforeach</select></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Job Type</label><select name="job_type" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select job type</option>@foreach ($jobTypes as $jobType)<option @selected(old('job_type', $order->job_type ?? $order->product?->name) === $jobType)>{{ $jobType }}</option>@endforeach</select></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Size / Format</label><select name="size_format" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select size</option>@foreach ($sizes as $size)<option @selected(old('size_format', $order->size_format) === $size)>{{ $size }}</option>@endforeach</select></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Priority</label><select name="priority" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800">@foreach ($priorities as $priority)<option @selected(old('priority', $order->priority) === $priority)>{{ $priority }}</option>@endforeach</select></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Brief Received By</label><select name="brief_received_by_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select staff</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('brief_received_by_id', $order->brief_received_by_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Brief Date</label><input type="datetime-local" name="brief_received_at" value="{{ old('brief_received_at', $order->brief_received_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                            <div class="space-y-1 sm:col-span-2"><label class="text-sm font-black text-slate-700">Assigned Designer</label><select name="assigned_designer_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select designer</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('assigned_designer_id', $order->assigned_designer_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></div>
                            <div class="space-y-1 sm:col-span-2"><label class="text-sm font-black text-slate-700">Artwork Notes</label><textarea name="artwork_notes" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800">{{ old('artwork_notes', $order->artwork_notes) }}</textarea></div>
                            <div class="space-y-1 sm:col-span-2">
                                <label class="text-sm font-black text-slate-700">Add Job Image Assets</label>
                                <livewire:uploads.secure-image-upload
                                    input-name="job_asset_image_paths"
                                    :multiple="true"
                                    directory="job-assets/images"
                                    :max-size-kb="5120"
                                    :max-files="20"
                                    :initial-paths="old('job_asset_image_paths', [])"
                                />
                                <p class="mt-2 text-xs text-slate-500">Upload images via secure Livewire flow. New images are added to existing assets.</p>
                                @error('job_asset_image_paths')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                                @error('job_asset_image_paths.*')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1 sm:col-span-2">
                                <label class="text-sm font-black text-slate-700">Add Artwork Documents (PDF, SVG, ZIP)</label>
                                <input type="file" name="job_asset_files[]" multiple accept=".pdf,.svg,.zip" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800">
                                <p class="mt-2 text-xs text-slate-500">Use this field for non-image artwork documents only.</p>
                                @error('job_asset_files.*')
                                    <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                @if ($admin->canAdmin('design.update'))
                    <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-cyan-100 to-cyan-50 border border-cyan-200">
                                <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Phase 2 — Design</h2>
                                <p class="text-sm text-slate-500">Design progress and approvals</p>
                            </div>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Design Start</label><input type="datetime-local" name="design_started_at" value="{{ old('design_started_at', $order->design_started_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Client Approval</label><input type="datetime-local" name="design_approved_at" value="{{ old('design_approved_at', $order->design_approved_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                            <label class="flex items-center gap-3 rounded-xl border-2 border-slate-200 px-5 py-4 text-sm font-black transition-all duration-300 hover:border-pink-200 sm:col-span-2">
                                <input type="hidden" name="design_approved_by_client" value="0">
                                <input type="checkbox" name="design_approved_by_client" value="1" @checked((bool) old('design_approved_by_client', $order->design_approved_by_client)) class="h-5 w-5 rounded border-slate-300 text-pink-600 focus:ring-pink-500">
                                Design approved by client
                            </label>
                            @if ($admin->canAdmin('design.upload'))
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="text-sm font-black text-slate-700">Upload Final Design Image</label>
                                    <livewire:uploads.secure-image-upload
                                        input-name="design_image_path"
                                        directory="designs/images"
                                        :max-size-kb="10240"
                                        :max-files="1"
                                        :initial-path="old('design_image_path')"
                                    />
                                    <p class="mt-2 text-xs text-slate-500">Use this field for final design images (JPG, PNG, WEBP).</p>
                                    @error('design_image_path')
                                        <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="text-sm font-black text-slate-700">Upload Final Design Document (PDF, SVG, ZIP)</label>
                                    <input type="file" name="design_file" accept=".pdf,.svg,.zip" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800">
                                    @error('design_file')
                                        <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($admin->canAdmin('production.update') || $admin->canAdmin('packaging.update'))
                    <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 border border-emerald-200">
                                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Phase 3 — Production</h2>
                                <p class="text-sm text-slate-500">Production tracking and materials</p>
                            </div>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Production Officer</label><select name="production_officer_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select staff</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('production_officer_id', $order->production_officer_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Production Start</label><input type="datetime-local" name="production_started_at" value="{{ old('production_started_at', $order->production_started_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Material / Substrate</label><select name="material_substrate" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select material</option>@foreach ($materials as $material)<option @selected(old('material_substrate', $order->material_substrate) === $material)>{{ $material }}</option>@endforeach</select></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">Finish / Lamination</label><select name="finish_lamination" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select finish</option>@foreach ($finishes as $finish)<option @selected(old('finish_lamination', $order->finish_lamination) === $finish)>{{ $finish }}</option>@endforeach</select></div>
                        </div>
                    </div>
                @endif

                @if ($admin->canAdmin('qc.update'))
                    <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-amber-100 to-amber-50 border border-amber-200">
                                <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Phase 4 — QC & Packaging</h2>
                                <p class="text-sm text-slate-500">Quality control checks</p>
                            </div>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">QC Checked By</label><select name="qc_checked_by_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select staff</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('qc_checked_by_id', $order->qc_checked_by_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></div>
                            <div class="space-y-1"><label class="text-sm font-black text-slate-700">QC Date</label><input type="datetime-local" name="qc_checked_at" value="{{ old('qc_checked_at', $order->qc_checked_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                            <div class="space-y-1 sm:col-span-2"><label class="text-sm font-black text-slate-700">QC Result</label><input name="qc_result" value="{{ old('qc_result', $order->qc_result) }}" placeholder="Passed / Failed — Reprint" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                        </div>
                    </div>
                @endif

                @if ($canEditStatus || $canEditDelivery || $canEditClientReview || $canViewAmounts || $canVerifyOrders)
                    <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-slate-100 to-slate-50 border border-slate-200">
                                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Status, Delivery & Financials</h2>
                                <p class="text-sm text-slate-500">Final stages and payment tracking</p>
                            </div>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            @if ($canEditStatus && count($statusOptions) > 0)
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Job Status</label><select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800">@foreach ($statusOptions as $status)<option @selected(old('status', $order->status) === $status)>{{ $status }}</option>@endforeach</select></div>
                            @endif
                            @if ($canViewAmounts)
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Payment Status</label><select name="payment_status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800">@foreach ($paymentStatuses as $status)<option @selected(old('payment_status', $order->payment_status) === $status)>{{ $status }}</option>@endforeach</select></div>
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Amount Paid</label><input type="number" step="0.01" min="0" name="amount_paid" value="{{ old('amount_paid', $order->amount_paid) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                            @endif
                            @if ($canEditDelivery)
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Delivery Method</label><select name="delivery_method" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select method</option>@foreach ($deliveryMethods as $method)<option @selected(old('delivery_method', $order->delivery_method) === $method)>{{ $method }}</option>@endforeach</select></div>
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Estimated Delivery</label><input type="datetime-local" name="estimated_delivery_at" value="{{ old('estimated_delivery_at', $order->estimated_delivery_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Actual Delivery</label><input type="datetime-local" name="actual_delivery_at" value="{{ old('actual_delivery_at', $order->actual_delivery_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Dispatched By</label><select name="dispatched_by_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select staff</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('dispatched_by_id', $order->dispatched_by_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></div>
                            @endif
                            @if ($canEditClientReview)
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Client Review</label><select name="client_review_status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option value="">Select review status</option>@foreach ($reviewStatuses as $status)<option @selected(old('client_review_status', $order->client_review_status) === $status)>{{ $status }}</option>@endforeach</select></div>
                                <div class="space-y-1 sm:col-span-2"><label class="text-sm font-black text-slate-700">After-Sales Action</label><textarea name="after_sales_action" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800">{{ old('after_sales_action', $order->after_sales_action) }}</textarea></div>
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">After-Sales Resolved</label><input type="datetime-local" name="after_sales_resolved_at" value="{{ old('after_sales_resolved_at', $order->after_sales_resolved_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                            @endif
                            @if ($canVerifyOrders)
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Verified At</label><input type="datetime-local" name="verified_at" value="{{ old('verified_at', $order->verified_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"></div>
                            @endif
                            @if ($admin->canAdmin('workflow.approve'))
                                <div class="space-y-1"><label class="text-sm font-black text-slate-700">Phase Approval</label><select name="phase_approval_status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800"><option @selected(old('phase_approval_status', $order->phase_approval_status) === 'Pending Operations Approval')>Pending Operations Approval</option><option @selected(old('phase_approval_status', $order->phase_approval_status) === 'Approved')>Approved</option><option @selected(old('phase_approval_status', $order->phase_approval_status) === 'Returned for Critical Review')>Returned for Critical Review</option></select></div>
                                <div class="space-y-1 sm:col-span-2"><label class="text-sm font-black text-slate-700">Operations Comment</label><textarea name="phase_approval_comment" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800">{{ old('phase_approval_comment', $order->phase_approval_comment) }}</textarea></div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-slate-100 to-slate-50 border border-slate-200">
                            <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Internal Notes</h2>
                            <p class="text-sm text-slate-500">Private notes visible to staff only</p>
                        </div>
                    </div>
                    <textarea name="internal_notes" rows="5" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-800">{{ old('internal_notes', $order->internal_notes) }}</textarea>
                </div>

                <button type="submit" class="btn-primary group relative w-full overflow-hidden rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-6 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30 hover:scale-[1.02]">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Workflow Update
                    </span>
                    <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                </button>
            </section>

            <!-- Sidebar -->
            <aside class="space-y-6">
                <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-black uppercase tracking-wider text-pink-700">Order Summary</p>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-1 border-b border-slate-100"><span class="font-bold text-slate-500">Client:</span><span class="font-semibold text-slate-900">{{ $order->customer_name }}</span></div>
                        <div class="flex justify-between py-1 border-b border-slate-100"><span class="font-bold text-slate-500">Contact:</span><span class="font-semibold text-slate-900">{{ $order->customer_phone }} · {{ $order->customer_email }}</span></div>
                        <div class="flex justify-between py-1 border-b border-slate-100"><span class="font-bold text-slate-500">Product:</span><span class="font-semibold text-slate-900">{{ $order->product?->name ?? 'Custom order' }}</span></div>
                        <div class="flex justify-between py-1 border-b border-slate-100"><span class="font-bold text-slate-500">Qty:</span><span class="font-semibold text-slate-900">{{ $order->quantity }}</span></div>
                        <div class="flex justify-between py-1 border-b border-slate-100"><span class="font-bold text-slate-500">Fulfilment:</span><span class="font-semibold text-slate-900">{{ $order->is_sample ? 'Sample · Express' : ($order->is_express ? 'Express' : 'Standard') }}</span></div>
                        <div class="flex justify-between py-1 border-b border-slate-100"><span class="font-bold text-slate-500">ETA:</span><span class="font-semibold text-slate-900">{{ $order->estimated_delivery_at?->format('M d, Y h:i A') ?? 'Pending' }}</span></div>
                        @if ($canViewAmounts)
                            <div class="flex justify-between py-1 border-b border-slate-100"><span class="font-bold text-slate-500">Total:</span><span class="font-semibold text-slate-900">₦{{ number_format($order->total_price, 2) }}</span></div>
                            <div class="flex justify-between py-1 border-b border-slate-100"><span class="font-bold text-slate-500">Paid:</span><span class="font-semibold text-slate-900">₦{{ number_format((float) $order->amount_paid, 2) }}</span></div>
                        @endif
                        <div class="flex justify-between py-1"><span class="font-bold text-slate-500">Phase:</span><span class="font-semibold text-slate-900">{{ $order->phase_approval_status ?? 'Pending' }}</span></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-black uppercase tracking-wider text-cyan-700">Client Artwork</p>
                    </div>
                    <div class="space-y-2">
                        @forelse (($order->job_image_assets ?? []) as $asset)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($asset['path']) }}" target="_blank" rel="noopener noreferrer" class="block rounded-xl border border-slate-200 p-4 text-sm font-black text-slate-800 transition-all duration-300 hover:border-pink-300 hover:bg-pink-50/30 hover:text-pink-700">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $asset['name'] ?? basename($asset['path']) }}
                                </div>
                            </a>
                        @empty
                            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-500 text-center">No client assets uploaded yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200/60 bg-gradient-to-br from-cyan-50/50 to-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-sm font-black uppercase tracking-wider text-cyan-700">Permission</p>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-600">Your role is <strong class="text-slate-900">{{ auth()->user()->role }}</strong>. Sections shown match SOP responsibility for that role.</p>
                </div>

                <div class="rounded-2xl border border-slate-200/60 bg-gradient-to-br from-pink-50/50 to-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm font-black uppercase tracking-wider text-pink-700">SOP Phase Gates</p>
                    </div>
                    <div class="space-y-4">
                        @forelse ($visibleWorkflowPhases as $phase)
                            <div class="border-b border-slate-100 pb-3 last:border-0">
                                <p class="font-black text-slate-950">{{ $phase['phase'] }}</p>
                                <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{{ $phase['responsible'] }} · {{ $phase['status'] }}</p>
                                <ul class="mt-2 space-y-1 text-xs leading-relaxed text-slate-600">
                                    @foreach ($phase['gates'] as $gate)
                                        <li class="flex items-start gap-2">
                                            <span class="mt-1.5 w-1 h-1 rounded-full bg-pink-400 flex-shrink-0"></span>
                                            {{ $gate }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">No workflow phases are assigned to your role.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </form>
    </div>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; }
        .section-delay-1 { animation-delay: 0.05s; }
    </style>
@endsection
