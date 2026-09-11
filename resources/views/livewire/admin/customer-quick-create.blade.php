<div class="mt-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-3">
    <p class="pb-label mb-0">Add new customer</p>
    <div class="mt-2 grid gap-2 sm:grid-cols-2">
        <div class="pb-field">
            <label class="pb-label">First Name</label>
            <input
                type="text"
                wire:model.live="first_name"
                placeholder="First name"
                class="pb-input"
            >
        </div>
        <div class="pb-field">
            <label class="pb-label">Last Name</label>
            <input
                type="text"
                wire:model.live="last_name"
                placeholder="Last name"
                class="pb-input"
            >
        </div>
        <div class="pb-field">
            <label class="pb-label">Email</label>
            <input
                type="email"
                wire:model.live="email"
                placeholder="customer@email.com"
                class="pb-input"
            >
        </div>
        <div class="pb-field">
            <label class="pb-label">Phone</label>
            <input
                type="text"
                wire:model.live="phone"
                placeholder="08012345678"
                class="pb-input"
            >
        </div>
        <div class="pb-field sm:col-span-2">
            <label class="pb-label">Company Name</label>
            <input
                type="text"
                wire:model.live="companyName"
                placeholder="Customer company"
                class="pb-input"
            >
        </div>
        <button
            type="button"
            wire:click="createCustomer"
            wire:loading.attr="disabled"
            class="pb-btn pb-btn-md pb-btn-ink sm:col-span-2 sm:justify-self-start"
        >
            <span wire:loading.remove wire:target="createCustomer">Create Customer</span>
            <span wire:loading wire:target="createCustomer">Saving...</span>
        </button>
    </div>

    @foreach (['first_name', 'last_name', 'email', 'phone', 'companyName'] as $field)
        @error($field)
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
    @endforeach

    @if ($statusMessage)
        <p class="mt-2 text-xs font-semibold text-emerald-700">{{ $statusMessage }}</p>
    @endif
</div>

