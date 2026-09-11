<div class="mt-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-3">
    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Create category on the go</p>
    <div class="mt-2 grid gap-2 sm:grid-cols-[1fr_1fr_auto]">
        <div class="pb-field">
            <label class="pb-label">Name</label>
            <input
                type="text"
                wire:model.live="name"
                placeholder="e.g. Flyers"
                class="pb-input w-full"
            >
        </div>
        <div class="pb-field">
            <label class="pb-label">Tag (optional)</label>
            <input
                type="text"
                wire:model.live="tag"
                placeholder="Promo"
                class="pb-input w-full"
            >
        </div>
        <button
            type="button"
            wire:click="createCategory"
            wire:loading.attr="disabled"
            class="pb-btn pb-btn-md pb-btn-ink sm:self-end"
        >
            <span wire:loading.remove wire:target="createCategory">Add</span>
            <span wire:loading wire:target="createCategory">Saving...</span>
        </button>
    </div>
    @error('name')
        <p class="pb-field-error">{{ $message }}</p>
    @enderror
    @error('tag')
        <p class="pb-field-error">{{ $message }}</p>
    @enderror
    @if ($statusMessage)
        <p class="mt-2 text-xs font-bold text-emerald-700">{{ $statusMessage }}</p>
    @endif
</div>
