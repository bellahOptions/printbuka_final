@extends('layouts.admin')

@section('title', 'Newsletter Campaigns | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-pink-700">Customer Marketing</p>
                    <h1 class="mt-1 text-4xl font-black text-slate-950">Newsletter Campaigns</h1>
                    <p class="mt-2 text-sm font-semibold text-slate-500">
                        Send marketing emails to verified and active registered customers.
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-right">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Current Audience</p>
                        <p class="text-2xl font-black text-slate-950">{{ number_format($audienceCount) }}</p>
                    </div>
                    <a href="{{ route('admin.newsletters.create') }}" class="rounded-xl bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">
                        + New Newsletter
                    </a>
                </div>
            </div>

            @if (session('status'))
                <p class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                    {{ session('status') }}
                </p>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-slate-950">Recent Campaigns</h2>
            <div class="mt-5 overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Sent By</th>
                            <th class="px-4 py-3">Recipients</th>
                            <th class="px-4 py-3">Delivered</th>
                            <th class="px-4 py-3">Failed</th>
                            <th class="px-4 py-3">Sent At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($campaigns as $campaign)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-black text-slate-900">{{ $campaign->subject }}</p>
                                    @if ($campaign->preheader)
                                        <p class="text-xs font-semibold text-slate-500">{{ $campaign->preheader }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ $campaign->sender?->displayName() ?? 'System' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ number_format($campaign->recipient_count) }}</td>
                                <td class="px-4 py-3 font-semibold text-emerald-700">{{ number_format($campaign->emails_sent) }}</td>
                                <td class="px-4 py-3 font-semibold text-pink-700">{{ number_format($campaign->emails_failed) }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-600">{{ $campaign->sent_at?->format('M j, Y g:i A') ?? 'Pending' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">
                                    No newsletter campaign has been sent yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $campaigns->links() }}</div>
        </section>
    </div>
@endsection
