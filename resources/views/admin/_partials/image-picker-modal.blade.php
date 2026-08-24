{{-- One shared Cloudinary image-library / crop-on-upload modal for the whole
     admin portal. Any "Choose Image" button (data-image-picker) or the rich
     text editor's image toolbar button opens this same modal via the
     `open-image-picker` window event — see resources/js/rich-media.js. --}}
<div x-data="imageLibraryModal()" x-show="open" x-cloak
     class="fixed inset-0 z-[70] flex items-center justify-center p-4" style="display: none;">
    <div class="absolute inset-0 bg-slate-950/60" @click="close()"></div>

    <div class="relative w-full max-w-3xl max-h-[85vh] flex flex-col rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h2 class="text-sm font-black text-slate-950">Image Library</h2>
            <button type="button" class="text-slate-400 hover:text-slate-700" @click="close()">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <div class="flex gap-1 border-b border-slate-200 px-5 pt-3">
            <button type="button"
                class="px-3 py-2 text-xs font-black rounded-t-lg"
                :class="tab === 'library' ? 'bg-slate-100 text-slate-950' : 'text-slate-500 hover:text-slate-800'"
                @click="tab = 'library'">Library</button>
            <button type="button"
                class="px-3 py-2 text-xs font-black rounded-t-lg"
                :class="tab === 'upload' ? 'bg-slate-100 text-slate-950' : 'text-slate-500 hover:text-slate-800'"
                @click="tab = 'upload'">Upload New</button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            <p x-show="error" x-text="error" class="mb-3 rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-700"></p>

            {{-- Library tab --}}
            <div x-show="tab === 'library'">
                <div x-show="loading && !images.length" class="py-10 text-center text-xs font-bold text-slate-400">Loading images…</div>

                <div x-show="!loading || images.length" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    <template x-for="image in images" :key="image.public_id">
                        <button type="button" @click="selectImage(image)"
                            class="group relative aspect-square rounded-lg border border-slate-200 overflow-hidden hover:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <img :src="image.url" :alt="image.public_id" class="h-full w-full object-cover" loading="lazy">
                            <span class="absolute inset-0 bg-slate-950/0 group-hover:bg-slate-950/20 transition"></span>
                        </button>
                    </template>
                </div>

                <p x-show="!loading && !images.length" class="py-10 text-center text-xs font-bold text-slate-400">No images uploaded yet. Switch to "Upload New".</p>

                <div class="mt-4 text-center" x-show="nextCursor">
                    <button type="button" @click="loadImages(true)" :disabled="loading"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-black text-slate-700 hover:bg-slate-50 disabled:opacity-50">
                        <span x-show="!loading">Load more</span>
                        <span x-show="loading">Loading…</span>
                    </button>
                </div>
            </div>

            {{-- Upload tab --}}
            <div x-show="tab === 'upload'">
                <div x-show="!hasFile">
                    <label class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-300 px-6 py-10 cursor-pointer hover:border-pink-400 hover:bg-pink-50/40 transition">
                        <x-heroicon-o-photo class="w-8 h-8 text-slate-400" />
                        <span class="text-xs font-black text-slate-600">Click to choose an image</span>
                        <span class="text-[11px] font-semibold text-slate-400">JPG, PNG or WEBP — up to 8MB</span>
                        <input type="file" accept="image/*" class="hidden" @change="onFileChosen($event)">
                    </label>
                </div>

                <div x-show="hasFile">
                    <p class="mb-2 text-[11px] font-bold text-slate-500">Drag corners to crop, then confirm.</p>
                    <div class="max-h-[45vh] overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                        <img x-ref="cropImage" class="block max-w-full">
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="destroyCropper()" :disabled="uploading"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-black text-slate-700 hover:bg-slate-50 disabled:opacity-50">
                            Choose Different Image
                        </button>
                        <button type="button" @click="uploadCropped()" :disabled="uploading"
                            class="rounded-lg bg-pink-600 px-4 py-2 text-xs font-black text-white hover:bg-pink-700 disabled:opacity-50">
                            <span x-show="!uploading">Use This Image</span>
                            <span x-show="uploading">Uploading…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
