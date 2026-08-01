<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class PrivacyPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://lanyardbekasi.com',
            'cache.public_store' => 'array',
        ]);
    }

    public function test_privacy_page_uses_the_public_design_system_and_complete_policy(): void
    {
        $response = $this->get('/kebijakan');

        $response
            ->assertOk()
            ->assertSee('Kebijakan Privasi yang Jelas dan Transparan')
            ->assertSee('/css/service-pages.css', false)
            ->assertSee('/css/privacy-index.css', false)
            ->assertSee('<link rel="canonical" href="https://lanyardbekasi.com/kebijakan"', false)
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('Halo%20Lanyard%20Bekasi%2C%20saya%20ingin%20bertanya%20mengenai%20kebijakan%20privasi%20dan%20data%20saya.', false)
            ->assertSee('Bekasi, Indonesia')
            ->assertDontSee('Lanyard Indonesia')
            ->assertDontSee('Indonesia Kota')
            ->assertDontSee('Jl. Sudirman No. 123');

        [, $xpath] = $this->dom($response->getContent());

        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(2, $xpath->query('//section[@id="privacy-hero"]//div[contains(concat(" ", normalize-space(@class), " "), " info-actions ")]/a')->length);
        $this->assertSame(4, $xpath->query('//section[contains(concat(" ", normalize-space(@class), " "), " privacy-trust ")]//ul/li')->length);
        $this->assertSame(7, $xpath->query('//aside[contains(concat(" ", normalize-space(@class), " "), " privacy-toc ")]//nav/a')->length);
        $this->assertSame(7, $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " privacy-document ")]/article')->length);
        $this->assertSame(4, $xpath->query('//ul[contains(concat(" ", normalize-space(@class), " "), " privacy-security ")]/li')->length);
        $this->assertSame(0, $xpath->query('//section[@id="kontak-privasi"]')->length);
        $this->assertSame(1, $xpath->query('//section[contains(concat(" ", normalize-space(@class), " "), " privacy-cta ")]//a[contains(@href, "wa.me")]')->length);
    }

    public function test_privacy_page_exposes_webpage_and_breadcrumb_schema(): void
    {
        $response = $this->get('/kebijakan')->assertOk();
        $schemas = $this->schemas($response->getContent());
        $page = collect($schemas)->firstWhere('@type', 'WebPage');
        $breadcrumb = collect($schemas)->firstWhere('@type', 'BreadcrumbList');

        $this->assertNotNull($page);
        $this->assertSame('https://lanyardbekasi.com/kebijakan', $page['url']);
        $this->assertSame('id-ID', $page['inLanguage']);
        $this->assertNotNull($breadcrumb);
        $this->assertSame('https://lanyardbekasi.com/kebijakan', $breadcrumb['itemListElement'][1]['item']);
    }

    public function test_privacy_styles_keep_spacing_accessibility_and_mobile_layout(): void
    {
        $css = file_get_contents(public_path('css/privacy-index.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('padding: 80px 0;', $css);
        $this->assertStringContainsString('border-radius: 16px;', $css);
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
}
