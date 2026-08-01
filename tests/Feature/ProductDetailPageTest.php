<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://lanyardbekasi.com',
            'app.prod_url' => 'https://lanyardbekasi.com',
            'cache.public_store' => 'array',
        ]);
    }

    public function test_product_detail_uses_the_marketplace_design_and_live_data(): void
    {
        $category = Category::query()->create([
            'name' => 'Lanyard Printing',
            'slug' => 'lanyard-printing',
            'type' => 'PRODUCT',
        ]);

        $product = Product::query()->create([
            'sku' => 'LB-DETAIL-01',
            'name' => 'Lanyard Printing dengan Stopper',
            'slug' => 'lanyard-printing-dengan-stopper',
            'specs' => json_encode(['Lebar tali 2 cm', 'Cetak full color']),
            'accessories' => "Stopper pengaman\nKait ring O",
            'basePrice' => 'Rp 17.000',
            'minOrder' => '50 pcs',
            'shortDescription' => 'Lanyard custom untuk kebutuhan kantor dan event.',
            'featuredImage' => '/uploads/aset-produk.webp',
            'categoryId' => $category->id,
            'description' => '<p>Produk IndoLanyard dengan hasil cetak tajam dan pilihan aksesori fleksibel.</p>',
            'published' => true,
            'sortOrder' => 1,
            'metaTitle' => 'Lanyard Printing Custom',
            'metaDescription' => 'Lanyard printing custom dengan pilihan aksesori.',
            'createdAt' => now(),
        ]);

        foreach (range(2, 6) as $index) {
            Product::query()->create([
                'sku' => 'LB-RELATED-'.$index,
                'name' => 'Produk Terkait '.$index,
                'slug' => 'produk-terkait-'.$index,
                'basePrice' => 'Rp '.($index * 10).'.000',
                'categoryId' => $index < 5 ? $category->id : null,
                'published' => true,
                'sortOrder' => $index,
                'createdAt' => now(),
            ]);
        }

        $response = $this->get(route('products.show', $product->slug));

        $response
            ->assertOk()
            ->assertSee('/css/product-detail.css', false)
            ->assertSee('Lanyard Printing dengan Stopper')
            ->assertSee('Rp 17.000')
            ->assertSee('50 pcs')
            ->assertSee('LB-DETAIL-01')
            ->assertSee('Deskripsi Produk')
            ->assertSee('Lebar tali 2 cm')
            ->assertSee('Stopper pengaman')
            ->assertSee('Pesan Custom dalam 4 Tahap')
            ->assertSee('Produk Serupa')
            ->assertSee('Pesan via WhatsApp')
            ->assertSee('Lanyard Bekasi dengan hasil cetak tajam')
            ->assertDontSee('IndoLanyard')
            ->assertDontSee('5.0')
            ->assertDontSee('12 ulasan')
            ->assertDontSee('Rp12.900')
            ->assertDontSee('/vendor/splide/', false)
            ->assertSee('<link rel="canonical" href="https://lanyardbekasi.com/produk/lanyard-printing-dengan-stopper">', false);

        [$document, $xpath] = $this->dom($response->getContent());

        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(3, $xpath->query('//nav[contains(@class, "product-detail__breadcrumb")]/ol/li')->length);
        $this->assertSame(4, $xpath->query('//section[contains(@class, "product-detail__trust")]//ul/li')->length);
        $this->assertSame(2, $xpath->query('//section[@id="spesifikasi-produk"]//ul/li')->length);
        $this->assertSame(2, $xpath->query('//section[@id="aksesori-produk"]//ul/li')->length);
        $this->assertSame(4, $xpath->query('//section[@id="alur-pemesanan"]//ol/li')->length);
        $this->assertSame(4, $xpath->query('//ul[contains(@class, "product-detail__related-list")]/li')->length);

        $heroImage = $xpath->query('//figure[contains(@class, "product-detail__media")]/img')->item(0);
        $this->assertNotNull($heroImage);
        $this->assertSame('1200', $heroImage->getAttribute('width'));
        $this->assertSame('900', $heroImage->getAttribute('height'));
        $this->assertSame('high', $heroImage->getAttribute('fetchpriority'));

        $this->assertSectionOrder($document->saveHTML(), [
            'product-detail__hero',
            'product-detail__trust',
            'product-detail__dossier',
            'product-detail__process',
            'product-detail__related',
            'product-detail__cta',
        ]);

        $productSchema = $this->structuredDataByType($xpath, 'Product');
        $this->assertSame('Lanyard Printing dengan Stopper', $productSchema['name']);
        $this->assertSame('LB-DETAIL-01', $productSchema['sku']);
        $this->assertSame('17000', $productSchema['offers']['price']);
        $this->assertSame('Lanyard Bekasi', $productSchema['brand']['name']);
        $this->assertArrayNotHasKey('aggregateRating', $productSchema);
        $this->assertArrayNotHasKey('availability', $productSchema['offers']);

        $breadcrumbSchema = $this->structuredDataByType($xpath, 'BreadcrumbList');
        $this->assertCount(3, $breadcrumbSchema['itemListElement']);
    }

    public function test_product_detail_handles_minimal_product_data_without_fake_claims(): void
    {
        $product = Product::query()->create([
            'sku' => 'LB-MINIMAL-01',
            'name' => 'Lanyard Minimal',
            'slug' => 'lanyard-minimal',
            'published' => true,
            'createdAt' => now(),
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response
            ->assertOk()
            ->assertSee('Minta quotation')
            ->assertSee('/uploads/aset-produk.webp', false)
            ->assertSee('Informasi bahan, ukuran, aksesori, dan kebutuhan produksi')
            ->assertDontSee('Minimum pesanan')
            ->assertDontSee('Spesifikasi</h3>', false)
            ->assertDontSee('Aksesori</h3>', false)
            ->assertDontSee('Pertimbangkan Produk Lain')
            ->assertDontSee('5.0')
            ->assertDontSee('12 ulasan')
            ->assertDontSee('Rp12.900');
    }

    public function test_product_detail_styles_preserve_spacing_accessibility_and_mobile_scroll_snap(): void
    {
        $css = file_get_contents(public_path('css/product-detail.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('--detail-shell: 1180px;', $css);
        $this->assertStringContainsString('padding: 80px 0;', $css);
        $this->assertStringContainsString('border-radius: 16px;', $css);
        $this->assertStringContainsString('min-height: 48px;', $css);
        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('scroll-snap-type: x mandatory;', $css);
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

    private function structuredDataByType(DOMXPath $xpath, string $type): array
    {
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $data = json_decode((string) $script->textContent, true);

            if (is_array($data) && ($data['@type'] ?? null) === $type) {
                return $data;
            }
        }

        $this->fail("Structured data {$type} was not found.");
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
