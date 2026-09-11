@extends('layouts.admin')

@section('title', 'Large Format Calculator | Printbuka')

@section('content')
    <div
        class="mx-auto max-w-2xl"
        x-data="largeFormatCalculator(@js($rates->map(fn ($rate) => ['id' => $rate->id, 'material' => $rate->material, 'rate' => (float) $rate->rate_per_sqft])))"
    >
        <a href="{{ route('admin.pricelist.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Pricelist</a>

        <div class="pb-page-header mt-3">
            <div>
                <h1 class="pb-page-title">Large Format Calculator</h1>
                <p class="pb-page-subtitle max-w-2xl">Pick a material, enter the size, and get an instant quote — updates as you type.</p>
            </div>
        </div>

        @if ($rates->isEmpty())
            <div class="pb-alert pb-alert-warning">
                No large-format materials configured yet. Ask a super admin, operations manager, or MD to set rates first.
            </div>
        @else
            <div class="pb-card p-6">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label">Material</label>
                        <select x-model.number="materialId" class="pb-select w-full">
                            <template x-for="material in materials" :key="material.id">
                                <option :value="material.id" x-text="material.material + ' (₦' + material.rate.toLocaleString() + ' / sqft)'"></option>
                            </template>
                        </select>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Width</label>
                        <input type="number" step="0.01" min="0" x-model.number="width"
                            class="pb-input w-full">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Height</label>
                        <input type="number" step="0.01" min="0" x-model.number="height"
                            class="pb-input w-full">
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Unit</label>
                        <div class="flex gap-2">
                            <button type="button" @click="unit = 'ft'" :class="unit === 'ft' ? 'pb-btn-primary' : 'pb-btn-outline'" class="pb-btn pb-btn-md flex-1">Feet</button>
                            <button type="button" @click="unit = 'in'" :class="unit === 'in' ? 'pb-btn-primary' : 'pb-btn-outline'" class="pb-btn pb-btn-md flex-1">Inches</button>
                        </div>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Quantity</label>
                        <input type="number" step="1" min="1" x-model.number="quantity"
                            class="pb-input w-full">
                    </div>
                </div>

                <div class="mt-6 rounded-2xl bg-slate-950 p-6 text-center">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">Estimated price</p>
                    <p class="mt-2 text-4xl font-black text-white" x-text="'₦' + price.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></p>
                    <p class="mt-2 text-xs text-slate-400" x-text="breakdown"></p>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('largeFormatCalculator', (materials) => ({
            materials,
            materialId: materials[0]?.id ?? null,
            width: 0,
            height: 0,
            unit: 'ft',
            quantity: 1,

            get selectedMaterial() {
                return this.materials.find((m) => m.id === this.materialId) ?? null;
            },

            get price() {
                if (!this.selectedMaterial) return 0;
                const areaSqft = (Number(this.width) * Number(this.height)) / (this.unit === 'in' ? 144 : 1);
                return Math.round(areaSqft * this.selectedMaterial.rate * Math.max(1, Number(this.quantity) || 1) * 100) / 100;
            },

            get breakdown() {
                if (!this.selectedMaterial) return '';
                const unitLabel = this.unit === 'in' ? 'in' : 'ft';
                return `${this.width}${unitLabel} × ${this.height}${unitLabel} × ₦${this.selectedMaterial.rate}/sqft × ${this.quantity} qty`;
            },
        }));
    });
</script>
@endpush
