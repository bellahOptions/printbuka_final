@extends('layouts.admin')

@section('title', 'Manage '.$order->displayNumber().' | Printbuka')

@section('content')
    @php
        $admin = auth()->user();
        $canEditDesign = $admin->canAdmin('design.update') || $admin->canAdmin('*');
        $canUploadDesign = $admin->canAdmin('design.upload') || $admin->canAdmin('*');
        $canEditProduction = $admin->canAdmin('production.update') || $admin->canAdmin('packaging.update') || $admin->canAdmin('*');
        $canEditQc = $admin->canAdmin('qc.update') || $admin->canAdmin('*');
        $canEditDelivery = $admin->canAdmin('delivery.update') || $admin->canAdmin('*');
        $invoicePaid = $order->invoice && (string) $order->invoice->status === 'paid';
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="pb-page-header">
            <div>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-700">← Back to Job Tracker</a>
                <div class="mt-1 flex flex-wrap items-center gap-3">
                    <h1 class="pb-page-title">{{ $order->job_order_number ?? $order->displayNumber() }}</h1>
                    @if ($order->is_concluded)
                        <span class="pb-badge pb-badge-success">Concluded</span>
                    @endif
                </div>
                <p class="pb-page-subtitle">
                    {{ $order->customer_name }}
                    · {{ $order->invoice?->invoice_number ?? 'Invoice pending' }}
                    · {{ $order->status }}
                    · <span class="{{ $invoicePaid ? 'text-emerald-600' : 'text-amber-600' }}">{{ $order->payment_status }}</span>
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.orders.job-log', $order) }}" class="pb-btn pb-btn-md pb-btn-outline">View Job Log</a>
                <a href="{{ route('admin.orders.job-log.download', $order) }}" class="pb-btn pb-btn-md pb-btn-outline">Download Job Log</a>
                @if (! $order->is_concluded && $canConcludeJob)
                    <form action="{{ route('admin.orders.conclude', $order) }}" method="POST" onsubmit="return confirm('Conclude this job? This will lock all edits and auto-settle the invoice permanently.');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="pb-btn pb-btn-md pb-btn-success">Conclude Job</button>
                    </form>
                @endif
            </div>
        </div>

        @if ($errors->any())
            <div class="pb-alert pb-alert-error items-start">
                <div>
                    <p class="font-semibold">Please resolve the following:</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
        @endif

        @if (session('warning'))
            <div class="pb-alert pb-alert-warning">{{ session('warning') }}</div>
        @endif

        @if ($order->is_concluded)
            <div class="pb-alert pb-alert-success">
                <x-heroicon-s-check-circle class="w-4 h-4 inline mr-1 shrink-0" /> This job was concluded on {{ $order->concluded_at?->format('M j, Y h:i A') ?? 'N/A' }} by {{ $order->concludedBy?->displayName() ?? 'N/A' }}.
                @if ($order->invoice)
                    Invoice <strong>{{ $order->invoice->invoice_number }}</strong> was auto-settled.
                @endif
                All edits are permanently locked.
            </div>
        @endif

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="pb-card p-6">
                <h2 class="pb-section-title">Job Information</h2>
                <p class="pb-section-subtitle">Key details about this job order.</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="pb-label">Job Order Number</p>
                        <p class="mt-1 pb-input bg-slate-50 flex items-center">{{ $order->job_order_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="pb-label">Channel</p>
                        <p class="mt-1 pb-input bg-slate-50 flex items-center">{{ $order->channel ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="pb-label">Brief Received By</p>
                        <p class="mt-1 pb-input bg-slate-50 flex items-center">{{ $order->briefReceiver?->displayName() ?? 'Pending' }}</p>
                    </div>
                    <div>
                        <p class="pb-label">Brief Date</p>
                        <p class="mt-1 pb-input bg-slate-50 flex items-center">{{ $order->brief_received_at?->format('M j, Y h:i A') ?? 'Pending' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="pb-label">Assigned Designer</p>
                        @if ($order->designer)
                            <p class="mt-1 pb-input bg-slate-50 flex items-center">{{ $order->designer->displayName() }}</p>
                        @elseif (! $order->brief_received_at)
                            <p class="mt-1 pb-input bg-slate-50 text-slate-400 flex items-center">Will be assigned automatically on brief receipt</p>
                        @else
                            <p class="mt-1 pb-input border-amber-300 bg-amber-50 text-amber-700 flex items-center">No active designer available — assign manually below</p>
                            @if (! $order->is_concluded && ($admin->canAdmin('*') || $admin->canAdmin('workflow.approve')))
                                @php
                                    $availableDesigners = \App\Models\User::query()
                                        ->whereIn('role', ['designer','graphic_designer','creative_designer'])
                                        ->where('is_active', true)
                                        ->orderBy('first_name')
                                        ->get();
                                @endphp
                                @if ($availableDesigners->isNotEmpty())
                                    <form action="{{ route('admin.orders.receive-brief', $order) }}" method="POST" class="mt-2 flex gap-2">
                                        @csrf
                                        <input type="hidden" name="force_designer_assign" value="1">
                                        <button type="submit" class="pb-btn pb-btn-sm pb-btn-secondary">
                                            Auto-assign Now
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endif
                    </div>
                    <div class="sm:col-span-2">
                        <p class="pb-label">Artwork Notes</p>
                        <p class="mt-1 pb-input bg-slate-50 text-slate-700 min-h-20 items-start pt-2">{{ $order->artwork_notes ?: 'No artwork notes provided by customer yet.' }}</p>
                    </div>
                </div>

                @if ($canReceiveBrief && ! $order->is_concluded)
                    <form action="{{ route('admin.orders.receive-brief', $order) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="pb-btn pb-btn-md pb-btn-ink">
                            Receive Job Brief
                        </button>
                    </form>
                @endif
            </div>

            <div class="pb-card p-6">
                <h2 class="pb-section-title">Workflow Gates</h2>
                <p class="pb-section-subtitle">Job progress uses operations-manager approvals.</p>

                <div class="mt-4 space-y-4">
                    @foreach ($visibleWorkflowPhases as $phase)
                        @php
                            $isCurrentPhase = (string) $phase['status'] === (string) $order->status;
                        @endphp
                        <article class="rounded-xl border {{ $isCurrentPhase ? 'border-pink-300 bg-pink-50/40' : 'border-slate-200 bg-white' }} p-4">
                            <p class="font-semibold text-slate-900">{{ $phase['phase'] }}</p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $phase['responsible'] }} · {{ $phase['status'] }}</p>

                            @if ($isCurrentPhase)
                                <div class="mt-3 rounded-lg bg-white p-3 text-xs font-semibold text-slate-700">
                                    Current stage.
                                    @if ($nextWorkflowStatus)
                                        Next: <span class="font-semibold text-slate-900">{{ $nextWorkflowStatus }}</span>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                @if ($nextWorkflowStatus && $canRequestMoveForward && ! $order->is_concluded)
                    <form action="{{ route('admin.orders.move-forward', $order) }}" method="POST" class="mt-5">
                        @csrf
                        <button type="submit" class="pb-btn pb-btn-md pb-btn-primary w-full">
                            Move Job Forward · {{ $order->status }} → {{ $nextWorkflowStatus }}
                        </button>
                    </form>
                @endif

                @if ($order->requested_next_status && ! $order->is_concluded)
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="pb-label text-amber-700">Pending Operations Approval</p>
                        <p class="mt-1 text-sm font-semibold text-amber-800">Requested next status: {{ $order->requested_next_status }}</p>
                        @if ($canApproveWorkflow)
                            <form action="{{ route('admin.orders.approve-forward', $order) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="pb-btn pb-btn-md pb-btn-success">
                                    Approve Move Forward
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <!-- Order Items -->
        @php
            $lineItems = $order->pricing_breakdown['line_items'] ?? [];
        @endphp
        @if (!empty($lineItems))
            <section class="pb-card p-6">
                <h2 class="pb-section-title">Order Items</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($lineItems as $item)
                        <article class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-2">
                            @if (! empty($item['image_path']))
                                <a href="{{ \App\Support\MediaUrl::resolve($item['image_path']) }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ \App\Support\MediaUrl::resolve($item['image_path']) }}" alt="{{ $item['description'] ?? 'Item image' }}" class="w-full h-36 object-cover rounded-md">
                                </a>
                            @endif
                            <p class="font-semibold text-slate-900">{{ $item['description'] ?? '—' }}</p>
                            <p class="text-sm text-slate-600">Qty: {{ $item['quantity'] ?? 1 }}</p>
                            @if (! empty($item['size_format']))<p class="text-xs text-slate-500">Size: {{ $item['size_format'] }}</p>@endif
                            @if (! empty($item['material_substrate']))<p class="text-xs text-slate-500">Material: {{ $item['material_substrate'] }}</p>@endif
                            @if (! empty($item['finish_lamination']))<p class="text-xs text-slate-500">Finish: {{ $item['finish_lamination'] }}</p>@endif
                            @if (! empty($item['artwork_notes']))<p class="text-xs text-slate-500">Notes: {{ $item['artwork_notes'] }}</p>@endif
                            @if (! empty($item['amount']))
                                <p class="text-sm font-semibold text-slate-900">₦{{ number_format((float) $item['amount'], 2) }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <form action="{{ route('admin.orders.update', $order) }}" method="POST" enctype="multipart/form-data" class="pb-card p-6">
            @csrf
            @method('PUT')

            @if ($order->is_concluded)
                <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800">
                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 inline mr-1 shrink-0" /> This job is concluded. Workflow edits are disabled.
                </div>
            @endif

            @php
                $singleOpsManager = ($operationsStaff->count() === 1) ? $operationsStaff->first() : null;
            @endphp

            <div class="grid gap-5 sm:grid-cols-2">
                <label class="pb-label">
                    Priority
                    <select name="priority" class="mt-2 pb-input">
                        @foreach ($priorities as $priority)
                            <option @selected(old('priority', $order->priority) === $priority)>{{ $priority }}</option>
                        @endforeach
                    </select>
                </label>

                @if ($canViewAmounts)
                    <label class="pb-label">
                        Payment Status
                        <select name="payment_status" class="mt-2 pb-input">
                            @foreach ($paymentStatuses as $status)
                                <option @selected(old('payment_status', $order->payment_status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="pb-label">
                        Amount Paid
                        <input type="number" step="0.01" min="0" name="amount_paid" value="{{ old('amount_paid', $order->amount_paid) }}" class="mt-2 pb-input">
                    </label>
                @endif

                @if ($canEditDesign)
                    <label class="pb-label">
                        Design Start
                        <input type="datetime-local" name="design_started_at" value="{{ old('design_started_at', $order->design_started_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 pb-input">
                    </label>

                    <label class="sm:col-span-2 inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold cursor-pointer hover:bg-slate-50">
                        <input type="hidden" name="design_approved_by_client" value="0">
                        <input type="checkbox" name="design_approved_by_client" value="1" class="rounded" @checked((bool) old('design_approved_by_client', $order->design_approved_by_client))>
                        <span>Design approved by client</span>
                        @if ($order->design_approved_at)
                            <span class="ml-auto text-xs font-semibold text-slate-400">Approved {{ $order->design_approved_at->format('M j, Y g:i A') }}</span>
                        @else
                            <span class="ml-auto text-xs font-semibold text-slate-400">Approval date auto-stamped on save</span>
                        @endif
                    </label>
                @endif

                @if ($canUploadDesign)
                    <label class="pb-label sm:col-span-2">
                        Upload Final Design File
                        <input type="file" name="design_file" accept=".pdf,.svg,.zip" class="mt-2 pb-input">
                    </label>
                @endif

                @if ($canEditProduction)
                    <div class="pb-label">
                        Operations Manager (Production)
                        @if ($singleOpsManager)
                            <input type="hidden" name="production_officer_id" value="{{ $singleOpsManager->id }}">
                            <div class="mt-2 min-h-12 flex items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-semibold text-slate-700">
                                {{ $singleOpsManager->displayName() }}
                                <span class="ml-2 text-xs font-normal text-slate-400">· auto-selected</span>
                            </div>
                        @else
                            <select name="production_officer_id" class="mt-2 pb-input">
                                <option value="">Select Operations Manager</option>
                                @foreach ($operationsStaff->isEmpty() ? $staff : $operationsStaff as $person)
                                    <option value="{{ $person->id }}" @selected((int) old('production_officer_id', $order->production_officer_id) === $person->id)>{{ $person->displayName() }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <label class="pb-label">
                        Production Start
                        <input type="datetime-local" name="production_started_at" value="{{ old('production_started_at', $order->production_started_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 pb-input">
                    </label>

                    <label class="pb-label">
                        Material / Substrate
                        <select name="material_substrate" class="mt-2 pb-input">
                            <option value="">Select material</option>
                            @foreach ($materials as $material)
                                <option @selected(old('material_substrate', $order->material_substrate) === $material)>{{ $material }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="pb-label">
                        Finish / Lamination
                        <select name="finish_lamination" class="mt-2 pb-input">
                            <option value="">Select finish</option>
                            @foreach ($finishes as $finish)
                                <option @selected(old('finish_lamination', $order->finish_lamination) === $finish)>{{ $finish }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                @if ($canEditQc)
                    <div class="pb-label">
                        QC Checked By
                        @if ($singleOpsManager)
                            <input type="hidden" name="qc_checked_by_id" value="{{ $singleOpsManager->id }}">
                            <div class="mt-2 min-h-12 flex items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-semibold text-slate-700">
                                {{ $singleOpsManager->displayName() }}
                                <span class="ml-2 text-xs font-normal text-slate-400">· auto-selected</span>
                            </div>
                        @else
                            <select name="qc_checked_by_id" class="mt-2 pb-input">
                                <option value="">Select Operations Manager</option>
                                @foreach ($operationsStaff->isEmpty() ? $staff : $operationsStaff as $person)
                                    <option value="{{ $person->id }}" @selected((int) old('qc_checked_by_id', $order->qc_checked_by_id) === $person->id)>{{ $person->displayName() }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <label class="pb-label">
                        QC Date
                        <input type="datetime-local" name="qc_checked_at" value="{{ old('qc_checked_at', $order->qc_checked_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 pb-input">
                    </label>

                    <label class="pb-label sm:col-span-2">
                        QC Result
                        <input name="qc_result" value="{{ old('qc_result', $order->qc_result) }}" class="mt-2 pb-input">
                    </label>
                @endif

                @if ($canEditDelivery)
                    <label class="pb-label">
                        Delivery Method
                        <select name="delivery_method" class="mt-2 pb-input">
                            <option value="">Select method</option>
                            @foreach ($deliveryMethods as $method)
                                <option @selected(old('delivery_method', $order->delivery_method) === $method)>{{ $method }}</option>
                            @endforeach
                        </select>
                    </label>

                    @php
                        // Auto-calculate estimated delivery: 48 hrs from production start (24 if express)
                        // Staff can still override the value
                        $deliveryHours = $order->is_express ? 24 : 48;
                        $autoEstimated = $order->estimated_delivery_at
                            ?? ($order->production_started_at
                                ? $order->production_started_at->copy()->addHours($deliveryHours)
                                : now()->addHours($deliveryHours));
                    @endphp
                    <label class="pb-label">
                        Estimated Delivery
                        <span class="ml-1 font-normal text-slate-400 normal-case text-xs">(auto: {{ $deliveryHours }}h{{ $order->is_express ? ' · express' : '' }})</span>
                        <input type="datetime-local" name="estimated_delivery_at"
                               value="{{ old('estimated_delivery_at', $autoEstimated->format('Y-m-d\\TH:i')) }}"
                               class="mt-2 pb-input {{ $order->estimated_delivery_at ? '' : 'border-amber-300 bg-amber-50' }}">
                    </label>

                    <label class="pb-label">
                        Actual Delivery
                        <input type="datetime-local" name="actual_delivery_at" value="{{ old('actual_delivery_at', $order->actual_delivery_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 pb-input">
                    </label>

                    <label class="pb-label">
                        Dispatched By
                        <select name="dispatched_by_id" class="mt-2 pb-input">
                            <option value="">Select staff</option>
                            @foreach ($customerServiceStaff->isEmpty() ? $staff : $customerServiceStaff as $person)
                                <option value="{{ $person->id }}" @selected((int) old('dispatched_by_id', $order->dispatched_by_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif


                <label class="pb-label sm:col-span-2">
                    Internal Notes
                    <textarea name="internal_notes" rows="4" data-rich-editor class="mt-2 pb-textarea">{{ old('internal_notes', $order->internal_notes) }}</textarea>
                </label>
            </div>

            <button type="submit" @disabled($order->is_concluded) class="mt-6 pb-btn pb-btn-lg pb-btn-ink w-full">
                Save Workflow Update
            </button>
        </form>

        <section class="pb-card p-6">
            <h2 class="pb-section-title">Client Artwork Assets</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse (($order->job_image_assets ?? []) as $asset)
                    <a href="{{ \App\Support\MediaUrl::resolve($asset['path']) }}" target="_blank" rel="noopener noreferrer" class="rounded-xl border border-slate-200 p-4 text-sm font-semibold text-slate-800 hover:border-pink-300 hover:bg-pink-50">
                        {{ $asset['name'] ?? basename($asset['path']) }}
                    </a>
                @empty
                    <p class="text-sm font-semibold text-slate-500">No client assets uploaded yet.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
