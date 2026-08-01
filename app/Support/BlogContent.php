<?php

namespace App\Support;

use Illuminate\Support\Str;

final class BlogContent
{
    /**
     * Render CMS article content that may contain trusted HTML or Markdown.
     * Inline and block Markdown are converted, then the existing sanitizer removes
     * unsafe markup and asset URLs are normalized.
     */
    public static function render(?string $content): string
    {
        $options = [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ];

        // Inline Markdown must run first: CommonMark treats existing block HTML
        // (the format used by imported CMS content) as opaque, so `**bold**`
        // inside a `<p>` would otherwise remain literal text.
        $inline = Str::inlineMarkdown((string) $content, $options);
        $markdown = Str::markdown($inline, $options);

        return AssetUrl::html(HtmlSanitizer::clean($markdown));
    }
}
