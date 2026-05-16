<div>
    <label for="email" class="text-sm font-black text-slate-800">Email address *</label>
    <div class="relative mt-2">
        <x-training.field-icon name="mail" />
        <input
            id="email"
            name="email"
            type="email"
            wire:model.live.debounce.500ms="email"
            value="{{ $email }}"
            class="min-h-12 w-full rounded-md border py-2 pl-11 pr-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 {{ $exists ? 'border-pink-400' : 'border-slate-200' }} @error('email') border-pink-400 @enderror"
            required
            data-email-helper="email-helper"
            data-email-exists="{{ $exists ? '1' : '0' }}"
        />
    </div>
    <p id="email-helper" class="mt-2 text-xs font-semibold {{ $exists ? 'text-pink-700' : 'text-slate-500' }}">
        {{ $exists ? 'This email has already submitted a PGTP application.' : 'Use an email you check often.' }}
    </p>
    @error('email') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
</div>
