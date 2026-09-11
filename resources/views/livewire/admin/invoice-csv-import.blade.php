<div class="mt-6 pb-card p-5 border-cyan-200 bg-cyan-50/50">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <p class="pb-label text-cyan-700">Process & Technology Manager Import</p>
            <h2 class="pb-section-title mt-1">Upload customer invoices and quotes</h2>
            <p class="mt-2 text-sm leading-6 text-slate-700">CSV rows are grouped by invoice or quote number. Imported records are saved as paid invoice/quotation records and their jobs are marked delivered.</p>
        </div>
        <form wire:submit="import" class="w-full max-w-md space-y-3">
            <input type="file" wire:model="csvFile" accept=".csv,text/csv" required class="pb-input">
            <div wire:loading wire:target="csvFile" class="pb-label text-cyan-700">Uploading file...</div>
            <div wire:loading wire:target="import" class="pb-label text-cyan-700">Importing records...</div>
            @error('csvFile')
                <p class="pb-field-error">{{ $message }}</p>
            @enderror
            <button type="submit" wire:loading.attr="disabled" wire:target="import,csvFile" class="pb-btn pb-btn-md pb-btn-secondary disabled:cursor-not-allowed disabled:opacity-60">Import CSV</button>
        </form>
    </div>
</div>
