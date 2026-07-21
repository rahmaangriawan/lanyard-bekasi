<?php

namespace App\Support;

use App\Models\Setting;
use DOMDocument;
use DOMNode;
use DOMText;
use Illuminate\Support\Facades\Cache;

final class AutoLinkSeo
{
    private const SKIP_TAGS = ['a', 'button', 'code', 'pre', 'script', 'style', 'textarea', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    public static function apply(string $html): string
    {
        if (trim($html) === '') {
            return $html;
        }

        $settings = Cache::remember('seo_auto_links_runtime', now()->addMinutes(10), fn () => Setting::query()
            ->whereIn('key', ['seo_auto_links', 'seo_auto_links_limit'])
            ->pluck('value', 'key')
            ->all());

        $links = collect(json_decode($settings['seo_auto_links'] ?? '[]', true) ?: [])
            ->filter(fn ($item) => is_array($item))
            ->map(fn (array $item) => [
                'keyword' => trim((string) ($item['keyword'] ?? '')),
                'url' => self::validUrl((string) ($item['url'] ?? '')),
            ])
            ->filter(fn (array $item) => $item['keyword'] !== '' && $item['url'] !== null)
            ->sortByDesc(fn (array $item) => mb_strlen($item['keyword']))
            ->values()
            ->all();

        $limit = max(0, min(20, (int) ($settings['seo_auto_links_limit'] ?? 2)));

        return $limit > 0 && $links !== [] ? self::inject($html, $links, $limit) : $html;
    }

    public static function inject(string $html, array $links, int $limit): string
    {
        if ($limit < 1 || trim($html) === '') {
            return $html;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><div data-auto-link-root>'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementsByTagName('div')->item(0);
        if (! $root) {
            return $html;
        }

        $textNodes = [];
        self::collectTextNodes($root, $textNodes);
        $inserted = 0;
        $usedKeywords = [];

        for ($nodeIndex = 0; $nodeIndex < count($textNodes); $nodeIndex++) {
            $textNode = $textNodes[$nodeIndex];
            if ($inserted >= $limit || ! $textNode->parentNode) {
                break;
            }

            foreach ($links as $link) {
                $keyword = trim((string) ($link['keyword'] ?? ''));
                $url = self::validUrl((string) ($link['url'] ?? ''));
                $keywordKey = mb_strtolower($keyword);

                if ($keyword === '' || $url === null || isset($usedKeywords[$keywordKey])) {
                    continue;
                }

                $pattern = '/(?<![\pL\pN])('.preg_quote($keyword, '/').')(?![\pL\pN])/iu';
                if (! preg_match($pattern, $textNode->nodeValue ?? '', $match, PREG_OFFSET_CAPTURE)) {
                    continue;
                }

                $matchedText = $match[1][0];
                $offset = $match[1][1];
                $before = substr($textNode->nodeValue, 0, $offset);
                $after = substr($textNode->nodeValue, $offset + strlen($matchedText));
                $fragment = $document->createDocumentFragment();

                if ($before !== '') {
                    $beforeNode = $document->createTextNode($before);
                    $fragment->appendChild($beforeNode);
                    $textNodes[] = $beforeNode;
                }

                $anchor = $document->createElement('a');
                $anchor->setAttribute('href', $url);
                $anchor->setAttribute('class', 'seo-auto-link');
                $anchor->appendChild($document->createTextNode($matchedText));
                $fragment->appendChild($anchor);

                if ($after !== '') {
                    $afterNode = $document->createTextNode($after);
                    $fragment->appendChild($afterNode);
                    $textNodes[] = $afterNode;
                }

                $textNode->parentNode->replaceChild($fragment, $textNode);
                $usedKeywords[$keywordKey] = true;
                $inserted++;
                break;
            }
        }

        $result = '';
        foreach ($root->childNodes as $child) {
            $result .= $document->saveHTML($child);
        }

        return $result;
    }

    private static function collectTextNodes(DOMNode $node, array &$textNodes): void
    {
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMText && trim($child->nodeValue ?? '') !== '') {
                $textNodes[] = $child;
                continue;
            }

            if ($child->nodeType !== XML_ELEMENT_NODE || in_array(strtolower($child->nodeName), self::SKIP_TAGS, true)) {
                continue;
            }

            self::collectTextNodes($child, $textNodes);
        }
    }

    private static function validUrl(string $url): ?string
    {
        $url = trim($url);
        if (preg_match('#^/(?!/)[^\s]*$#u', $url)) {
            return $url;
        }

        return filter_var($url, FILTER_VALIDATE_URL) && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)
            ? $url
            : null;
    }
}
