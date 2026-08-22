<?php

namespace App\Support;

class EmailBlockRenderer
{
    /**
     * Render a list of editor blocks into a self-contained, email-safe HTML
     * table — droppable into any surrounding markup (table- or div-based).
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  array<string, string>  $variables  Whitelisted {{token}} => value substitutions
     */
    public static function render(array $blocks, array $variables = []): string
    {
        if ($blocks === []) {
            return '';
        }

        $rows = '';

        foreach ($blocks as $block) {
            $type = (string) ($block['type'] ?? '');

            $rows .= match ($type) {
                'heading' => self::heading($block, $variables),
                'paragraph' => self::paragraph($block, $variables),
                'image' => self::image($block),
                'button' => self::button($block, $variables),
                'divider' => self::divider(),
                'spacer' => self::spacer($block),
                default => '',
            };
        }

        if ($rows === '') {
            return '';
        }

        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100%;"><tbody>'.$rows.'</tbody></table>';
    }

    private static function heading(array $block, array $variables): string
    {
        $size = ($block['size'] ?? 'md') === 'lg' ? '22px' : '17px';
        $text = self::substitute((string) ($block['text'] ?? ''), $variables);

        return '<tr><td style="padding:0 0 12px;font-size:'.$size.';font-weight:700;color:#0f172a;">'.$text.'</td></tr>';
    }

    private static function paragraph(array $block, array $variables): string
    {
        $text = nl2br(self::substitute((string) ($block['text'] ?? ''), $variables));

        return '<tr><td style="padding:0 0 14px;font-size:14px;line-height:1.6;color:#334155;">'.$text.'</td></tr>';
    }

    private static function image(array $block): string
    {
        $url = MediaUrl::resolve((string) ($block['url'] ?? ''));

        if ($url === null) {
            return '';
        }

        $alt = e((string) ($block['alt'] ?? ''));

        return '<tr><td style="padding:0 0 14px;"><img src="'.$url.'" alt="'.$alt.'" style="max-width:100%;height:auto;display:block;border-radius:8px;"></td></tr>';
    }

    private static function button(array $block, array $variables): string
    {
        $label = self::substitute((string) ($block['label'] ?? 'Learn more'), $variables);
        $url = self::substitute((string) ($block['url'] ?? '#'), $variables);

        return '<tr><td style="padding:0 0 16px;">'
            .'<table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="border-radius:8px;background:#db2777;">'
            .'<a href="'.e($url).'" style="display:inline-block;padding:11px 22px;font-size:13px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:8px;">'.$label.'</a>'
            .'</td></tr></table>'
            .'</td></tr>';
    }

    private static function divider(): string
    {
        return '<tr><td style="padding:8px 0;"><div style="border-top:1px solid #e2e8f0;"></div></td></tr>';
    }

    private static function spacer(array $block): string
    {
        $height = max(0, min(80, (int) ($block['height'] ?? 16)));

        return '<tr><td style="height:'.$height.'px;line-height:'.$height.'px;font-size:0;">&nbsp;</td></tr>';
    }

    private static function substitute(string $text, array $variables): string
    {
        $text = e($text);

        $replacements = [];
        foreach ($variables as $key => $value) {
            $replacements['{{'.$key.'}}'] = e((string) $value);
        }

        return strtr($text, $replacements);
    }
}
