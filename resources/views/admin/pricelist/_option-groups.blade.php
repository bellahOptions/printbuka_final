{{-- Expects $optionLines: array<string,string> keyed by size|material|finish|density|delivery --}}
<div class="pb-card p-6">
    <h2 class="pb-section-title">Option pricing</h2>
    <div class="mt-2 pb-alert pb-alert-info">
        One option per line: <strong>Label|Extra price</strong>. Example: A3|5000
    </div>

    <div class="mt-5 grid gap-5 sm:grid-cols-2">
        <div class="pb-field">
            <label class="pb-label">Size / Format Options</label>
            <textarea name="size_options" rows="6" class="pb-textarea font-mono" placeholder="A4|0&#10;A3|5000">{{ old('size_options', $optionLines['size'] ?? '') }}</textarea>
        </div>
        <div class="pb-field">
            <label class="pb-label">Material Type Options</label>
            <textarea name="material_options" rows="6" class="pb-textarea font-mono" placeholder="Art Card 300gsm|0&#10;PVC|2500">{{ old('material_options', $optionLines['material'] ?? '') }}</textarea>
        </div>
        <div class="pb-field">
            <label class="pb-label">Finish / Lamination Options</label>
            <textarea name="finish_options" rows="6" class="pb-textarea font-mono" placeholder="No Finish|0&#10;Gloss Lamination|1500">{{ old('finish_options', $optionLines['finish'] ?? '') }}</textarea>
        </div>
        <div class="pb-field">
            <label class="pb-label">Paper Density Options</label>
            <textarea name="density_options" rows="6" class="pb-textarea font-mono" placeholder="300gsm|0&#10;350gsm|1200">{{ old('density_options', $optionLines['density'] ?? '') }}</textarea>
        </div>
        <div class="pb-field sm:col-span-2">
            <label class="pb-label">Delivery Options</label>
            <textarea name="delivery_options" rows="4" class="pb-textarea font-mono" placeholder="Client Pickup|0&#10;Dispatch Rider|3000">{{ old('delivery_options', $optionLines['delivery'] ?? '') }}</textarea>
        </div>
    </div>
</div>
