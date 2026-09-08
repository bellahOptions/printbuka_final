@php
    $badgeClass = function (string $status): string {
        return match ($status) {
            \App\Models\Training::STATUS_ACCEPTED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            \App\Models\Training::STATUS_REJECTED => 'bg-pink-50 text-pink-700 border-pink-200',
            default => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    };
@endphp

<div class="space-y-4">
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[980px] text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-4">Applicant</th>
                    <th class="px-5 py-4">Skill</th>
                    <th class="px-5 py-4">Location</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Submitted</th>
                    <th class="px-5 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($applications as $application)
                    <tr wire:key="training-row-{{ $application->id }}" class="table-row-hover">
                        <td class="px-5 py-4">
                            <p class="font-black text-slate-950">{{ $application->fullName() }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $application->email }} · {{ $application->phone_whatsapp }}</p>
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-700">{{ $application->desired_skill }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $application->city_state }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full border px-3 py-1 text-xs font-black uppercase {{ $badgeClass($application->status) }}">{{ $application->statusLabel() }}</span>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $application->created_at?->format('M j, Y g:i A') }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.training.show', $application) }}" class="font-black text-pink-700 hover:text-pink-800">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm font-semibold text-slate-500">No training applications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs font-bold text-slate-400">
        Showing {{ number_format($applications->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('application', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more applications as you scroll...
            </span>
            <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more applications...
            </span>
            <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                Load More
            </button>
        </div>
    @endif
</div>
