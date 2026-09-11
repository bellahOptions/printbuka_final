@extends('layouts.admin')
@section('title', 'Compose Newsletter | Printbuka')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    <div>
        <a href="{{ route('admin.newsletters.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-800">← Back to Newsletter Campaigns</a>
        <h1 class="pb-page-title mt-2">Compose Newsletter</h1>
        <p class="pb-page-subtitle">Build the newsletter below, then send it to every active, verified customer.</p>
    </div>

    @if ($errors->any())
        <div class="pb-alert pb-alert-error flex-col items-start">
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
            <div class="pb-field">
                <label class="pb-label">Email Subject *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    class="pb-input"
                    placeholder="Limited Offer: Save on Your Next Print Order">
            </div>

            <div class="pb-field">
                <label class="pb-label">Preheader</label>
                <input type="text" name="preheader" value="{{ old('preheader') }}"
                    class="pb-input"
                    placeholder="Fresh deals on print and branding services">
            </div>
        </div>

        <div class="pb-card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="pb-section-title text-sm">Live Preview</p>
                <a href="#" id="open-full-preview" target="_blank" class="text-xs font-semibold text-brand-600 hover:text-brand-800">Open in new tab ↗</a>
            </div>
            <iframe id="template-preview-frame" class="w-full rounded-xl border border-slate-200" style="height: 420px;" title="Newsletter preview"></iframe>
        </div>

        <div class="pb-card p-5">
            <p class="pb-section-title text-sm mb-4">Newsletter content</p>
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
