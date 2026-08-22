@extends('layouts.admin')
@section('title', 'Internal Memos | Printbuka')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Internal Memos</h1>
            <p class="text-sm text-slate-500 mt-1">Compose and send memos to staff, and review what's already gone out.</p>
        </div>
        <a href="{{ route('admin.memos.create') }}" class="pb-btn pb-btn-primary">+ New Memo</a>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3.5 text-left">Subject</th>
                    <th class="px-5 py-3.5 text-left">Sent</th>
                    <th class="px-5 py-3.5 text-left">Delivery</th>
                    <th class="px-5 py-3.5 text-left">By</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($memos as $memo)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-5 py-4 font-black text-slate-900">{{ $memo->subject }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $memo->sent_at?->format('M j, Y g:i A') ?? '—' }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $memo->emails_sent }} sent{{ $memo->emails_failed > 0 ? ', '.$memo->emails_failed.' failed' : '' }} / {{ $memo->recipient_count }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $memo->sentBy?->displayName() }}</td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.memos.show', $memo) }}" class="text-sm font-black text-slate-700 hover:text-pink-600">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400 font-semibold">No memos sent yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($memos->hasPages())
        <div class="px-5 py-4 border-t border-slate-200">{{ $memos->links() }}</div>
        @endif
    </div>

</div>
@endsection
