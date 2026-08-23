@extends('layouts.admin')

@section('title', 'Large Format Calculator | Printbuka')

@section('content')
    <div
        class="mx-auto max-w-2xl"
        x-data="largeFormatCalculator(@js($rates->map(fn ($rate) => ['id' => $rate->id, 'material' => $rate->material, 'rate' => (float) $rate->rate_per_sqft])))"
    >
        <a href="{{ route('admin.pricelist.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Pricelist</a>

        <div class="mt-3">
            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Pricelist</p>
            <h1 class="mt-2 text-4xl text-slate-950">Large format calculator.</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-500">Pick a material, enter the size, and get an instant quote — updates as you type.</p>
        </div>

        @if ($rates->isEmpty())
            <div class="mt-8 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800">
                No large-format materials configured yet. Ask a super admin, operations manager, or MD to set rates first.
            </div>
        @else
            <div class="mt-8 rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="space-y-1 sm:col-span-2">
                        <label class="text-sm font-black text-slate-700">Material</label>
                        <select x-model.number="materialId" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <template x-for="material in materials" :key="material.id">
                                <option :value="material.id" x-text="material.material + ' (₦' + material.rate.toLocaleString() + ' / sqft)'"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-black text-slate-700">Width</label>
                        <input type="number" step="0.01" min="0" x-model.number="width"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-black text-slate-700">Height</label>
                        <input type="number" step="0.01" min="0" x-model.number="height"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-black text-slate-700">Unit</label>
                        <div class="flex gap-2">
                            <button type="button" @click="unit = 'ft'" :class="unit === 'ft' ? 'bg-pink-600 text-white' : 'bg-white text-slate-700 border border-slate-300'" class="flex-1 rounded-lg px-4 py-3 text-sm font-black transition">Feet</button>
                            <button type="button" @click="unit = 'in'" :class="unit === 'in' ? 'bg-pink-600 text-white' : 'bg-white text-slate-700 border border-slate-300'" class="flex-1 rounded-lg px-4 py-3 text-sm font-black transition">Inches</button>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-black text-slate-700">Quantity</label>
                        <input type="number" step="1" min="1" x-model.number="quantity"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
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
