@extends('layouts.admin')
@section('title', $entry['name'].' | PDF Templates | Printbuka')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    <div>
        <a href="{{ route('admin.pdf-templates.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to PDF Templates</a>
        <h1 class="text-2xl font-black text-slate-950 mt-2">{{ $entry['name'] }}</h1>
        <p class="text-sm text-slate-500 mt-1">The figures, line items, and computed totals on this PDF always stay accurate — only the intro and footer below are editable.</p>
    </div>

    <form method="POST" action="{{ route('admin.pdf-templates.update', $key) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="pb-card p-5">
            <p class="text-sm font-black text-slate-900 mb-4">Intro — shown at the top of the PDF, above the header</p>
            @include('admin.email-builder._canvas', [
                'fieldName' => 'intro_blocks',
                'blocks' => $template?->intro_blocks ?? [],
                'variables' => $entry['variables'] ?? [],
            ])
        </div>

        <div class="pb-card p-5">
            <p class="text-sm font-black text-slate-900 mb-4">Footer — shown at the bottom of the PDF, below the main content</p>
            @include('admin.email-builder._canvas', [
                'fieldName' => 'outro_blocks',
                'blocks' => $template?->outro_blocks ?? [],
                'variables' => $entry['variables'] ?? [],
            ])
        </div>

        <div class="flex items-center justify-between">
            <div class="flex gap-3">
                <button type="submit" class="pb-btn pb-btn-primary">Save Template</button>
                <button type="button" class="pb-btn pb-btn-outline" onclick="previewPdfTemplate('{{ route('admin.pdf-templates.preview', $key) }}')">Preview</button>
                <a href="{{ route('admin.pdf-templates.index') }}" class="pb-btn pb-btn-outline">Cancel</a>
            </div>
            @if ($template)
                <form method="POST" action="{{ route('admin.pdf-templates.reset', $key) }}" onsubmit="return confirm('Revert this PDF template to its default content?');">
                    @csrf
                    <button type="submit" class="pb-btn pb-btn-ghost text-pink-700">Revert to Default</button>
                </form>
            @endif
        </div>
    </form>

</div>
<script>
    function previewPdfTemplate(baseUrl) {
        const introBlocks = document.querySelector('input[name="intro_blocks"]')?.value ?? '[]';
        const outroBlocks = document.querySelector('input[name="outro_blocks"]')?.value ?? '[]';
        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('intro_blocks', introBlocks);
        url.searchParams.set('outro_blocks', outroBlocks);
        window.open(url.toString(), '_blank');
    }
</script>
@endsection
