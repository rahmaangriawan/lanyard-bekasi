<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class ShippingPageTest extends TestCase
{
    public function test_shipping_page_uses_lanyard_bekasi_content_and_homepage_design_structure(): void
    {
        $response = $this->get('/pengiriman');

        $response
            ->assertOk()
            ->assertSee('Pengiriman Aman dan Terpantau ke Seluruh Indonesia')
            ->assertSee('Halo%20Lanyard%20Bekasi%2C%20saya%20mau%20konsultasi%20pengiriman%20lanyard%20custom.', false)
            ->assertSee('href="'.route('area').'"', false)
            ->assertSee('/css/shipping-index.css', false)
            ->assertSee('/uploads/lanyard-bekasi-cta.webp', false)
            ->assertDontSee('Indonesia Raya')
            ->assertDontSee('Kota Indonesia')
            ->assertDontSee('Lanyard Indonesia')
            ->assertDontSee('mengganti/mencetak ulang')
            ->assertDontSee('â');

        $html = $response->getContent();
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();
        $xpath = new DOMXPath($document);
        $shippingRoot = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " shipping-page ")]')->item(0);

        $this->assertNotNull($shippingRoot);
        $this->assertStringNotContainsString('—', $shippingRoot->textContent);
        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(7, $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " shipping-page ")]/section')->length);
        $this->assertSame(4, $xpath->query('//ul[contains(concat(" ", normalize-space(@class), " "), " shipping-trust__list ")]/li')->length);
        $this->assertSame(2, $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " shipping-services__grid ")]/article')->length);
        $this->assertSame(4, $xpath->query('//ol[contains(concat(" ", normalize-space(@class), " "), " shipping-process__list ")]/li')->length);
        $this->assertSame(0, $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " shipping-process__number ")]')->length);
        $this->assertSame(4, $xpath->query('//section[@id="estimasi-pengiriman"]//tbody/tr')->length);
        $this->assertSame(4, $xpath->query('//section[@id="faq-pengiriman"]//details')->length);
        $this->assertSame(0, $xpath->query('//section[@id="faq-pengiriman"]//details[@open]')->length);
        $this->assertSame(
            1,
            $xpath->query('//section[@id="shipping-hero"]//img[@src="/uploads/lanyard-bekasi-cta.webp" and @width="941" and @height="1672" and @fetchpriority="high"]')->length
        );
        $this->assertSame(
            1,
            $xpath->query('//a[@href="'.route('area').'" and contains(normalize-space(.), "Lihat Area Layanan")]')->length
        );

        $sectionOrder = [
            'shipping-hero',
            'shipping-trust',
            'shipping-services',
            'shipping-process',
            'shipping-estimates',
            'shipping-faq',
            'shipping-cta',
        ];
        $lastPosition = -1;
        foreach ($sectionOrder as $className) {
            $position = strpos($html, $className);
            $this->assertNotFalse($position);
            $this->assertGreaterThan($lastPosition, $position);
            $lastPosition = $position;
        }
    }

    public function test_shipping_page_exposes_breadcrumb_and_visible_faq_schema(): void
    {
        $response = $this->get('/pengiriman')->assertOk();
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($response->getContent());
        libxml_clear_errors();
        $xpath = new DOMXPath($document);

        $schemas = [];
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $decoded = json_decode($script->textContent, true);
            if (is_array($decoded)) {
                $schemas[] = $decoded;
            }
        }

        $breadcrumb = collect($schemas)->firstWhere('@type', 'BreadcrumbList');
        $faq = collect($schemas)->firstWhere('@type', 'FAQPage');

        $this->assertNotNull($breadcrumb);
        $this->assertSame('https://lanyardbekasi.com/pengiriman', $breadcrumb['itemListElement'][1]['item']);
        $this->assertNotNull($faq);
        $this->assertCount(4, $faq['mainEntity']);
        $this->assertSame(
            'Apakah semua pengiriman ke Jabodetabek gratis?',
            $faq['mainEntity'][0]['name']
        );

        foreach ($faq['mainEntity'] as $entity) {
            $this->assertStringContainsString(
                $entity['name'],
                $response->getContent()
            );
            $this->assertStringContainsString(
                $entity['acceptedAnswer']['text'],
                $response->getContent()
            );
        }
    }

    public function test_shipping_styles_keep_homepage_tokens_and_eighty_pixel_section_spacing(): void
    {
        $css = file_get_contents(public_path('css/shipping-index.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('--shipping-section-space: 80px;', $css);
        $this->assertStringContainsString('--shipping-green: #3f8c1b;', $css);
        $this->assertStringContainsString('--shipping-dark: #073b29;', $css);
        $this->assertStringContainsString('padding: var(--shipping-section-space) 0;', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
    }
}
