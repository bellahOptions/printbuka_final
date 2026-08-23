<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Support\EmailBlockRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPdfTemplateController extends Controller
{
    public function index(): View
    {
        $registry = config('printbuka_pdf_templates', []);
        $customized = EmailTemplate::query()->pluck('updated_at', 'key');

        $templates = collect($registry)->map(function (array $entry, string $key) use ($customized): array {
            return [
                'key' => $key,
                'name' => $entry['name'],
                'view' => $entry['view'],
                'is_customized' => $customized->has($key),
                'updated_at' => $customized->get($key),
            ];
        })->values();

        return view('admin.pdf-templates.index', ['templates' => $templates]);
    }

    public function edit(string $key): View
    {
        $entry = $this->registryEntry($key);

        $template = EmailTemplate::query()->where('key', $key)->first();

        return view('admin.pdf-templates.edit', [
            'key' => $key,
            'entry' => $entry,
            'template' => $template,
        ]);
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $entry = $this->registryEntry($key);

        $validated = $request->validate([
            'intro_blocks' => ['required', 'string'],
            'outro_blocks' => ['required', 'string'],
        ]);

        $introBlocks = json_decode($validated['intro_blocks'], true);
        $outroBlocks = json_decode($validated['outro_blocks'], true);
        abort_unless(is_array($introBlocks) && is_array($outroBlocks), 422, 'Invalid block data.');

        EmailTemplate::query()->updateOrCreate(
            ['key' => $key],
            [
                'name' => $entry['name'],
                'mail_class' => $entry['view'],
                'intro_blocks' => $introBlocks,
                'outro_blocks' => $outroBlocks,
                'is_active' => true,
                'updated_by_id' => $request->user()->id,
            ]
        );

        return redirect()
            ->route('admin.pdf-templates.index')
            ->with('status', '"'.$entry['name'].'" PDF template saved.');
    }

    public function reset(string $key): RedirectResponse
    {
        $entry = $this->registryEntry($key);

        EmailTemplate::query()->where('key', $key)->delete();

        return redirect()
            ->route('admin.pdf-templates.index')
            ->with('status', '"'.$entry['name'].'" reverted to its default content.');
    }

    public function preview(Request $request, string $key): View
    {
        $entry = $this->registryEntry($key);

        $introBlocks = json_decode((string) $request->query('intro_blocks', '[]'), true) ?: [];
        $outroBlocks = json_decode((string) $request->query('outro_blocks', '[]'), true) ?: [];

        $sampleVariables = collect($entry['variables'] ?? [])
            ->mapWithKeys(fn (string $variable) => [$variable => '['.str($variable)->replace('_', ' ')->title()->value().']'])
            ->all();

        return view('admin.pdf-templates.preview', [
            'entry' => $entry,
            'introHtml' => EmailBlockRenderer::render($introBlocks, $sampleVariables),
            'outroHtml' => EmailBlockRenderer::render($outroBlocks, $sampleVariables),
        ]);
    }

    private function registryEntry(string $key): array
    {
        // Dotted keys ('pdf.invoice_customer') must not go through config()'s
        // dot-notation nested lookup — see AdminEmailTemplateController for why.
        $entry = config('printbuka_pdf_templates', [])[$key] ?? null;
        abort_if($entry === null, 404, 'Unknown PDF template.');

        return $entry;
    }
}
