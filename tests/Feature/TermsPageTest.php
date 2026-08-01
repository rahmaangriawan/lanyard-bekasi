<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class TermsPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://lanyardbekasi.com',
            'cache.public_store' => 'array',
        ]);
    }

    public function test_terms_page_uses_the_public_design_system_and_complete_terms(): void
    {
        $response = $this->get('/syarat-ketentuan');

        $response
            ->assertOk()
            ->assertSee('Syarat yang Jelas Sebelum Pesanan Diproduksi')
            ->assertSee('/css/service-pages.css', false)
            ->assertSee('/css/terms-index.css', false)
            ->assertSee('<link rel="canonical" href="https://lanyardbekasi.com/syarat-ketentuan"', false)
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('Halo%20Lanyard%20Bekasi%2C%20saya%20ingin%20berkonsultasi%20mengenai%20syarat%20pemesanan%20lanyard%20custom.', false)
            ->assertSee('href="'.route('ordering').'"', false)
            ->assertSee('href="'.route('shipping').'"', false)
            ->assertSee('href="'.route('warranty').'"', false)
            ->assertSee('DP minimal 50%')
            ->assertSee('5 sampai 10 persen')
            ->assertSee('1 sampai 2 mm')
            ->assertSee('3x24 jam')
            ->assertSee('foto dan video unboxing')
            ->assertSee('follow-up dan klaim asuransi')
            ->assertDontSee('ganti baru 100%')
            ->assertDontSee('penggantian otomatis')
            ->assertDontSee('Lanyard Indonesia')
            ->assertDontSee('Â');

        [, $xpath] = $this->dom($response->getContent());

        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(2, $xpath->query('//section[@id="terms-hero"]//div[contains(concat(" ", normalize-space(@class), " "), " info-actions ")]/a')->length);
        $this->assertSame(4, $xpath->query('//section[contains(concat(" ", normalize-space(@class), " "), " terms-trust ")]//ul/li')->length);
        $this->assertSame(4, $xpath->query('//section[@id="ringkasan-syarat"]//ul/li')->length);
        $this->assertSame(7, $xpath->query('//aside[contains(concat(" ", normalize-space(@class), " "), " terms-toc ")]//nav/a')->length);
        $this->assertSame(7, $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " terms-document ")]/article')->length);
        $this->assertSame(1, $xpath->query('//section[@id="terms-cta"]//a[contains(@href, "wa.me")]')->length);

        $this->assertSectionOrder($response->getContent(), [
            'id="terms-hero"',
            'terms-trust',
            'id="ringkasan-syarat"',
            'id="isi-ketentuan"',
            'id="terms-cta"',
        ]);
    }

    public function test_terms_page_exposes_webpage_and_breadcrumb_schema(): void
    {
        $response = $this->get('/syarat-ketentuan')->assertOk();
        $schemas = $this->schemas($response->getContent());
        $page = collect($schemas)->firstWhere('@type', 'WebPage');
        $breadcrumb = collect($schemas)->firstWhere('@type', 'BreadcrumbList');

        $this->assertNotNull($page);
        $this->assertSame('https://lanyardbekasi.com/syarat-ketentuan', $page['url']);
        $this->assertSame('id-ID', $page['inLanguage']);
        $this->assertSame('2026-07-31', $page['dateModified']);
        $this->assertNotNull($breadcrumb);
        $this->assertSame('https://lanyardbekasi.com/syarat-ketentuan', $breadcrumb['itemListElement'][1]['item']);
    }

    public function test_terms_styles_keep_spacing_accessibility_and_responsive_layout(): void
    {
        $css = file_get_contents(public_path('css/terms-index.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('padding: 80px 0;', $css);
        $this->assertStringContainsString('border-radius: 16px;', $css);
        $this->assertStringContainsString('position: sticky;', $css);
        $this->assertStringContainsString('min-height: 44px;', $css);
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
