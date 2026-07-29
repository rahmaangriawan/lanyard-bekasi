<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use App\Support\StructuredData;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_preserves_editorial_markup_and_safe_links(): void
    {
        $html = '<h2 id="bagian">Judul</h2><p><strong>Tebal</strong> dan <a href="/produk" target="_blank" rel="nofollow">tautan</a>.</p>'
            .'<a href="produk/lanyard">tautan relatif</a>'
            .'<figure><img src="https://example.com/image.webp" alt="Contoh" width="640"><figcaption>Caption</figcaption></figure>'
            .'<table><thead><tr><th scope="col">Nama</th></tr></thead><tbody><tr><td>Nilai</td></tr></tbody></table>';

        $clean = HtmlSanitizer::clean($html);

        $this->assertStringContainsString('<h2 id="bagian">Judul</h2>', $clean);
        $this->assertStringContainsString('<strong>Tebal</strong>', $clean);
        $this->assertStringContainsString('href="/produk"', $clean);
        $this->assertStringContainsString('href="produk/lanyard"', $clean);
        $this->assertStringContainsString('rel="nofollow noopener noreferrer"', $clean);
        $this->assertStringContainsString('<figcaption>Caption</figcaption>', $clean);
        $this->assertStringContainsString('<table>', $clean);
    }

    public function test_it_removes_scriptable_markup_and_unsafe_urls(): void
    {
        $html = '<script>alert(1)</script><style>body{display:none}</style>'
            .'<p onclick="alert(1)" style="color:red" data-payload="x">Aman</p>'
            .'<a href="javascript:alert(1)">JS</a><a href="//evil.example/path">Protocol relative</a>'
            .'<a href="java&#x0A;script:alert(1)">Encoded</a>'
            .'<a href="javascript%3Aalert(1)">Percent encoded</a>'
            .'<img src="data:image/svg+xml,%3Csvg/onload=alert(1)%3E" onerror="alert(1)">'
            .'<iframe src="https://evil.example"></iframe>';

        $clean = HtmlSanitizer::clean($html);

        $this->assertStringContainsString('<p>Aman</p>', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('<style', $clean);
        $this->assertStringNotContainsString('<iframe', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('style=', $clean);
        $this->assertStringNotContainsString('data-payload', $clean);
        $this->assertStringNotContainsString('javascript:', strtolower($clean));
        $this->assertStringNotContainsString('//evil.example', $clean);
        $this->assertStringNotContainsString('data:image', $clean);
    }

    public function test_structured_data_encoder_prevents_script_breakout(): void
    {
        $encoded = StructuredData::encode([
            '@context' => 'https://schema.org',
            'name' => '</script><script>alert("xss")</script>',
        ]);

        $this->assertStringNotContainsString('</script>', $encoded);
        $this->assertStringNotContainsString('<script>', $encoded);
        $this->assertStringContainsString('\u003C/script\u003E', $encoded);
        $this->assertSame(
            '</script><script>alert("xss")</script>',
            json_decode($encoded, true, flags: JSON_THROW_ON_ERROR)['name'],
        );
    }
}
