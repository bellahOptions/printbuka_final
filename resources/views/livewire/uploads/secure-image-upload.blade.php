<div class="space-y-3">
    @if($multiple)
        {{-- ── MULTIPLE FILE UPLOAD ─────────────────────────── --}}

        {{-- Upload zone --}}
        <label class="pb-upload-zone block cursor-pointer"
               aria-label="Click to select images or drop files here">
            <input
                type="file"
                wire:model="uploads"
                multiple
                accept="{{ $accept ?: '.jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp' }}"
                class="sr-only"
                @error('uploads') aria-invalid="true" @enderror
                @error('uploads.*') aria-invalid="true" @enderror
            >
            {{-- Loading state --}}
            <div wire:loading wire:target="uploads"
                 class="flex flex-col items-center gap-2 pointer-events-none">
                <svg class="h-6 w-6 text-brand-500 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span class="text-xs font-medium text-brand-600">Uploading…</span>
            </div>
            {{-- Idle state --}}
            <div wire:loading.remove wire:target="uploads"
                 class="flex flex-col items-center gap-2 pointer-events-none">
                <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-slate-600">
                    Click or drag images here
                </p>
                <p class="text-xs text-slate-400">
                    JPG, PNG or WebP · max {{ number_format($maxSizeKb / 1024, 1) }} MB each · up to {{ $maxFiles }} file{{ $maxFiles !== 1 ? 's' : '' }}
                </p>
            </div>
        </label>

        {{-- Hidden inputs --}}
        @foreach($storedPaths as $path)
            <input type="hidden" name="{{ $inputName }}[]" value="{{ $path }}">
        @endforeach

        {{-- Preview grid --}}
        @if($storedPaths !== [])
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 lg:grid-cols-5">
                @foreach($storedPaths as $path)
                    @php($url = $this->imageUrl($path))
                    <div class="pb-upload-preview group aspect-square">
                        @if($url)
                            <img src="{{ $url }}" alt="Preview" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-slate-100">
                                <svg class="h-6 w-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <button
                            type="button"
                            wire:click="removePath('{{ base64_encode($path) }}')"
                            class="pb-upload-remove opacity-0 group-hover:opacity-100"
                            aria-label="Remove image"
                        >
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>

            <p class="text-xs text-slate-400">
                {{ count($storedPaths) }}/{{ $maxFiles }} image{{ count($storedPaths) !== 1 ? 's' : '' }} selected
            </p>
        @endif

        @error('uploads')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
        @error('uploads.*')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror

    @else
        {{-- ── SINGLE FILE UPLOAD ──────────────────────────── --}}

        @if(!$storedPath)
            {{-- Upload zone --}}
            <label class="pb-upload-zone block cursor-pointer"
                   aria-label="Click to select an image">
                <input
                    type="file"
                    wire:model="upload"
                    accept="{{ $accept ?: '.jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp' }}"
                    class="sr-only"
                    @error('upload') aria-invalid="true" @enderror
                >
                <div wire:loading wire:target="upload"
                     class="flex flex-col items-center gap-2 pointer-events-none">
                    <svg class="h-6 w-6 text-brand-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="text-xs font-medium text-brand-600">Uploading…</span>
                </div>
                <div wire:loading.remove wire:target="upload"
                     class="flex flex-col items-center gap-2 pointer-events-none">
                    <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm font-medium text-slate-600">Click or drag image here</p>
                    <p class="text-xs text-slate-400">
                        JPG, PNG or WebP · max {{ number_format($maxSizeKb / 1024, 1) }} MB
                        @if($minWidth || $minHeight)
                            · min {{ $minWidth }}×{{ $minHeight }}px
                        @endif
                    </p>
                </div>
            </label>
        @endif

        <input type="hidden" name="{{ $inputName }}" value="{{ $storedPath ?? '' }}">

        @if($storedPath)
            @php($url = $this->imageUrl($storedPath))
            <div class="flex items-center gap-3">
                <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                    @if($url)
                        <img src="{{ $url }}" alt="" class="block h-full w-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                        <div class="hidden h-full w-full items-center justify-center">
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16M14 14l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @else
                        <div class="flex h-full w-full items-center justify-center">
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16M14 14l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex flex-col gap-2">
                    <p class="flex items-center gap-1 text-xs font-medium text-emerald-700">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Image uploaded
                    </p>
                    <button
                        type="button"
                        wire:click="clearSingle"
                        class="pb-btn pb-btn-sm pb-btn-outline text-xs text-red-600 border-red-200 hover:bg-red-50 hover:border-red-400"
                    >
                        Remove
                    </button>
                </div>
            </div>
        @endif

        @error('upload')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
    @endif
</div>
