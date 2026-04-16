<div class="mt-3 rounded-md border border-dashed border-slate-300 bg-slate-50 p-3">
    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Add new customer</p>
    <div class="mt-2 grid gap-2 sm:grid-cols-2">
        <label class="text-xs font-black text-slate-700">
            First Name
            <input
                type="text"
                wire:model.live="first_name"
                placeholder="First name"
                class="mt-1 min-h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold"
            >
        </label>
        <label class="text-xs font-black text-slate-700">
            Last Name
            <input
                type="text"
                wire:model.live="last_name"
                placeholder="Last name"
                class="mt-1 min-h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold"
            >
        </label>
        <label class="text-xs font-black text-slate-700">
            Email
            <input
                type="email"
                wire:model.live="email"
                placeholder="customer@email.com"
                class="mt-1 min-h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold"
            >
        </label>
        <label class="text-xs font-black text-slate-700">
            Phone
            <input
                type="text"
                wire:model.live="phone"
                placeholder="08012345678"
                class="mt-1 min-h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold"
            >
        </label>
        <label class="text-xs font-black text-slate-700 sm:col-span-2">
            Company Name
            <input
                type="text"
                wire:model.live="companyName"
                placeholder="Customer company"
                class="mt-1 min-h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold"
            >
        </label>
        <button
            type="button"
            wire:click="createCustomer"
            wire:loading.attr="disabled"
            class="min-h-10 rounded-md bg-slate-900 px-4 text-xs font-black uppercase tracking-wide text-white transition hover:bg-slate-800 sm:col-span-2 sm:justify-self-start"
        >
            <span wire:loading.remove wire:target="createCustomer">Create Customer</span>
            <span wire:loading wire:target="createCustomer">Saving...</span>
        </button>
    </div>

    @foreach (['first_name', 'last_name', 'email', 'phone', 'companyName'] as $field)
        @error($field)
            <p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>
        @enderror
    @endforeach

    @if ($statusMessage)
        <p class="mt-2 text-xs font-bold text-emerald-700">{{ $statusMessage }}</p>
    @endif
</div>

