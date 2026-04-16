<div class="mt-3 rounded-md border border-dashed border-slate-300 bg-slate-50 p-3">
    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Create category on the go</p>
    <div class="mt-2 grid gap-2 sm:grid-cols-[1fr_1fr_auto]">
        <label class="text-xs font-black text-slate-700">
            Name
            <input
                type="text"
                wire:model.live="name"
                placeholder="e.g. Flyers"
                class="mt-1 min-h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold"
            >
        </label>
        <label class="text-xs font-black text-slate-700">
            Tag (optional)
            <input
                type="text"
                wire:model.live="tag"
                placeholder="Promo"
                class="mt-1 min-h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold"
            >
        </label>
        <button
            type="button"
            wire:click="createCategory"
            wire:loading.attr="disabled"
            class="min-h-10 rounded-md bg-slate-900 px-4 text-xs font-black uppercase tracking-wide text-white transition hover:bg-slate-800 sm:self-end"
        >
            <span wire:loading.remove wire:target="createCategory">Add</span>
            <span wire:loading wire:target="createCategory">Saving...</span>
        </button>
    </div>
    @error('name')
        <p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>
    @enderror
    @error('tag')
        <p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>
    @enderror
    @if ($statusMessage)
        <p class="mt-2 text-xs font-bold text-emerald-700">{{ $statusMessage }}</p>
    @endif
</div>
