{{-- Expects $optionLines: array<string,string> keyed by size|material|finish|density|delivery --}}
<div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-black text-slate-950">Option pricing</h2>
    <div class="mt-2 rounded-xl border border-blue-100 bg-blue-50 p-3 text-sm text-blue-800">
        One option per line: <strong>Label|Extra price</strong>. Example: A3|5000
    </div>

    <div class="mt-5 grid gap-5 sm:grid-cols-2">
        <div class="space-y-1">
            <label class="text-sm font-black text-slate-700">Size / Format Options</label>
            <textarea name="size_options" rows="6" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 font-mono text-sm text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="A4|0&#10;A3|5000">{{ old('size_options', $optionLines['size'] ?? '') }}</textarea>
        </div>
        <div class="space-y-1">
            <label class="text-sm font-black text-slate-700">Material Type Options</label>
            <textarea name="material_options" rows="6" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 font-mono text-sm text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Art Card 300gsm|0&#10;PVC|2500">{{ old('material_options', $optionLines['material'] ?? '') }}</textarea>
        </div>
        <div class="space-y-1">
            <label class="text-sm font-black text-slate-700">Finish / Lamination Options</label>
            <textarea name="finish_options" rows="6" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 font-mono text-sm text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="No Finish|0&#10;Gloss Lamination|1500">{{ old('finish_options', $optionLines['finish'] ?? '') }}</textarea>
        </div>
        <div class="space-y-1">
            <label class="text-sm font-black text-slate-700">Paper Density Options</label>
            <textarea name="density_options" rows="6" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 font-mono text-sm text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="300gsm|0&#10;350gsm|1200">{{ old('density_options', $optionLines['density'] ?? '') }}</textarea>
        </div>
        <div class="space-y-1 sm:col-span-2">
            <label class="text-sm font-black text-slate-700">Delivery Options</label>
            <textarea name="delivery_options" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 font-mono text-sm text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Client Pickup|0&#10;Dispatch Rider|3000">{{ old('delivery_options', $optionLines['delivery'] ?? '') }}</textarea>
        </div>
    </div>
</div>
