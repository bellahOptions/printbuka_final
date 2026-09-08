<div>
    {{-- Filters --}}
    <div class="pb-card p-4 mb-6">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[220px]">
                <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Reference, name or email…"
                       class="input input-bordered border-slate-200 input-sm w-full" />
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Payment</label>
                <select wire:model.live="payment" class="select select-bordered border-slate-200 select-sm">
                    <option value="">All Payments</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Order Status</label>
                <select wire:model.live="status" class="select select-bordered border-slate-200 select-sm">
                    <option value="">All Statuses</option>
                    <option value="order_received">Order Received</option>
                    <option value="processing">Processing</option>
                    <option value="dispatched">Dispatched</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>
            @if ($search !== '' || $status !== '' || $payment !== '')
                <button type="button" wire:click="clearFilters" class="btn btn-sm btn-ghost font-black text-slate-400">
                    <x-heroicon-o-x-mark class="w-4 h-4" /> Clear
                </button>
            @endif
        </div>
    </div>

    <div class="pb-card overflow-hidden">
        @if ($orders->isEmpty())
            <div class="py-16 text-center">
                <x-heroicon-o-shopping-bag class="w-12 h-12 text-slate-200 mx-auto mb-3" />
                <p class="font-black text-slate-700">No orders found.</p>
                <p class="text-sm text-slate-400 mt-1">Try adjusting your filters.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="pb-table pb-table--cards">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th class="hidden sm:table-cell">Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Order Status</th>
                            <th class="hidden md:table-cell">Date</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                        @php
                            $fulfillConfig = match($order->fulfillment_status) {
                                'delivered'      => ['class' => 'pb-badge-success',  'label' => 'Delivered',      'dot' => 'bg-emerald-500'],
                                'dispatched'     => ['class' => 'pb-badge-info',     'label' => 'Dispatched',     'dot' => 'bg-violet-500'],
                                'processing'     => ['class' => 'pb-badge-warning',  'label' => 'Processing',     'dot' => 'bg-amber-500'],
                                'order_received' => ['class' => 'pb-badge-neutral',  'label' => 'Order Received', 'dot' => 'bg-sky-400'],
                                default          => ['class' => 'pb-badge-neutral',  'label' => ucfirst($order->fulfillment_status), 'dot' => 'bg-slate-400'],
                            };
                            $payConfig = match($order->payment_status) {
                                'paid'   => ['class' => 'pb-badge-success', 'label' => 'Paid'],
                                'failed' => ['class' => 'pb-badge-danger',  'label' => 'Failed'],
                                default  => ['class' => 'pb-badge-warning', 'label' => 'Pending'],
                            };
                        @endphp
                            <tr wire:key="shop-order-row-{{ $order->id }}" class="hover:bg-slate-50/60 transition-colors">
                                <td data-label="Reference">
                                    <a href="{{ route('admin.shop-orders.show', $order) }}"
                                       class="font-mono font-black text-slate-900 hover:text-pink-600 text-sm transition-colors">{{ $order->reference }}</a>
                                </td>
                                <td data-label="Customer">
                                    <p class="font-black text-slate-900 text-sm leading-tight">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $order->customer_email }}</p>
                                </td>
                                <td data-label="Items" class="hidden sm:table-cell">
                                    <span class="pb-badge-neutral text-xs">{{ $order->items_count }} item{{ $order->items_count !== 1 ? 's' : '' }}</span>
                                </td>
                                <td data-label="Total">
                                    <p class="font-black text-slate-900 text-sm">₦{{ number_format((float) $order->total, 0) }}</p>
                                </td>
                                <td data-label="Payment">
                                    <span class="{{ $payConfig['class'] }} text-xs">{{ $payConfig['label'] }}</span>
                                </td>
                                <td data-label="Order Status">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $fulfillConfig['dot'] }}"></span>
                                        <span class="{{ $fulfillConfig['class'] }} text-xs">{{ $fulfillConfig['label'] }}</span>
                                    </div>
                                </td>
                                <td data-label="Date" class="hidden md:table-cell">
                                    <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-slate-400">{{ $order->created_at->format('H:i') }}</p>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.shop-orders.show', $order) }}"
                                       class="btn btn-xs btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">
                                        View →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 space-y-3">
                <p class="text-xs font-bold text-slate-400">
                    Showing {{ number_format($orders->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('order', $totalCount) }}
                </p>

                @if ($hasMore)
                    <div class="flex flex-col items-center gap-3 py-2" wire:poll.visible="loadMore">
                        <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                            Loading more orders as you scroll...
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                            Loading more orders...
                        </span>
                        <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                            Load More
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
