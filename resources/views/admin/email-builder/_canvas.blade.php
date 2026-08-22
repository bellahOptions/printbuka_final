{{--
    Shared drag-and-drop block canvas.

    Props:
    - $fieldName  string  Hidden input name the serialized blocks are submitted under.
    - $blocks     array   Initial blocks for this zone.
    - $variables  array   List of {{token}} names available to this zone (informational).
--}}
@php($variables = $variables ?? [])
<div x-data="emailBlockBuilder(@js($blocks ?? []))" x-init="initSortable($refs.canvas)">
    <input type="hidden" name="{{ $fieldName }}" :value="serialize()">

    @if (! empty($variables))
        <p class="text-xs text-slate-500 mb-3">
            Available placeholders:
            @foreach ($variables as $variable)
                <code class="pb-badge pb-badge-outline text-[10px] mx-0.5">&#123;&#123;{{ $variable }}&#125;&#125;</code>
            @endforeach
        </p>
    @endif

    <div class="grid gap-4" style="grid-template-columns: 180px 1fr 300px;">
        {{-- Palette --}}
        <div class="pb-card p-3 space-y-2 h-fit">
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Add block</p>
            <button type="button" class="pb-btn pb-btn-outline pb-btn-sm w-full justify-start" @click="addBlock('heading')">+ Heading</button>
            <button type="button" class="pb-btn pb-btn-outline pb-btn-sm w-full justify-start" @click="addBlock('paragraph')">+ Paragraph</button>
            <button type="button" class="pb-btn pb-btn-outline pb-btn-sm w-full justify-start" @click="addBlock('image')">+ Image</button>
            <button type="button" class="pb-btn pb-btn-outline pb-btn-sm w-full justify-start" @click="addBlock('button')">+ Button</button>
            <button type="button" class="pb-btn pb-btn-outline pb-btn-sm w-full justify-start" @click="addBlock('divider')">+ Divider</button>
            <button type="button" class="pb-btn pb-btn-outline pb-btn-sm w-full justify-start" @click="addBlock('spacer')">+ Spacer</button>
        </div>

        {{-- Canvas --}}
        <div>
            <div x-ref="canvas" class="space-y-2 min-h-[160px] rounded-xl border border-dashed border-slate-300 p-3">
                <template x-for="block in blocks" :key="block.id">
                    <div
                        class="pb-card p-3 flex items-start gap-3 cursor-pointer transition"
                        :class="selectedId === block.id ? 'ring-2 ring-pink-500' : ''"
                        @click="selectBlock(block.id)"
                    >
                        <span class="block-drag-handle cursor-grab select-none text-slate-400 pt-0.5" title="Drag to reorder">⠿⠿</span>
                        <div class="flex-1 min-w-0 text-sm">
                            <p class="text-[10px] font-black uppercase tracking-wide text-pink-600 mb-0.5" x-text="block.type"></p>
                            <p class="truncate text-slate-700" x-show="block.type === 'heading'" x-text="block.text"></p>
                            <p class="truncate text-slate-700" x-show="block.type === 'paragraph'" x-text="block.text"></p>
                            <p class="truncate text-slate-700" x-show="block.type === 'button'" x-text="'Button: ' + block.label"></p>
                            <p class="truncate text-slate-700" x-show="block.type === 'image'" x-text="block.url || 'No image URL set'"></p>
                            <p class="text-slate-400" x-show="block.type === 'divider'">— divider —</p>
                            <p class="text-slate-400" x-show="block.type === 'spacer'" x-text="'Spacer — ' + block.height + 'px'"></p>
                        </div>
                        <button type="button" class="text-slate-400 hover:text-pink-600" @click.stop="removeBlock(block.id)" title="Remove block">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                </template>
                <p class="text-sm text-slate-400 text-center py-6" x-show="blocks.length === 0">No blocks yet — add one from the left.</p>
            </div>
        </div>

        {{-- Properties panel --}}
        <div class="pb-card p-4 h-fit" x-show="selected">
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mb-3">Block settings</p>

            <template x-if="selected?.type === 'heading'">
                <div class="space-y-3">
                    <div class="pb-field">
                        <label class="pb-label">Text</label>
                        <input type="text" class="pb-input" x-model="selected.text">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Size</label>
                        <select class="pb-select" x-model="selected.size">
                            <option value="md">Normal</option>
                            <option value="lg">Large</option>
                        </select>
                    </div>
                </div>
            </template>

            <template x-if="selected?.type === 'paragraph'">
                <div class="pb-field">
                    <label class="pb-label">Text</label>
                    <textarea class="pb-textarea" rows="5" x-model="selected.text"></textarea>
                </div>
            </template>

            <template x-if="selected?.type === 'image'">
                <div class="space-y-3">
                    <div class="pb-field">
                        <label class="pb-label">Image URL</label>
                        <input type="text" class="pb-input" placeholder="https://…" x-model="selected.url">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Alt text</label>
                        <input type="text" class="pb-input" x-model="selected.alt">
                    </div>
                </div>
            </template>

            <template x-if="selected?.type === 'button'">
                <div class="space-y-3">
                    <div class="pb-field">
                        <label class="pb-label">Label</label>
                        <input type="text" class="pb-input" x-model="selected.label">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Link URL</label>
                        <input type="text" class="pb-input" placeholder="https://…" x-model="selected.url">
                    </div>
                </div>
            </template>

            <template x-if="selected?.type === 'spacer'">
                <div class="pb-field">
                    <label class="pb-label">Height (px)</label>
                    <input type="number" min="0" max="80" class="pb-input" x-model.number="selected.height">
                </div>
            </template>

            <template x-if="selected?.type === 'divider'">
                <p class="text-sm text-slate-500">A plain horizontal divider — no settings.</p>
            </template>
        </div>
    </div>
</div>
