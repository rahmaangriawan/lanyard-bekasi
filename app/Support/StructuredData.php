<?php

namespace App\Support;

use DOMDocument;
use DOMElement;

final class StructuredData
{
    public static function encode(mixed $value): string
    {
        return json_encode(
            $value,
            JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_HEX_TAG
                | JSON_HEX_AMP
                | JSON_HEX_APOS
                | JSON_HEX_QUOT
                | JSON_THROW_ON_ERROR,
        );
    }

    public static function faqSchema(array $items): ?array
    {
        $entities = collect($items)->filter(fn (array $item): bool => trim((string) ($item['question'] ?? '')) !== '' && trim((string) ($item['answer'] ?? '')) !== '')
            ->map(fn (array $item): array => ['@type' => 'Question', 'name' => trim(strip_tags((string) $item['question'])), 'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags((string) $item['answer']))]])
            ->values()->all();

        return $entities === [] ? null : ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities];
    }

    public static function extractFaqs(string $html, int $limit = 8): array
    {
        if (trim($html) === '' || ! class_exists(DOMDocument::class)) {
            return [];
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML('<?xml encoding="UTF-8"><main>'.$html.'</main>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if (! $loaded) {
            return [];
        }

        $items = [];
        foreach (iterator_to_array($document->getElementsByTagName('*')) as $node) {
            if (! $node instanceof DOMElement || ! in_array(strtolower($node->tagName), ['h2', 'h3', 'h4'], true)) {
                continue;
            }
            $question = self::clean($node->textContent);
            if (! str_ends_with($question, '?')) {
                continue;
            }

            $parts = [];
            for ($sibling = $node->nextSibling; $sibling; $sibling = $sibling->nextSibling) {
                if ($sibling instanceof DOMElement && in_array(strtolower($sibling->tagName), ['h2', 'h3', 'h4'], true)) {
                    break;
                }
                $text = self::clean($sibling->textContent);
                if ($text !== '') {
                    $parts[] = $text;
                }
            }
            if ($parts !== []) {
                $items[] = ['question' => $question, 'answer' => implode(' ', $parts)];
            }
            if (count($items) >= $limit) {
                break;
            }
        }

        return $items;
    }

    private static function clean(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }
}
