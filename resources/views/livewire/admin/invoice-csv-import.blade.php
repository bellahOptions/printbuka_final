<div class="mt-6 rounded-md border border-cyan-200 bg-cyan-50 p-5">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Super Admin Import</p>
            <h2 class="mt-1 text-2xl font-black text-slate-950">Upload customer invoices and quotes</h2>
            <p class="mt-2 text-sm leading-6 text-slate-700">CSV rows are grouped by invoice or quote number. Imported records are saved as paid invoice/quotation records and their jobs are marked delivered.</p>
        </div>
        <form wire:submit="import" class="w-full max-w-md space-y-3">
            <input type="file" wire:model="csvFile" accept=".csv,text/csv" required class="w-full rounded-md border border-cyan-200 bg-white px-3 py-2 text-sm font-semibold text-slate-900">
            <div wire:loading wire:target="csvFile" class="text-xs font-black uppercase tracking-wide text-cyan-700">Uploading file...</div>
            <div wire:loading wire:target="import" class="text-xs font-black uppercase tracking-wide text-cyan-700">Importing records...</div>
            @error('csvFile')
                <p class="text-sm font-semibold text-red-700">{{ $message }}</p>
            @enderror
            <button type="submit" wire:loading.attr="disabled" wire:target="import,csvFile" class="rounded-md bg-cyan-700 px-5 py-3 text-sm font-black text-white transition hover:bg-cyan-800 disabled:cursor-not-allowed disabled:opacity-60">Import CSV</button>
        </form>
    </div>
</div>
