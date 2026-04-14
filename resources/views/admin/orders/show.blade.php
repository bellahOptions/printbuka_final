@extends('layouts.admin')

@section('title', 'Manage '.$order->displayNumber().' | Printbuka')

@section('content')
    @php($admin = auth()->user())
    <div class="mx-auto max-w-7xl">
            <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-black text-cyan-300 hover:text-cyan-200">Back to Job Tracker</a>
                <h1 class="mt-3 text-4xl">{{ $order->job_order_number ?? $order->displayNumber() }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">{{ $order->customer_name }} · {{ $order->product?->name ?? 'Custom order' }} · {{ $order->invoice?->invoice_number ?? 'Invoice pending' }}</p>
            </div>

            @if (session('status'))
                <p class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
            @endif
            @if (session('warning'))
                <p class="mt-6 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">{{ session('warning') }}</p>
            @endif

            <form action="{{ route('admin.orders.update', $order) }}" method="POST" enctype="multipart/form-data" class="mt-8 grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                @csrf
                @method('PUT')

                <section class="space-y-6">
                    @if ($admin->canAdmin('orders.intake'))
                        <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Phase 1 — Intake</p>
                            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                <label class="text-sm font-black">Job Order #<input name="job_order_number" value="{{ old('job_order_number', $order->job_order_number) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                <label class="text-sm font-black">Channel<select name="channel" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($channels as $channel)<option @selected(old('channel', $order->channel) === $channel)>{{ $channel }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">Job Type<select name="job_type" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select job type</option>@foreach ($jobTypes as $jobType)<option @selected(old('job_type', $order->job_type ?? $order->product?->name) === $jobType)>{{ $jobType }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">Size / Format<select name="size_format" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select size</option>@foreach ($sizes as $size)<option @selected(old('size_format', $order->size_format) === $size)>{{ $size }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">Priority<select name="priority" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($priorities as $priority)<option @selected(old('priority', $order->priority) === $priority)>{{ $priority }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">Brief Received By<select name="brief_received_by_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select staff</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('brief_received_by_id', $order->brief_received_by_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">Brief Date<input type="datetime-local" name="brief_received_at" value="{{ old('brief_received_at', $order->brief_received_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                <label class="text-sm font-black sm:col-span-2">Assigned Designer<select name="assigned_designer_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select designer</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('assigned_designer_id', $order->assigned_designer_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></label>
                                <label class="text-sm font-black sm:col-span-2">Artwork Notes<textarea name="artwork_notes" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('artwork_notes', $order->artwork_notes) }}</textarea></label>
                                <label class="text-sm font-black sm:col-span-2">Add Job Image / Artwork Assets<input type="file" name="job_asset_files[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.svg,.zip" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold"><span class="mt-2 block text-xs font-bold text-slate-500">New files are added to the existing client assets.</span></label>
                            </div>
                        </div>
                    @endif

                    @if ($admin->canAdmin('design.update'))
                        <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                            <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Phase 2 — Design</p>
                            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                <label class="text-sm font-black">Design Start<input type="datetime-local" name="design_started_at" value="{{ old('design_started_at', $order->design_started_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                <label class="text-sm font-black">Client Approval<input type="datetime-local" name="design_approved_at" value="{{ old('design_approved_at', $order->design_approved_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                <label class="flex items-center gap-3 rounded-md border border-slate-200 px-4 py-3 text-sm font-black sm:col-span-2">
                                    <input type="hidden" name="design_approved_by_client" value="0">
                                    <input type="checkbox" name="design_approved_by_client" value="1" @checked((bool) old('design_approved_by_client', $order->design_approved_by_client)) class="h-5 w-5 rounded border-slate-300 text-pink-600">
                                    Design approved by client
                                </label>
                                @if ($admin->canAdmin('design.upload'))
                                    <label class="text-sm font-black sm:col-span-2">Upload Final Design<input type="file" name="design_file" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold"></label>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($admin->canAdmin('production.update') || $admin->canAdmin('packaging.update'))
                        <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                            <p class="text-sm font-black uppercase tracking-wide text-emerald-700">Phase 3 — Production</p>
                            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                <label class="text-sm font-black">Production Officer<select name="production_officer_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select staff</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('production_officer_id', $order->production_officer_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">Production Start<input type="datetime-local" name="production_started_at" value="{{ old('production_started_at', $order->production_started_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                <label class="text-sm font-black">Material / Substrate<select name="material_substrate" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select material</option>@foreach ($materials as $material)<option @selected(old('material_substrate', $order->material_substrate) === $material)>{{ $material }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">Finish / Lamination<select name="finish_lamination" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select finish</option>@foreach ($finishes as $finish)<option @selected(old('finish_lamination', $order->finish_lamination) === $finish)>{{ $finish }}</option>@endforeach</select></label>
                            </div>
                        </div>
                    @endif

                    @if ($admin->canAdmin('qc.update'))
                        <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                            <p class="text-sm font-black uppercase tracking-wide text-amber-700">Phase 4 — QC & Packaging</p>
                            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                <label class="text-sm font-black">QC Checked By<select name="qc_checked_by_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select staff</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('qc_checked_by_id', $order->qc_checked_by_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">QC Date<input type="datetime-local" name="qc_checked_at" value="{{ old('qc_checked_at', $order->qc_checked_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                <label class="text-sm font-black sm:col-span-2">QC Result<input name="qc_result" value="{{ old('qc_result', $order->qc_result) }}" placeholder="Passed / Failed — Reprint" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                            </div>
                        </div>
                    @endif

                    @if ($admin->canAdmin('delivery.update') || $admin->canAdmin('client_review.update') || $admin->canAdmin('invoices.manage') || $admin->canAdmin('orders.verify'))
                        <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                            <p class="text-sm font-black uppercase tracking-wide text-slate-700">Status, Delivery & Financials</p>
                            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                <label class="text-sm font-black">Job Status<select name="status" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($jobStatuses as $status)<option @selected(old('status', $order->status) === $status)>{{ $status }}</option>@endforeach</select></label>
                                @if ($canViewAmounts)
                                    <label class="text-sm font-black">Payment Status<select name="payment_status" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($paymentStatuses as $status)<option @selected(old('payment_status', $order->payment_status) === $status)>{{ $status }}</option>@endforeach</select></label>
                                    <label class="text-sm font-black">Amount Paid<input type="number" step="0.01" min="0" name="amount_paid" value="{{ old('amount_paid', $order->amount_paid) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                @endif
                                <label class="text-sm font-black">Delivery Method<select name="delivery_method" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select method</option>@foreach ($deliveryMethods as $method)<option @selected(old('delivery_method', $order->delivery_method) === $method)>{{ $method }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">Estimated Delivery<input type="datetime-local" name="estimated_delivery_at" value="{{ old('estimated_delivery_at', $order->estimated_delivery_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                <label class="text-sm font-black">Actual Delivery<input type="datetime-local" name="actual_delivery_at" value="{{ old('actual_delivery_at', $order->actual_delivery_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                <label class="text-sm font-black">Dispatched By<select name="dispatched_by_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select staff</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('dispatched_by_id', $order->dispatched_by_id) === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></label>
                                <label class="text-sm font-black">Client Review<select name="client_review_status" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select review status</option>@foreach ($reviewStatuses as $status)<option @selected(old('client_review_status', $order->client_review_status) === $status)>{{ $status }}</option>@endforeach</select></label>
                                <label class="text-sm font-black sm:col-span-2">After-Sales Action<textarea name="after_sales_action" rows="3" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('after_sales_action', $order->after_sales_action) }}</textarea></label>
                                <label class="text-sm font-black">After-Sales Resolved<input type="datetime-local" name="after_sales_resolved_at" value="{{ old('after_sales_resolved_at', $order->after_sales_resolved_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                <label class="text-sm font-black">Verified At<input type="datetime-local" name="verified_at" value="{{ old('verified_at', $order->verified_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                                @if ($admin->canAdmin('workflow.approve'))
                                    <label class="text-sm font-black">Phase Approval<select name="phase_approval_status" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option @selected(old('phase_approval_status', $order->phase_approval_status) === 'Pending Operations Approval')>Pending Operations Approval</option><option @selected(old('phase_approval_status', $order->phase_approval_status) === 'Approved')>Approved</option><option @selected(old('phase_approval_status', $order->phase_approval_status) === 'Returned for Critical Review')>Returned for Critical Review</option></select></label>
                                    <label class="text-sm font-black sm:col-span-2">Operations Comment<textarea name="phase_approval_comment" rows="3" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('phase_approval_comment', $order->phase_approval_comment) }}</textarea></label>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-slate-700">Internal Notes</p>
                        <textarea name="internal_notes" rows="5" class="mt-5 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('internal_notes', $order->internal_notes) }}</textarea>
                    </div>

                    <button class="w-full rounded-md bg-pink-600 px-5 py-4 text-sm font-black text-white transition hover:bg-pink-700">Save Workflow Update</button>
                </section>

                <aside class="space-y-6">
                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Frontend Order</p>
                        <div class="mt-5 space-y-3 text-sm">
                            <p><span class="font-bold text-slate-500">Client:</span> {{ $order->customer_name }}</p>
                            <p><span class="font-bold text-slate-500">Contact:</span> {{ $order->customer_phone }} · {{ $order->customer_email }}</p>
                            <p><span class="font-bold text-slate-500">Product:</span> {{ $order->product?->name ?? 'Custom order' }}</p>
                            <p><span class="font-bold text-slate-500">Channel:</span> {{ $order->channel ?? 'Online' }}</p>
                            <p><span class="font-bold text-slate-500">Qty:</span> {{ $order->quantity }}</p>
                            @if ($canViewAmounts)
                                <p><span class="font-bold text-slate-500">Total:</span> NGN {{ number_format($order->total_price, 2) }}</p>
                                <p><span class="font-bold text-slate-500">Amount Paid:</span> NGN {{ number_format((float) $order->amount_paid, 2) }}</p>
                            @endif
                            <p><span class="font-bold text-slate-500">Delivery:</span> {{ $order->delivery_address ?: 'Pending' }} {{ $order->delivery_city ? '· '.$order->delivery_city : '' }}</p>
                            <p><span class="font-bold text-slate-500">Phase Approval:</span> {{ $order->phase_approval_status ?? 'Pending Operations Approval' }}</p>
                            <p><span class="font-bold text-slate-500">Final Design:</span> {{ $order->final_design_path ? 'Uploaded' : 'Pending' }}</p>
                        </div>
                    </div>

                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Client Artwork Assets</p>
                        <div class="mt-5 space-y-3 text-sm">
                            @forelse (($order->job_image_assets ?? []) as $asset)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($asset['path']) }}" target="_blank" rel="noopener noreferrer" class="block rounded-md border border-slate-200 p-4 font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">{{ $asset['name'] ?? basename($asset['path']) }}</a>
                            @empty
                                <p class="rounded-md border border-dashed border-slate-300 p-5 text-sm text-slate-600">No client assets uploaded yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Workbook Permission</p>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Your role is <strong>{{ auth()->user()->role }}</strong>. Sections shown here match the SOP responsibility for that role.</p>
                    </div>

                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">SOP Phase Gates</p>
                        <div class="mt-5 space-y-5">
                            @foreach ($workflowPhases as $phase)
                                <div>
                                    <p class="font-black text-slate-950">{{ $phase['phase'] }}</p>
                                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{{ $phase['responsible'] }} · {{ $phase['status'] }}</p>
                                    <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                                        @foreach ($phase['gates'] as $gate)
                                            <li>• {{ $gate }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </form>
    </div>
@endsection
