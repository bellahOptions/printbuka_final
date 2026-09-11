<section class="mt-8 space-y-4">
    @if (session('status'))
        <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
    @endif
    @if (session('warning'))
        <div class="pb-alert pb-alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="pb-card p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div class="pb-field w-full max-w-xl">
                <label class="pb-label">Search orders</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Order no, invoice no, customer, status..."
                    class="pb-input"
                >
            </div>

            <div class="w-full max-w-md space-y-2">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                    <div class="pb-field grow">
                        <label class="pb-label">Batch action</label>
                        <select wire:model.live="batchAction" class="pb-input">
                            <option value="">Select action</option>
                            <option value="priority_urgent">Set Priority: Urgent</option>
                            <option value="priority_normal">Set Priority: Normal</option>
                            @if ($canApproveWorkflow)
                                <option value="status">Set Workflow Status</option>
                            @endif
                            @if ($canManageInvoices)
                                <option value="payment_status">Set Payment Status</option>
                            @endif
                            @if ($canConcludeJob)
                                <option value="conclude">Conclude Job(s)</option>
                            @endif
                        </select>
                    </div>
                    <button
                        type="button"
                        wire:click="applyBatchAction"
                        @if ($batchAction === 'conclude')
                            wire:confirm="Conclude the selected job(s)? This locks them from further edits, auto-settles their invoice, and emails the client and staff. This cannot be undone."
                        @endif
                        class="pb-btn pb-btn-md pb-btn-ink"
                    >
                        Apply
                    </button>
                </div>

                @if ($batchAction === 'status')
                    <div class="pb-field">
                        <label class="pb-label">Target status</label>
                        <select wire:model.live="targetStatus" class="pb-input">
                            <option value="">Choose status</option>
                            @foreach ($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($batchAction === 'payment_status')
                    <div class="pb-field">
                        <label class="pb-label">Target payment status</label>
                        <select wire:model.live="targetPaymentStatus" class="pb-input">
                            <option value="">Choose payment status</option>
                            @foreach ($paymentStatusOptions as $paymentStatusOption)
                                <option value="{{ $paymentStatusOption }}">{{ $paymentStatusOption }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>

        @error('batchAction')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
        @error('selected')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
        @error('targetStatus')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
        @error('targetPaymentStatus')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="pb-table-wrapper">
        <table class="pb-table min-w-[1080px]">
            <thead>
                <tr>
                    @php
                        $loadedIds = $orders->pluck('id')->map(fn ($id): int => (int) $id)->all();
                        $allLoadedSelected = $loadedIds !== [] && count(array_diff($loadedIds, $selected)) === 0;
                    @endphp
                    <th>
                        <input type="checkbox" wire:click="toggleSelectLoadedSelection" class="h-4 w-4 rounded border-slate-300 text-pink-600" @checked($allLoadedSelected)>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('job_order_number')" class="inline-flex items-center gap-1">
                            Job Order / Invoice #
                            @if ($sortField === 'job_order_number')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('customer_name')" class="inline-flex items-center gap-1">
                            Client
                            @if ($sortField === 'customer_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('channel')" class="inline-flex items-center gap-1">
                            Channel
                            @if ($sortField === 'channel')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('priority')" class="inline-flex items-center gap-1">
                            Priority
                            @if ($sortField === 'priority')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('payment_status')" class="inline-flex items-center gap-1">
                            Payment Status
                            @if ($sortField === 'payment_status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('status')" class="inline-flex items-center gap-1">
                            Status
                            @if ($sortField === 'status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="text-right">Manage</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr wire:key="order-row-{{ $order->id }}">
                        <td data-label="">
                            <input type="checkbox" value="{{ $order->id }}" wire:model.live="selected" class="h-4 w-4 rounded border-slate-300 text-pink-600">
                        </td>
                        <td data-label="Job Order / Invoice #">
                            <span class="block font-semibold text-slate-900">{{ $order->job_order_number ?? $order->displayNumber() }}</span>
                            <span class="text-xs font-semibold text-slate-500">{{ $order->invoice?->invoice_number ?? 'Invoice Pending' }}</span>
                        </td>
                        <td data-label="Client">
                            <span class="block font-bold text-slate-900">{{ $order->customer_name }}</span>
                            <span class="text-xs text-slate-500">{{ $order->customer_phone }} · {{ $order->customer_email }}</span>
                        </td>
                        <td data-label="Channel">{{ $order->channel ?? 'Online' }}</td>
                        <td data-label="Priority">
                            <span class="pb-badge {{ $order->priorityBadgeClass() }}">{{ $order->priorityLabel() }}</span>
                        </td>
                        <td data-label="Payment Status">
                            <span class="pb-badge {{ $order->paymentStatusBadgeClass() }}">{{ $order->payment_status ?: 'Pending' }}</span>
                        </td>
                        <td data-label="Status">
                            <span class="pb-badge {{ $order->statusBadgeClass() }}">{{ $order->status }}</span>
                        </td>
                        <td data-label="" class="text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-pink-700 hover:text-pink-800">Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="pb-empty">
                                <p class="pb-empty-title">No jobs matched your search.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs font-semibold text-slate-400">
        Showing {{ number_format($orders->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('job', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more jobs as you scroll...
            </span>
            <span class="text-xs font-semibold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more jobs...
            </span>
            <button type="button" wire:click="loadMore" class="pb-btn pb-btn-md pb-btn-outline">
                Load More
            </button>
        </div>
    @endif
</section>
