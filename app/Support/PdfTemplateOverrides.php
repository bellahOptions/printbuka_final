<?php

namespace App\Support;

use App\Models\EmailTemplate;

class PdfTemplateOverrides
{
    /**
     * Resolve the intro/outro HTML for a customizable PDF, ready to spread
     * into the view data passed to Pdf::loadView(). Empty strings when the
     * super admin hasn't customized this PDF — the document renders exactly
     * as it always has.
     *
     * @param  array<string, string>  $variables  Whitelisted {{token}} substitutions
     * @return array{introHtml: string, outroHtml: string}
     */
    public static function forKey(string $key, array $variables = []): array
    {
        $template = EmailTemplate::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        return [
            'introHtml' => EmailBlockRenderer::render($template?->intro_blocks ?? [], $variables),
            'outroHtml' => EmailBlockRenderer::render($template?->outro_blocks ?? [], $variables),
        ];
    }
}
