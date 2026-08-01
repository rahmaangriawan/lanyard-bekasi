<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class FaqPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://lanyardbekasi.com',
            'cache.public_store' => 'array',
        ]);
    }

    public function test_faq_page_has_three_categories_and_twelve_closed_questions(): void
    {
        $response = $this->get('/faq');

        $response
            ->assertOk()
            ->assertSee('Jawaban untuk Pertanyaan Seputar Lanyard Custom')
            ->assertSee('Halo%20Lanyard%20Bekasi%2C%20saya%20masih%20punya%20pertanyaan%20tentang%20lanyard%20custom.', false)
            ->assertSee('href="'.route('products.index').'"', false)
            ->assertSee('href="'.route('services').'"', false)
            ->assertSee('href="'.route('shipping').'"', false)
            ->assertSee('href="'.route('warranty').'"', false)
            ->assertSee('/css/service-pages.css', false)
            ->assertSee('/css/faq-index.css', false)
            ->assertSee('<link rel="canonical" href="https://lanyardbekasi.com/faq"', false)
            ->assertDontSee('Pusat Tali Lanyard');

        [, $xpath] = $this->dom($response->getContent());

        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(3, $xpath->query('//nav[@id="topik-faq"]//ul/li')->length);
        $this->assertSame(3, $xpath->query('//section[contains(concat(" ", normalize-space(@class), " "), " faq-category ")]')->length);
        $this->assertSame(4, $xpath->query('//section[@id="produk-layanan"]//details')->length);
        $this->assertSame(4, $xpath->query('//section[@id="pengiriman"]//details')->length);
        $this->assertSame(4, $xpath->query('//section[@id="garansi"]//details')->length);
        $this->assertSame(12, $xpath->query('//section[contains(concat(" ", normalize-space(@class), " "), " faq-category ")]//details')->length);
        $this->assertSame(0, $xpath->query('//section[contains(concat(" ", normalize-space(@class), " "), " faq-category ")]//details[@open]')->length);
        $this->assertSame(
            1,
            $xpath->query('//section[@id="faq-hero"]//img[@src="/uploads/hero-faq-lanyard-bekasi.webp" and @width="1448" and @height="1086" and @fetchpriority="high"]')->length
        );
        $this->assertSame(
            2,
            $xpath->query('//header[@id="site-header"]//a[@href="'.route('faq').'" and @aria-current="page"]')->length
        );
        $this->assertGreaterThanOrEqual(
            1,
            $xpath->query('//footer[@id="site-footer"]//a[@href="'.route('faq').'"]')->length
        );
        $this->assertSame(1, $xpath->query('//footer[@id="site-footer"]//*[normalize-space(.)="Bekasi, Indonesia"]')->length);
        $this->assertSame(0, $xpath->query('//footer[@id="site-footer"]//a[contains(normalize-space(.), "Konsultasi via WhatsApp")]')->length);
        $this->assertSame(1, $xpath->query('//a[@id="floating-whatsapp"]')->length);

        $this->assertSectionOrder($response->getContent(), [
            'faq-hero',
            'faq-topics',
            'id="produk-layanan"',
            'id="pengiriman"',
            'id="garansi"',
            'faq-cta',
        ]);
    }

    public function test_faq_schema_contains_exactly_the_twelve_visible_questions(): void
    {
        $response = $this->get('/faq')->assertOk();
        $schemas = $this->schemas($response->getContent());
        $breadcrumb = collect($schemas)->firstWhere('@type', 'BreadcrumbList');
        $faqSchema = collect($schemas)->firstWhere('@type', 'FAQPage');

        $this->assertNotNull($breadcrumb);
        $this->assertSame('https://lanyardbekasi.com/faq', $breadcrumb['itemListElement'][1]['item']);
        $this->assertNotNull($faqSchema);
        $this->assertSame('https://lanyardbekasi.com/faq', $faqSchema['url']);
        $this->assertCount(12, $faqSchema['mainEntity']);

        $expectedFaqs = collect([
            ...config('seo.faq'),
            ...config('seo.shipping_faq'),
            ...config('seo.warranty_faq'),
        ]);

        $this->assertSame(
            $expectedFaqs->pluck('question')->all(),
            collect($faqSchema['mainEntity'])->pluck('name')->all()
        );

        foreach ($faqSchema['mainEntity'] as $entity) {
            $this->assertStringContainsString($entity['name'], $response->getContent());
            $this->assertStringContainsString($entity['acceptedAnswer']['text'], $response->getContent());
        }
    }

    public function test_related_pages_reuse_the_same_faq_configuration(): void
    {
        $pages = [
            '/layanan' => config('seo.faq'),
            '/pengiriman' => config('seo.shipping_faq'),
            '/garansi' => config('seo.warranty_faq'),
        ];

        foreach ($pages as $uri => $faqs) {
            $response = $this->get($uri)->assertOk();

            foreach ($faqs as $faq) {
                $response
                    ->assertSee($faq['question'])
                    ->assertSee($faq['answer']);
            }
        }
    }

    public function test_faq_styles_keep_spacing_accessibility_and_responsive_layout(): void
    {
        $css = file_get_contents(public_path('css/faq-index.css'));
        $floatingCss = file_get_contents(public_path('css/floating-whatsapp.css'));
        $floatingJs = file_get_contents(public_path('js/floating-whatsapp.js'));

        $this->assertIsString($css);
        $this->assertIsString($floatingCss);
        $this->assertIsString($floatingJs);
        $this->assertStringContainsString('padding: 80px 0;', $css);
        $this->assertStringContainsString('border-radius: 16px;', $css);
        $this->assertStringContainsString('min-height: 44px;', $css);
        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('@media (max-width: 767px)', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        $this->assertStringContainsString('@keyframes floating-whatsapp-pulse', $floatingCss);
        $this->assertStringContainsString('animation: none;', $floatingCss);
        $this->assertStringNotContainsString('is-footer-visible', $floatingCss);
        $this->assertStringNotContainsString('IntersectionObserver', $floatingJs);
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
