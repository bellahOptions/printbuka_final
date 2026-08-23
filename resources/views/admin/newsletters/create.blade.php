@extends('layouts.admin')
@section('title', 'Compose Newsletter | Printbuka')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    <div>
        <a href="{{ route('admin.newsletters.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Newsletter Campaigns</a>
        <h1 class="text-2xl font-black text-slate-950 mt-2">Compose Newsletter</h1>
        <p class="text-sm text-slate-500 mt-1">Build the newsletter below, then send it to every active, verified customer.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-pink-200 bg-pink-50 p-4 text-sm font-bold text-pink-800">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.newsletters.store') }}" onsubmit="return confirm('Send this newsletter now? This cannot be undone.');" class="space-y-6">
        @csrf

        <div class="pb-card p-5 grid gap-5 sm:grid-cols-2">
            <label class="text-sm font-black text-slate-800">
                Email Subject *
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    class="pb-input mt-2"
                    placeholder="Limited Offer: Save on Your Next Print Order">
            </label>

            <label class="text-sm font-black text-slate-800">
                Preheader
                <input type="text" name="preheader" value="{{ old('preheader') }}"
                    class="pb-input mt-2"
                    placeholder="Fresh deals on print and branding services">
            </label>
        </div>

        <div class="pb-card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-black text-slate-900">Live Preview</p>
                <a href="#" id="open-full-preview" target="_blank" class="text-xs font-black text-pink-600 hover:text-pink-800">Open in new tab ↗</a>
            </div>
            <iframe id="template-preview-frame" class="w-full rounded-xl border border-slate-200" style="height: 420px;" title="Newsletter preview"></iframe>
        </div>

        <div class="pb-card p-5">
            <p class="text-sm font-black text-slate-900 mb-4">Newsletter content</p>
            @include('admin.email-builder._canvas', [
                'fieldName' => 'blocks',
                'blocks' => [],
                'variables' => ['customer_name', 'company_name'],
            ])
        </div>

        <div class="flex gap-3">
            <button type="submit" class="pb-btn pb-btn-primary">Send Newsletter</button>
            <a href="{{ route('admin.newsletters.index') }}" class="pb-btn pb-btn-outline">Cancel</a>
        </div>
    </form>

</div>
<script>
    function buildNewsletterPreviewUrl() {
        const blocks = document.querySelector('input[name="blocks"]')?.value ?? '[]';
        const url = new URL('{{ route('admin.newsletters.preview') }}', window.location.origin);
        url.searchParams.set('blocks', blocks);
        return url.toString();
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('open-full-preview')?.addEventListener('click', (event) => {
            event.preventDefault();
            window.open(buildNewsletterPreviewUrl(), '_blank');
        });

        window.wireLivePreview('#template-preview-frame', buildNewsletterPreviewUrl);
    });
</script>
@endsection
