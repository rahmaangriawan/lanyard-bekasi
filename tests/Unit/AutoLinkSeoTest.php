<?php

namespace Tests\Unit;

use App\Support\AutoLinkSeo;
use PHPUnit\Framework\TestCase;

class AutoLinkSeoTest extends TestCase
{
    public function test_it_injects_configured_links_without_touching_existing_links_or_headings(): void
    {
        $html = '<h2>Lanyard custom</h2><p>Lanyard custom Indonesia tersedia. <a href="/lama">Lanyard custom</a></p><p>Lanyard untuk instansi.</p>';

        $result = AutoLinkSeo::inject($html, [
            ['keyword' => 'lanyard custom', 'url' => '/produk'],
            ['keyword' => 'lanyard', 'url' => '/'],
        ], 2);

        $this->assertStringContainsString('<h2>Lanyard custom</h2>', $result);
        $this->assertStringContainsString('<a href="/produk" class="seo-auto-link">Lanyard custom</a> Indonesia', $result);
        $this->assertStringContainsString('<a href="/lama">Lanyard custom</a>', $result);
        $this->assertSame(2, substr_count($result, 'class="seo-auto-link"'));
    }

    public function test_it_honours_the_page_limit_and_rejects_unsafe_urls(): void
    {
        $result = AutoLinkSeo::inject('<p>Produk lanyard untuk event.</p>', [
            ['keyword' => 'produk', 'url' => 'javascript:alert(1)'],
            ['keyword' => 'lanyard', 'url' => '/produk'],
            ['keyword' => 'event', 'url' => '/area'],
        ], 1);

        $this->assertStringNotContainsString('javascript:', $result);
        $this->assertSame(1, substr_count($result, 'class="seo-auto-link"'));
        $this->assertStringContainsString('href="/produk"', $result);
    }
}
