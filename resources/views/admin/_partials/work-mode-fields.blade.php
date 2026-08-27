{{-- Shared radio/checkbox fields for declaring a work arrangement. Expects an
     optional $profile (StaffProfile) variable to pre-fill the current value;
     used by both the first-time prompt modal and the attendance page's
     "change my work arrangement" settings block. --}}
@php($currentMode = $profile?->work_mode)
@php($currentDays = $profile?->onsite_days ?? [])

<div class="space-y-2">
    @foreach (['onsite' => ['Onsite', 'I work from the office every working day.'], 'hybrid' => ['Hybrid', 'I split my time between the office and remote.'], 'remote' => ['Fully Remote', 'I never need to be at the office.']] as $value => [$label, $description])
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3 cursor-pointer has-[:checked]:border-pink-400 has-[:checked]:bg-pink-50">
            <input type="radio" name="work_mode" value="{{ $value }}" @checked($currentMode === $value) class="mt-1 text-pink-600 focus:ring-pink-500" data-work-mode-option required>
            <span>
                <span class="block text-sm font-black text-slate-900">{{ $label }}</span>
                <span class="block text-xs text-slate-500">{{ $description }}</span>
            </span>
        </label>
    @endforeach
</div>

<div class="{{ $currentMode === 'hybrid' ? '' : 'hidden' }}" data-onsite-days-wrap>
    <span class="pb-label">Which days are you onsite?</span>
    <div class="mt-2 grid grid-cols-3 gap-2 sm:grid-cols-6">
        @foreach (\App\Models\StaffProfile::WEEKDAYS as $day)
            <label class="flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 px-2 py-2 text-xs font-bold cursor-pointer has-[:checked]:border-pink-400 has-[:checked]:bg-pink-50 has-[:checked]:text-pink-700">
                <input type="checkbox" name="onsite_days[]" value="{{ $day }}" @checked(in_array($day, $currentDays, true)) class="sr-only">
                {{ $day }}
            </label>
        @endforeach
    </div>
    <p class="mt-1.5 text-xs text-slate-400">Any other working day is treated as a remote day for you.</p>
</div>

@error('work_mode')
    <p class="pb-field-error">{{ $message }}</p>
@enderror
@error('onsite_days')
    <p class="pb-field-error">{{ $message }}</p>
@enderror
