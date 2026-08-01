<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIndexPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://lanyardbekasi.com',
            'cache.public_store' => 'array',
        ]);
    }

    public function test_product_index_uses_the_public_catalog_design_and_live_data(): void
    {
        $response = $this->get('/produk');

        $response
            ->assertOk()
            ->assertSee('Temukan Lanyard yang Tepat untuk Kebutuhan Anda')
            ->assertSee('/css/product-index.css', false)
            ->assertSee('Katalog Lanyard dan Aksesori')
            ->assertSee('Bantuan Desain')
            ->assertSee('Spesifikasi Fleksibel')
            ->assertSee('Quality Control')
            ->assertSee('Pengiriman Luas')
            ->assertSee('Belum Yakin Produk Mana yang Paling Sesuai?')
            ->assertSee('Halo%20Lanyard%20Bekasi%2C%20saya%20ingin%20dibantu%20memilih%20produk%20lanyard%20custom%20yang%20sesuai.', false)
            ->assertSee('href="'.route('ordering').'"', false)
            ->assertDontSee('â€œ')
            ->assertDontSee('â€')
            ->assertDontSee('Lanyard Indonesia');

        [$document, $xpath] = $this->dom($response->getContent());

        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(1, $xpath->query('//form[@role="search"]//input[@type="search"]')->length);
        $this->assertSame(3, $xpath->query('//ul[contains(@class, "product-index__hero-facts")]/li')->length);
        $this->assertSame(4, $xpath->query('//ul[contains(@class, "product-index__trust-list")]/li')->length);
        $this->assertGreaterThan(0, $xpath->query('//ul[contains(@class, "product-index__grid")]/li')->length);
        $this->assertSame(1, $xpath->query('//section[contains(@class, "product-index__cta")]//a[contains(@href, "wa.me")]')->length);

        $heroImage = $xpath->query('//div[contains(@class, "product-index__visual-frame")]/img')->item(0);
        $this->assertNotNull($heroImage);
        $this->assertSame('1448', $heroImage->getAttribute('width'));
        $this->assertSame('1086', $heroImage->getAttribute('height'));

        $this->assertSectionOrder($document->saveHTML(), [
            'product-index__hero',
            'product-index__trust',
            'product-index__catalog',
            'product-index__cta',
        ]);
    }

    public function test_product_search_result_is_not_indexable_and_uses_readable_quotes(): void
    {
        $response = $this->get('/produk?q=produk');

        $response
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('hasil untuk “produk”')
            ->assertDontSee('â€œ')
            ->assertDontSee('â€');
    }

    public function test_product_styles_keep_accessibility_and_mobile_readability(): void
    {
        $css = file_get_contents(public_path('css/product-index.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('width: min(calc(100% - 40px), 1180px);', $css);
        $this->assertStringContainsString('padding: 80px 0;', $css);
        $this->assertStringContainsString('border-radius: 16px;', $css);
        $this->assertStringContainsString('min-height: 44px;', $css);
        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('@media (max-width: 767px)', $css);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $css);
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
