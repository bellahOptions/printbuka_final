<?php

namespace App\Mail\Concerns;

use App\Models\EmailTemplate;
use App\Support\EmailBlockRenderer;

trait HasEditableTemplate
{
    private false|null|EmailTemplate $resolvedTemplate = false;

    /**
     * Unique registry key for this mail type, e.g. 'staff.kyc_reminder'.
     */
    abstract protected function templateKey(): string;

    /**
     * Whitelisted {{token}} => value substitutions available to this mail's blocks.
     *
     * @return array<string, string>
     */
    abstract protected function templateVariables(): array;

    protected function templateSubject(string $default): string
    {
        $subject = $this->loadTemplate()?->subject;

        if (! filled($subject)) {
            return $default;
        }

        $replacements = [];
        foreach ($this->templateVariables() as $key => $value) {
            $replacements['{{'.$key.'}}'] = (string) $value;
        }

        $subject = strtr($subject, $replacements);

        // Strip control characters (incl. CR/LF) to prevent header injection via a stored subject.
        return trim(preg_replace('/[\r\n\x00-\x1F]+/', ' ', $subject) ?? '');
    }

    protected function templateIntroHtml(): string
    {
        $blocks = $this->loadTemplate()?->intro_blocks ?? [];

        return EmailBlockRenderer::render($blocks, $this->templateVariables());
    }

    protected function templateOutroHtml(): string
    {
        $blocks = $this->loadTemplate()?->outro_blocks ?? [];

        return EmailBlockRenderer::render($blocks, $this->templateVariables());
    }

    private function loadTemplate(): ?EmailTemplate
    {
        if ($this->resolvedTemplate === false) {
            $this->resolvedTemplate = EmailTemplate::query()
                ->where('key', $this->templateKey())
                ->where('is_active', true)
                ->first();
        }

        return $this->resolvedTemplate;
    }
}
