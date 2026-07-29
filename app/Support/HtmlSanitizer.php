<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'a',
        'blockquote',
        'br',
        'caption',
        'code',
        'div',
        'em',
        'figcaption',
        'figure',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'hr',
        'img',
        'li',
        'ol',
        'p',
        'pre',
        'span',
        'strong',
        'table',
        'tbody',
        'td',
        'tfoot',
        'th',
        'thead',
        'tr',
        'u',
        'ul',
    ];

    private const GLOBAL_ATTRIBUTES = ['class', 'id', 'title', 'aria-label'];

    private const TAG_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height', 'loading', 'decoding'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan', 'scope'],
    ];

    public static function clean(?string $html): string
    {
        $html = (string) $html;

        if ($html === '') {
            return '';
        }

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);

        $document->loadHTML(
            '<?xml encoding="utf-8" ?><!doctype html><html><body>'.$html.'</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $body = $document->getElementsByTagName('body')->item(0);

        if (! $body) {
            return '';
        }

        self::sanitizeChildren($body);

        $clean = '';
        foreach ($body->childNodes as $child) {
            $clean .= $document->saveHTML($child);
        }

        return trim($clean);
    }

    private static function sanitizeChildren(DOMNode $node): void
    {
        for ($child = $node->firstChild; $child !== null;) {
            $next = $child->nextSibling;

            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                    self::unwrapOrRemove($child);
                    $child = $next;

                    continue;
                }

                self::sanitizeAttributes($child, $tag);
                self::sanitizeChildren($child);
            }

            $child = $next;
        }
    }

    private static function unwrapOrRemove(DOMElement $element): void
    {
        $tag = strtolower($element->tagName);

        if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'link', 'meta'], true)) {
            $element->parentNode?->removeChild($element);

            return;
        }

        while ($element->firstChild) {
            $element->parentNode?->insertBefore($element->firstChild, $element);
        }

        $element->parentNode?->removeChild($element);
    }

    private static function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowed = array_merge(self::GLOBAL_ATTRIBUTES, self::TAG_ATTRIBUTES[$tag] ?? []);

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            $value = trim($attribute->value);

            if (str_starts_with($name, 'on') || str_starts_with($name, 'data-') || ! in_array($name, $allowed, true)) {
                $element->removeAttributeNode($attribute);

                continue;
            }

            if (in_array($name, ['href', 'src'], true) && ! self::isSafeUrl($value, $name)) {
                $element->removeAttributeNode($attribute);
            }
        }

        if ($tag === 'a') {
            $target = strtolower($element->getAttribute('target'));
            if ($target !== '' && ! in_array($target, ['_blank', '_self'], true)) {
                $element->removeAttribute('target');
            }

            $allowedRel = ['nofollow', 'noopener', 'noreferrer', 'sponsored', 'ugc'];
            $rel = array_values(array_unique(array_filter(
                preg_split('/\s+/', strtolower($element->getAttribute('rel'))) ?: [],
                fn (string $token): bool => in_array($token, $allowedRel, true),
            )));
            if ($element->getAttribute('target') === '_blank') {
                $rel = array_values(array_unique([...$rel, 'noopener', 'noreferrer']));
            }

            if ($rel === []) {
                $element->removeAttribute('rel');
            } else {
                $element->setAttribute('rel', implode(' ', $rel));
            }
        }
    }

    private static function isSafeUrl(string $value, string $attribute): bool
    {
        if ($value === '') {
            return true;
        }

        $decoded = rawurldecode($value);
        if (
            preg_match('/[\x00-\x1F\x7F]/', $decoded)
            || str_starts_with($decoded, '//')
            || str_starts_with($decoded, '\\')
        ) {
            return false;
        }

        if (str_starts_with($decoded, '#') || preg_match('#^/(?!/)#', $decoded)) {
            return true;
        }

        $scheme = strtolower((string) parse_url($decoded, PHP_URL_SCHEME));

        if ($scheme === '') {
            return true;
        }

        return $attribute === 'src'
            ? in_array($scheme, ['http', 'https'], true)
            : in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }
}
