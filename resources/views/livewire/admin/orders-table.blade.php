<section class="mt-8 space-y-4">
    @if (session('status'))
        <p class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
    @endif
    @if (session('warning'))
        <p class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">{{ session('warning') }}</p>
    @endif

    <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <label class="w-full max-w-xl">
                <span class="text-xs font-black uppercase tracking-wide text-slate-500">Search orders</span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Order no, invoice no, customer, status..."
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold"
                >
            </label>

            <div class="w-full max-w-md space-y-2">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                    <label class="grow">
                        <span class="text-xs font-black uppercase tracking-wide text-slate-500">Batch action</span>
                        <select wire:model.live="batchAction" class="mt-2 h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                            <option value="">Select action</option>
                            <option value="priority_urgent">Set Priority: Urgent</option>
                            <option value="priority_normal">Set Priority: Normal</option>
                            @if ($canApproveWorkflow)
                                <option value="status">Set Workflow Status</option>
                            @endif
                            @if ($canManageInvoices)
                                <option value="payment_status">Set Payment Status</option>
                            @endif
                        </select>
                    </label>
                    <button
                        type="button"
                        wire:click="applyBatchAction"
                        class="h-11 rounded-md bg-slate-900 px-4 text-sm font-black text-white transition hover:bg-pink-700"
                    >
                        Apply
                    </button>
                </div>

                @if ($batchAction === 'status')
                    <label class="block">
                        <span class="text-xs font-black uppercase tracking-wide text-slate-500">Target status</span>
                        <select wire:model.live="targetStatus" class="mt-2 h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                            <option value="">Choose status</option>
                            @foreach ($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                @if ($batchAction === 'payment_status')
                    <label class="block">
                        <span class="text-xs font-black uppercase tracking-wide text-slate-500">Target payment status</span>
                        <select wire:model.live="targetPaymentStatus" class="mt-2 h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                            <option value="">Choose payment status</option>
                            @foreach ($paymentStatusOptions as $paymentStatusOption)
                                <option value="{{ $paymentStatusOption }}">{{ $paymentStatusOption }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif
            </div>
        </div>

        @error('batchAction')
            <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
        @error('selected')
            <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
        @error('targetStatus')
            <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
        @error('targetPaymentStatus')
            <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[1080px] text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                    @php
                        $pageIds = $orders->pluck('id')->map(fn ($id): int => (int) $id)->all();
                        $allPageSelected = $pageIds !== [] && count(array_diff($pageIds, $selected)) === 0;
                    @endphp
                    <th class="px-4 py-4">
                        <input type="checkbox" wire:click="toggleSelectPageSelection" class="h-4 w-4 rounded border-slate-300 text-pink-600" @checked($allPageSelected)>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('job_order_number')" class="inline-flex items-center gap-1">
                            Job Order / Invoice #
                            @if ($sortField === 'job_order_number')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('customer_name')" class="inline-flex items-center gap-1">
                            Client
                            @if ($sortField === 'customer_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('channel')" class="inline-flex items-center gap-1">
                            Channel
                            @if ($sortField === 'channel')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('priority')" class="inline-flex items-center gap-1">
                            Priority
                            @if ($sortField === 'priority')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('payment_status')" class="inline-flex items-center gap-1">
                            Payment Status
                            @if ($sortField === 'payment_status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('status')" class="inline-flex items-center gap-1">
                            Status
                            @if ($sortField === 'status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4 text-right">Manage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($orders as $order)
                    <tr wire:key="order-row-{{ $order->id }}">
                        <td class="px-4 py-4">
                            <input type="checkbox" value="{{ $order->id }}" wire:model.live="selected" class="h-4 w-4 rounded border-slate-300 text-pink-600">
                        </td>
                        <td class="px-5 py-4">
                            <span class="block font-black text-slate-900">{{ $order->job_order_number ?? $order->displayNumber() }}</span>
                            <span class="text-xs font-semibold text-slate-500">{{ $order->invoice?->invoice_number ?? 'Invoice Pending' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="block font-bold">{{ $order->customer_name }}</span>
                            <span class="text-xs text-slate-500">{{ $order->customer_phone }} · {{ $order->customer_email }}</span>
                        </td>
                        <td class="px-5 py-4">{{ $order->channel ?? 'Online' }}</td>
                        <td class="px-5 py-4">{{ $order->priority ?: '—' }}</td>
                        <td class="px-5 py-4">{{ $order->payment_status ?: 'Pending' }}</td>
                        <td class="px-5 py-4">{{ $order->status }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-black text-pink-700 hover:text-pink-800">Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-slate-500">No jobs matched your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $orders->links() }}</div>
</section>
