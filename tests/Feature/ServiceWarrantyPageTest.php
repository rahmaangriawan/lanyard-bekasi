<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class ServiceWarrantyPageTest extends TestCase
{
    public function test_services_page_uses_the_public_design_system_and_expected_content(): void
    {
        $response = $this->get('/layanan');

        $response
            ->assertOk()
            ->assertSee('Layanan Cetak Lanyard Custom untuk Berbagai Kebutuhan')
            ->assertSee('Halo%20Lanyard%20Bekasi%2C%20saya%20mau%20konsultasi%20layanan%20lanyard%20custom.', false)
            ->assertSee('href="'.route('products.index').'"', false)
            ->assertSee('href="'.route('ordering').'"', false)
            ->assertSee('/css/service-pages.css', false)
            ->assertSee('/uploads/hero-layanan-lanyard-bekasi.webp', false)
            ->assertDontSee('Pusat Tali Lanyard')
            ->assertDontSee('â€”');

        [$document, $xpath] = $this->dom($response->getContent());
        $root = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " services-page ")]')->item(0);

        $this->assertNotNull($root);
        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(8, $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " services-page ")]/section')->length);
        $this->assertSame(4, $xpath->query('//section[@id="kemampuan-layanan"]//ul/li')->length);
        $this->assertSame(4, $xpath->query('//section[contains(concat(" ", normalize-space(@class), " "), " info-process ")]//ol/li')->length);
        $this->assertSame(4, $xpath->query('//section[@id="faq-layanan"]//details')->length);
        $this->assertSame(0, $xpath->query('//section[@id="faq-layanan"]//details[@open]')->length);
        $this->assertSame(
            1,
            $xpath->query('//section[@id="services-hero"]//img[@src="/uploads/hero-layanan-lanyard-bekasi.webp" and @width="1448" and @height="1086" and @fetchpriority="high"]')->length
        );
        $this->assertSame(
            2,
            $xpath->query('//header[@id="site-header"]//a[@href="'.route('services').'" and @aria-current="page"]')->length
        );

        $this->assertSectionOrder($response->getContent(), [
            'services-hero',
            'info-trust',
            'info-capabilities',
            'info-audiences',
            'info-customization',
            'info-process',
            'info-faq',
            'info-cta',
        ]);
    }

    public function test_warranty_page_matches_the_published_terms_and_claim_flow(): void
    {
        $response = $this->get('/garansi');

        $response
            ->assertOk()
            ->assertSee('Garansi yang Jelas untuk Hasil Produksi Anda')
            ->assertSee('Halo%20Lanyard%20Bekasi%2C%20saya%20ingin%20mengajukan%20klaim%20garansi%20untuk%20pesanan%20lanyard.', false)
            ->assertSee('href="'.route('terms').'"', false)
            ->assertSee('3x24 jam')
            ->assertSee('5 sampai 10 persen')
            ->assertSee('1 sampai 2 mm')
            ->assertSee('foto dan video unboxing')
            ->assertSee('follow-up dan klaim asuransi')
            ->assertDontSee('penggantian otomatis')
            ->assertDontSee('â€”');

        [$document, $xpath] = $this->dom($response->getContent());
        $root = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " warranty-page ")]')->item(0);

        $this->assertNotNull($root);
        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(7, $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " warranty-page ")]/section')->length);
        $this->assertSame(3, $xpath->query('//section[@id="cakupan-garansi"]//article[contains(@class, "warranty-coverage__panel--covered")]//li')->length);
        $this->assertSame(5, $xpath->query('//section[@id="cakupan-garansi"]//article[contains(@class, "warranty-coverage__panel--excluded")]//li')->length);
        $this->assertSame(4, $xpath->query('//section[@id="alur-klaim"]//ol/li')->length);
        $this->assertSame(4, $xpath->query('//section[@id="faq-garansi"]//details')->length);
        $this->assertSame(0, $xpath->query('//section[@id="faq-garansi"]//details[@open]')->length);
        $this->assertSame(
            1,
            $xpath->query('//section[@id="warranty-hero"]//img[@src="/uploads/hero-garansi-lanyard-bekasi.webp" and @width="1448" and @height="1086" and @fetchpriority="high"]')->length
        );
        $this->assertSame(
            2,
            $xpath->query('//header[@id="site-header"]//a[@href="'.route('warranty').'" and @aria-current="page"]')->length
        );

        $this->assertSectionOrder($response->getContent(), [
            'warranty-hero',
            'info-trust',
            'warranty-coverage',
            'warranty-quality',
            'warranty-claim',
            'info-faq',
            'info-cta',
        ]);
    }

    public function test_services_and_warranty_expose_matching_structured_data(): void
    {
        $services = $this->get('/layanan')->assertOk();
        $servicesSchemas = $this->schemas($services->getContent());
        $service = collect($servicesSchemas)->firstWhere('@type', 'Service');
        $serviceBreadcrumb = collect($servicesSchemas)->firstWhere('@type', 'BreadcrumbList');
        $serviceFaq = collect($servicesSchemas)->firstWhere('@type', 'FAQPage');

        $this->assertNotNull($service);
        $this->assertSame('https://lanyardbekasi.com/layanan', $service['url']);
        $this->assertSame('https://lanyardbekasi.com/layanan', $serviceBreadcrumb['itemListElement'][1]['item']);
        $this->assertCount(4, $serviceFaq['mainEntity']);

        $warranty = $this->get('/garansi')->assertOk();
        $warrantySchemas = $this->schemas($warranty->getContent());
        $page = collect($warrantySchemas)->firstWhere('@type', 'WebPage');
        $warrantyBreadcrumb = collect($warrantySchemas)->firstWhere('@type', 'BreadcrumbList');
        $warrantyFaq = collect($warrantySchemas)->firstWhere('@type', 'FAQPage');

        $this->assertNotNull($page);
        $this->assertSame('https://lanyardbekasi.com/garansi', $page['url']);
        $this->assertSame('https://lanyardbekasi.com/garansi', $warrantyBreadcrumb['itemListElement'][1]['item']);
        $this->assertCount(4, $warrantyFaq['mainEntity']);

        foreach ($serviceFaq['mainEntity'] as $entity) {
            $this->assertStringContainsString($entity['name'], $services->getContent());
            $this->assertStringContainsString($entity['acceptedAnswer']['text'], $services->getContent());
        }

        foreach ($warrantyFaq['mainEntity'] as $entity) {
            $this->assertStringContainsString($entity['name'], $warranty->getContent());
            $this->assertStringContainsString($entity['acceptedAnswer']['text'], $warranty->getContent());
        }
    }

    public function test_shared_styles_keep_homepage_tokens_and_eighty_pixel_spacing(): void
    {
        $css = file_get_contents(public_path('css/service-pages.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('--info-section-space: 80px;', $css);
        $this->assertStringContainsString('--info-green: #3f8c1b;', $css);
        $this->assertStringContainsString('--info-dark: #073b29;', $css);
        $this->assertStringContainsString('padding: var(--info-section-space) 0;', $css);
        $this->assertStringContainsString('min-height: 50px;', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
    }

    private function dom(string $html): array
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();

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

    private function assertSectionOrder(string $html, array $classNames): void
    {
        $lastPosition = -1;
        foreach ($classNames as $className) {
            $position = strpos($html, $className);
            $this->assertNotFalse($position, $className);
            $this->assertGreaterThan($lastPosition, $position, $className);
            $lastPosition = $position;
        }
    }
}
