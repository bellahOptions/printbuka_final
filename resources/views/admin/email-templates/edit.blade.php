@extends('layouts.admin')
@section('title', $entry['name'].' | Email Templates | Printbuka')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    <div>
        <a href="{{ route('admin.email-templates.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Email Templates</a>
        <h1 class="text-2xl font-black text-slate-950 mt-2">{{ $entry['name'] }}</h1>
        <p class="text-sm text-slate-500 mt-1">The line-item tables and computed figures in this email always stay accurate — only the intro, footer, and subject below are editable.</p>
    </div>

    <form method="POST" action="{{ route('admin.email-templates.update', $key) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="pb-card p-5">
            <label class="pb-label">Subject line</label>
            <input
                type="text"
                name="subject"
                class="pb-input"
                placeholder="Leave blank to use the default subject"
                value="{{ old('subject', $template?->subject) }}"
            >
            @if (! empty($entry['variables']))
                <p class="text-xs text-slate-500 mt-2">
                    Placeholders:
                    @foreach ($entry['variables'] as $variable)
                        <code class="pb-badge pb-badge-outline text-[10px] mx-0.5">&#123;&#123;{{ $variable }}&#125;&#125;</code>
                    @endforeach
                </p>
            @endif
        </div>

        <div class="pb-card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-black text-slate-900">Live Preview</p>
                <a href="#" id="open-full-preview" target="_blank" class="text-xs font-black text-pink-600 hover:text-pink-800">Open in new tab ↗</a>
            </div>
            <iframe id="template-preview-frame" class="w-full rounded-xl border border-slate-200" style="height: 420px;" title="Email preview"></iframe>
        </div>

        <div class="pb-card p-5">
            <p class="text-sm font-black text-slate-900 mb-4">Intro — shown before the email's main content</p>
            @include('admin.email-builder._canvas', [
                'fieldName' => 'intro_blocks',
                'blocks' => $template?->intro_blocks ?? [],
                'variables' => $entry['variables'] ?? [],
            ])
        </div>

        <div class="pb-card p-5">
            <p class="text-sm font-black text-slate-900 mb-4">Footer — shown after the email's main content</p>
            @include('admin.email-builder._canvas', [
                'fieldName' => 'outro_blocks',
                'blocks' => $template?->outro_blocks ?? [],
                'variables' => $entry['variables'] ?? [],
            ])
        </div>

        <div class="flex items-center justify-between">
            <div class="flex gap-3">
                <button type="submit" class="pb-btn pb-btn-primary">Save Template</button>
                <a href="{{ route('admin.email-templates.index') }}" class="pb-btn pb-btn-outline">Cancel</a>
            </div>
            @if ($template)
                <form method="POST" action="{{ route('admin.email-templates.reset', $key) }}" onsubmit="return confirm('Revert this template to its default content?');">
                    @csrf
                    <button type="submit" class="pb-btn pb-btn-ghost text-pink-700">Revert to Default</button>
                </form>
            @endif
        </div>
    </form>

</div>
<script>
    function buildTemplatePreviewUrl() {
        const introBlocks = document.querySelector('input[name="intro_blocks"]')?.value ?? '[]';
        const outroBlocks = document.querySelector('input[name="outro_blocks"]')?.value ?? '[]';
        const url = new URL('{{ route('admin.email-templates.preview', $key) }}', window.location.origin);
        url.searchParams.set('intro_blocks', introBlocks);
        url.searchParams.set('outro_blocks', outroBlocks);
        return url.toString();
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('open-full-preview')?.addEventListener('click', (event) => {
            event.preventDefault();
            window.open(buildTemplatePreviewUrl(), '_blank');
        });

        window.wireLivePreview('#template-preview-frame', buildTemplatePreviewUrl);
    });
</script>
@endsection
