@extends('layouts.admin')

@section('title', 'Manage '.$order->displayNumber().' | Printbuka')

@section('content')
    @php($admin = auth()->user())
    @php($canEditDesign = $admin->canAdmin('design.update') || $admin->canAdmin('*'))
    @php($canUploadDesign = $admin->canAdmin('design.upload') || $admin->canAdmin('*'))
    @php($canEditProduction = $admin->canAdmin('production.update') || $admin->canAdmin('packaging.update') || $admin->canAdmin('*'))
    @php($canEditQc = $admin->canAdmin('qc.update') || $admin->canAdmin('*'))
    @php($canEditDelivery = $admin->canAdmin('delivery.update') || $admin->canAdmin('*'))
    @php($canEditClientReview = $admin->canAdmin('client_review.update') || $admin->canAdmin('*'))

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-8 text-white shadow-xl">
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-black text-cyan-300">Back to Job Tracker</a>
            <h1 class="mt-3 text-4xl font-black lg:text-5xl">{{ $order->job_order_number ?? $order->displayNumber() }}</h1>
            <p class="mt-2 text-sm text-slate-300">
                {{ $order->customer_name }} · {{ $order->invoice?->invoice_number ?? 'Invoice pending' }} · {{ $order->status }}
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
                <p class="text-sm font-black text-pink-700">Please resolve the following:</p>
                <ul class="mt-2 list-disc pl-5 text-sm font-semibold text-pink-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
        @endif

        @if (session('warning'))
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800">{{ session('warning') }}</div>
        @endif

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Locked Intake Fields</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">These fields are muted and cannot be edited by staff/admin.</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Job Order Number</p>
                        <p class="mt-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 font-semibold text-slate-800">{{ $order->job_order_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Channel</p>
                        <p class="mt-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 font-semibold text-slate-800">{{ $order->channel ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Brief Received By</p>
                        <p class="mt-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 font-semibold text-slate-800">{{ $order->briefReceiver?->displayName() ?? 'Pending' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Brief Date</p>
                        <p class="mt-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 font-semibold text-slate-800">{{ $order->brief_received_at?->format('M j, Y h:i A') ?? 'Pending' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Assigned Designer (Automatic)</p>
                        <p class="mt-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 font-semibold text-slate-800">{{ $order->designer?->displayName() ?? 'Awaiting automatic assignment' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Artwork Notes (Customer-managed)</p>
                        <p class="mt-1 min-h-20 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">{{ $order->artwork_notes ?: 'No artwork notes provided by customer yet.' }}</p>
                    </div>
                </div>

                @if ($canReceiveBrief)
                    <form action="{{ route('admin.orders.receive-brief', $order) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-black text-white transition hover:bg-slate-700">
                            Receive Job Brief
                        </button>
                    </form>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Workflow Gates</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">Job progress uses operations-manager approvals.</p>

                <div class="mt-4 space-y-4">
                    @foreach ($visibleWorkflowPhases as $phase)
                        @php($isCurrentPhase = (string) $phase['status'] === (string) $order->status)
                        <article class="rounded-xl border {{ $isCurrentPhase ? 'border-pink-300 bg-pink-50/40' : 'border-slate-200 bg-white' }} p-4">
                            <p class="font-black text-slate-900">{{ $phase['phase'] }}</p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $phase['responsible'] }} · {{ $phase['status'] }}</p>

                            @if ($isCurrentPhase)
                                <div class="mt-3 rounded-lg bg-white p-3 text-xs font-semibold text-slate-700">
                                    Current stage.
                                    @if ($nextWorkflowStatus)
                                        Next: <span class="font-black text-slate-900">{{ $nextWorkflowStatus }}</span>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                @if ($nextWorkflowStatus && $canRequestMoveForward)
                    <form action="{{ route('admin.orders.move-forward', $order) }}" method="POST" class="mt-5">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-pink-600 px-4 py-3 text-sm font-black text-white transition hover:bg-pink-700">
                            Move Job Forward · {{ $order->status }} → {{ $nextWorkflowStatus }}
                        </button>
                    </form>
                @endif

                @if ($order->requested_next_status)
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-amber-700">Pending Operations Approval</p>
                        <p class="mt-1 text-sm font-semibold text-amber-800">Requested next status: {{ $order->requested_next_status }}</p>
                        @if ($canApproveWorkflow)
                            <form action="{{ route('admin.orders.approve-forward', $order) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-black text-white transition hover:bg-emerald-700">
                                    Approve Move Forward
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <form action="{{ route('admin.orders.update', $order) }}" method="POST" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="grid gap-5 sm:grid-cols-2">
                <label class="text-sm font-black">
                    Job Type
                    <select name="job_type" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                        <option value="">Select job type</option>
                        @foreach ($jobTypes as $jobType)
                            <option @selected(old('job_type', $order->job_type ?? $order->product?->name) === $jobType)>{{ $jobType }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="text-sm font-black">
                    Size / Format
                    <select name="size_format" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                        <option value="">Select size</option>
                        @foreach ($sizes as $size)
                            <option @selected(old('size_format', $order->size_format) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="text-sm font-black">
                    Priority
                    <select name="priority" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                        @foreach ($priorities as $priority)
                            <option @selected(old('priority', $order->priority) === $priority)>{{ $priority }}</option>
                        @endforeach
                    </select>
                </label>

                @if ($canViewAmounts)
                    <label class="text-sm font-black">
                        Payment Status
                        <select name="payment_status" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                            @foreach ($paymentStatuses as $status)
                                <option @selected(old('payment_status', $order->payment_status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="text-sm font-black">
                        Amount Paid
                        <input type="number" step="0.01" min="0" name="amount_paid" value="{{ old('amount_paid', $order->amount_paid) }}" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>
                @endif

                @if ($canEditDesign)
                    <label class="text-sm font-black">
                        Design Start
                        <input type="datetime-local" name="design_started_at" value="{{ old('design_started_at', $order->design_started_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>

                    <label class="text-sm font-black">
                        Client Approval Date
                        <input type="datetime-local" name="design_approved_at" value="{{ old('design_approved_at', $order->design_approved_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>

                    <label class="sm:col-span-2 inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-black">
                        <input type="hidden" name="design_approved_by_client" value="0">
                        <input type="checkbox" name="design_approved_by_client" value="1" @checked((bool) old('design_approved_by_client', $order->design_approved_by_client))>
                        Design approved by client
                    </label>
                @endif

                @if ($canUploadDesign)
                    <label class="text-sm font-black sm:col-span-2">
                        Upload Final Design File
                        <input type="file" name="design_file" accept=".pdf,.svg,.zip" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-black text-slate-700">Upload Final Design Image</label>
                        <livewire:uploads.secure-image-upload
                            input-name="design_image_path"
                            directory="designs/images"
                            :max-size-kb="10240"
                            :max-files="1"
                            :initial-path="old('design_image_path')"
                        />
                    </div>
                @endif

                @if ($canEditProduction)
                    <label class="text-sm font-black">
                        Production Officer
                        <select name="production_officer_id" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                            <option value="">Select staff</option>
                            @foreach ($staff as $person)
                                <option value="{{ $person->id }}" @selected((int) old('production_officer_id', $order->production_officer_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="text-sm font-black">
                        Production Start
                        <input type="datetime-local" name="production_started_at" value="{{ old('production_started_at', $order->production_started_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>

                    <label class="text-sm font-black">
                        Material / Substrate
                        <select name="material_substrate" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                            <option value="">Select material</option>
                            @foreach ($materials as $material)
                                <option @selected(old('material_substrate', $order->material_substrate) === $material)>{{ $material }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="text-sm font-black">
                        Finish / Lamination
                        <select name="finish_lamination" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                            <option value="">Select finish</option>
                            @foreach ($finishes as $finish)
                                <option @selected(old('finish_lamination', $order->finish_lamination) === $finish)>{{ $finish }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                @if ($canEditQc)
                    <label class="text-sm font-black">
                        QC Checked By
                        <select name="qc_checked_by_id" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                            <option value="">Select staff</option>
                            @foreach ($staff as $person)
                                <option value="{{ $person->id }}" @selected((int) old('qc_checked_by_id', $order->qc_checked_by_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="text-sm font-black">
                        QC Date
                        <input type="datetime-local" name="qc_checked_at" value="{{ old('qc_checked_at', $order->qc_checked_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>

                    <label class="text-sm font-black sm:col-span-2">
                        QC Result
                        <input name="qc_result" value="{{ old('qc_result', $order->qc_result) }}" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>
                @endif

                @if ($canEditDelivery)
                    <label class="text-sm font-black">
                        Delivery Method
                        <select name="delivery_method" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                            <option value="">Select method</option>
                            @foreach ($deliveryMethods as $method)
                                <option @selected(old('delivery_method', $order->delivery_method) === $method)>{{ $method }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="text-sm font-black">
                        Estimated Delivery
                        <input type="datetime-local" name="estimated_delivery_at" value="{{ old('estimated_delivery_at', $order->estimated_delivery_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>

                    <label class="text-sm font-black">
                        Actual Delivery
                        <input type="datetime-local" name="actual_delivery_at" value="{{ old('actual_delivery_at', $order->actual_delivery_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>

                    <label class="text-sm font-black">
                        Dispatched By
                        <select name="dispatched_by_id" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                            <option value="">Select staff</option>
                            @foreach ($staff as $person)
                                <option value="{{ $person->id }}" @selected((int) old('dispatched_by_id', $order->dispatched_by_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                @if ($canEditClientReview)
                    <label class="text-sm font-black">
                        Client Review
                        <select name="client_review_status" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                            <option value="">Select status</option>
                            @foreach ($reviewStatuses as $status)
                                <option @selected(old('client_review_status', $order->client_review_status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="text-sm font-black">
                        After-Sales Resolved
                        <input type="datetime-local" name="after_sales_resolved_at" value="{{ old('after_sales_resolved_at', $order->after_sales_resolved_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3">
                    </label>

                    <label class="text-sm font-black sm:col-span-2">
                        After-Sales Action
                        <textarea name="after_sales_action" rows="3" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3">{{ old('after_sales_action', $order->after_sales_action) }}</textarea>
                    </label>
                @endif

                <label class="text-sm font-black sm:col-span-2">
                    Internal Notes
                    <textarea name="internal_notes" rows="4" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3">{{ old('internal_notes', $order->internal_notes) }}</textarea>
                </label>
            </div>

            <button type="submit" class="mt-6 w-full rounded-xl bg-slate-900 px-6 py-3 text-sm font-black text-white transition hover:bg-slate-700">
                Save Workflow Update
            </button>
        </form>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Client Artwork Assets</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse (($order->job_image_assets ?? []) as $asset)
                    <a href="{{ \App\Support\MediaUrl::resolve($asset['path']) }}" target="_blank" rel="noopener noreferrer" class="rounded-xl border border-slate-200 p-4 text-sm font-black text-slate-800 hover:border-pink-300 hover:bg-pink-50">
                        {{ $asset['name'] ?? basename($asset['path']) }}
                    </a>
                @empty
                    <p class="text-sm font-semibold text-slate-500">No client assets uploaded yet.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
