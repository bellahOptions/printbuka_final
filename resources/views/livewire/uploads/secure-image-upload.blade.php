<div class="space-y-3">
    @if ($multiple)
        <input
            type="file"
            wire:model="uploads"
            multiple
            accept="{{ $accept ?: '.jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp' }}"
            class="file-input file-input-bordered w-full @error('uploads') file-input-error @enderror @error('uploads.*') file-input-error @enderror"
        >

        @foreach ($storedPaths as $path)
            <input type="hidden" name="{{ $inputName }}[]" value="{{ $path }}">
        @endforeach

        @if ($storedPaths !== [])
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                @foreach ($storedPaths as $path)
                    @php($url = $this->imageUrl($path))
                    <div class="relative rounded-md border border-slate-200 bg-white p-1">
                        @if ($url)
                            <img src="{{ $url }}" alt="Uploaded image preview" class="h-20 w-full rounded object-cover">
                        @else
                            <div class="flex h-20 items-center justify-center rounded bg-slate-100 text-[11px] font-bold text-slate-500">Missing</div>
                        @endif
                        <button
                            type="button"
                            wire:click="removePath('{{ base64_encode($path) }}')"
                            class="absolute right-1 top-1 rounded bg-slate-900/80 px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wide text-white transition hover:bg-pink-700"
                        >
                            Remove
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        @error('uploads')
            <p class="text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
        @error('uploads.*')
            <p class="text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
    @else
        <input
            type="file"
            wire:model="upload"
            accept="{{ $accept ?: '.jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp' }}"
            class="file-input file-input-bordered w-full @error('upload') file-input-error @enderror"
        >

        <input type="hidden" name="{{ $inputName }}" value="{{ $storedPath ?? '' }}">

        @if ($storedPath)
            @php($url = $this->imageUrl($storedPath))
            <div class="rounded-md border border-slate-200 bg-white p-2">
                @if ($url)
                    <img src="{{ $url }}" alt="Uploaded image preview" class="h-28 w-28 rounded-full border border-slate-200 object-cover">
                @endif
                <button
                    type="button"
                    wire:click="clearSingle"
                    class="mt-2 rounded-md border border-slate-300 px-3 py-1 text-xs font-black text-slate-700 transition hover:border-pink-300 hover:text-pink-700"
                >
                    Remove Uploaded Image
                </button>
            </div>
        @endif

        @error('upload')
            <p class="text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
    @endif

    <div wire:loading wire:target="upload,uploads" class="text-xs font-semibold text-slate-500">
        Uploading image...
    </div>
</div>

