<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class OrderingPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://lanyardbekasi.com',
            'cache.public_store' => 'array',
        ]);
    }

    public function test_ordering_page_uses_the_public_design_system_and_six_step_flow(): void
    {
        $response = $this->get('/cara-pemesanan');

        $response
            ->assertOk()
            ->assertSee('Pesan Lanyard Custom dengan Alur yang Jelas')
            ->assertSee('Halo%20Lanyard%20Bekasi%2C%20saya%20mau%20konsultasi%20pemesanan%20lanyard%20custom.', false)
            ->assertSee('href="'.route('products.index').'"', false)
            ->assertSee('/css/service-pages.css', false)
            ->assertSee('/css/ordering-index.css', false)
            ->assertSee('<link rel="preload" href="/uploads/hero-cara-pemesanan-lanyard-bekasi.webp" as="image" type="image/webp" fetchpriority="high">', false)
            ->assertSee('<link rel="canonical" href="https://lanyardbekasi.com/cara-pemesanan"', false)
            ->assertDontSee('dalam 5 Langkah')
            ->assertDontSee('Pusat Tali Lanyard');

        [, $xpath] = $this->dom($response->getContent());

        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(2, $xpath->query('//section[@id="ordering-hero"]//div[contains(concat(" ", normalize-space(@class), " "), " info-actions ")]/a')->length);
        $this->assertSame(1, $xpath->query('//section[@id="ordering-hero"]//img[@src="/uploads/hero-cara-pemesanan-lanyard-bekasi.webp" and @width="1448" and @height="1086" and @fetchpriority="high"]')->length);
        $this->assertSame(4, $xpath->query('//section[@id="ordering-trust"]//ul/li')->length);
        $this->assertSame(6, $xpath->query('//section[@id="ordering-process"]//ol/li')->length);
        $this->assertSame(0, $xpath->query('//section[@id="ordering-process"]//ol/li/span[normalize-space(.)="01" or normalize-space(.)="02" or normalize-space(.)="03" or normalize-space(.)="04" or normalize-space(.)="05" or normalize-space(.)="06"]')->length);
        $this->assertSame(5, $xpath->query('//aside[contains(concat(" ", normalize-space(@class), " "), " ordering-preparation ")]//ul/li')->length);
        $this->assertSame(4, $xpath->query('//section[@id="ordering-faq"]//details')->length);
        $this->assertSame(0, $xpath->query('//section[@id="ordering-faq"]//details[@open]')->length);
        $this->assertSame(1, $xpath->query('//section[@id="ordering-cta"]//a[contains(@href, "wa.me")]')->length);

        $this->assertSectionOrder($response->getContent(), [
            'id="ordering-hero"',
            'id="ordering-trust"',
            'id="ordering-process"',
            'id="ordering-faq"',
            'id="ordering-cta"',
        ]);
    }

    public function test_ordering_page_exposes_breadcrumb_howto_and_visible_faq_schema(): void
    {
        $response = $this->get('/cara-pemesanan')->assertOk();
        $schemas = $this->schemas($response->getContent());
        $breadcrumb = collect($schemas)->firstWhere('@type', 'BreadcrumbList');
        $howTo = collect($schemas)->firstWhere('@type', 'HowTo');
        $faqSchema = collect($schemas)->firstWhere('@type', 'FAQPage');

        $this->assertNotNull($breadcrumb);
        $this->assertSame('https://lanyardbekasi.com/cara-pemesanan', $breadcrumb['itemListElement'][1]['item']);
        $this->assertNotNull($howTo);
        $this->assertSame('Cara Pemesanan Lanyard Custom dalam 6 Langkah', $howTo['name']);
        $this->assertSame('https://lanyardbekasi.com/uploads/hero-cara-pemesanan-lanyard-bekasi.webp', $howTo['image']);
        $this->assertCount(6, $howTo['step']);
        $this->assertSame(range(1, 6), collect($howTo['step'])->pluck('position')->all());
        $this->assertNotNull($faqSchema);
        $this->assertSame('https://lanyardbekasi.com/cara-pemesanan', $faqSchema['url']);
        $this->assertCount(4, $faqSchema['mainEntity']);
        $this->assertSame(
            collect(config('seo.faq'))->pluck('question')->all(),
            collect($faqSchema['mainEntity'])->pluck('name')->all()
        );
    }

    public function test_ordering_styles_keep_spacing_focus_and_responsive_behavior(): void
    {
        $css = file_get_contents(public_path('css/ordering-index.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('padding: 80px 0;', $css);
        $this->assertStringContainsString('border-radius: 16px;', $css);
        $this->assertStringContainsString('min-height: 54px;', $css);
        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('@media (max-width: 767px)', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
    }

    private function dom(string $html): array
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return [$document, new DOMXPath($document)];
    }

    private function schemas(string $html): array
    {
        [, $xpath] = $this->dom($html);
        $schemas = [];

        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $decoded = json_decode($script->textContent, true);
            if (is_array($decoded)) {
                $schemas[] = $decoded;
            }
        }

        return $schemas;
    }

    private function assertSectionOrder(string $html, array $markers): void
    {
        $lastPosition = -1;

        foreach ($markers as $marker) {
            $position = strpos($html, $marker);
            $this->assertNotFalse($position, $marker);
            $this->assertGreaterThan($lastPosition, $position, $marker);
            $lastPosition = $position;
        }
    }
}
