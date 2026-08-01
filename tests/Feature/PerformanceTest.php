<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.public_store' => 'array']);
    }

    public function test_anonymous_homepage_does_not_start_a_session_or_xsrf_cookie(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $this->assertSame([], $response->headers->getCookies());
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age=60', $cacheControl);
        $this->assertStringContainsString('s-maxage=300', $cacheControl);
        $this->assertStringContainsString('stale-while-revalidate=600', $cacheControl);
        $this->assertNotEmpty($response->headers->get('ETag'));
    }

    public function test_homepage_uses_cached_html_and_supports_conditional_requests(): void
    {
        $first = $this->get('/');
        $etag = (string) $first->headers->get('ETag');

        $this->assertTrue(Cache::store('array')->has('home_response_html'));

        $this->withHeader('If-None-Match', $etag)
            ->get('/')
            ->assertNotModified();
    }

    public function test_homepage_uses_minified_bundles_and_explicit_image_loading_policy(): void
    {
        $response = $this->get('/');
        $html = $response->getContent();

        $this->assertStringContainsString('/build/public/site.min.css', $html);
        $this->assertStringContainsString('/build/public/home.min.css', $html);
        $this->assertStringContainsString('/build/public/site.min.js', $html);
        $this->assertStringContainsString('/build/public/home.min.js', $html);
        $this->assertStringNotContainsString('/css/hero.css', $html);
        $this->assertStringNotContainsString('/js/home-gallery.js', $html);

        preg_match_all('/<img\b[^>]*>/i', $html, $matches);
        $this->assertNotEmpty($matches[0]);

        foreach ($matches[0] as $image) {
            $this->assertMatchesRegularExpression('/\bloading="(?:lazy|eager)"/i', $image);
            $this->assertMatchesRegularExpression('/\bdecoding="async"/i', $image);
        }
    }

    public function test_homepage_accessibility_semantics_are_well_formed(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new DOMXPath($document);

        $this->assertSame(3, $xpath->query('//*[@class="home-testimonials__stars" and @role="img" and @aria-label="Rating 5 dari 5"]')->length);
        $this->assertSame(0, $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " home-testimonials__dots ")]')->length);
        $this->assertSame(3, $xpath->query('//*[@data-testimonial-item]')->length);
        $this->assertSame(3, $xpath->query('//img[contains(concat(" ", normalize-space(@class), " "), " home-testimonials__media-image ") and @width="1200" and @height="801"]')->length);
        $this->assertSame(1, $xpath->query('//img[@src="/uploads/testimonial-lanyard-anggi.webp"]')->length);
        $this->assertSame(1, $xpath->query('//img[@src="/uploads/testimonial-lanyard-andi.webp"]')->length);
        $this->assertSame(1, $xpath->query('//img[@src="/uploads/testimonial-lanyard-dimas.webp"]')->length);
        $this->assertSame(0, $xpath->query('//button[@data-testimonials-prev or @data-testimonials-next]')->length);
        $this->assertSame(1, $xpath->query('//*[@data-testimonials-status and @aria-live="polite" and @aria-atomic="true"]')->length);
        $this->assertSame(1, $xpath->query('//section[@id="cta-section"]//img[@src="/uploads/lanyard-bekasi-cta.webp" and @width="941" and @height="1672"]')->length);
        $this->assertSame(1, $xpath->query('//ul[contains(concat(" ", normalize-space(@class), " "), " home-products__grid ")]')->length);
        $this->assertSame(4, $xpath->query('//ul[contains(concat(" ", normalize-space(@class), " "), " home-products__grid ")]/li[contains(concat(" ", normalize-space(@class), " "), " home-products__card ")]')->length);
        $this->assertSame(4, $xpath->query('//ol[contains(concat(" ", normalize-space(@class), " "), " home-order__list ")]/li')->length);
        $this->assertSame(3, $xpath->query('//article[contains(concat(" ", normalize-space(@class), " "), " home-blog__card ")]')->length);
        $this->assertSame(1, $xpath->query('//main//h1')->length);
        $this->assertSame(1, $xpath->query('//*[@id="layanan"]')->length);
        $this->assertSame(1, $xpath->query('//*[@id="garansi"]')->length);
        $this->assertSame(1, $xpath->query('//*[@id="faq"]')->length);
        $this->assertSame(4, $xpath->query('//*[@id="faq"]//details')->length);
        $this->assertSame(0, $xpath->query('//*[@id="faq"]//details[@open]')->length);
        $this->assertSame(0, $xpath->query('//*[@id="faq"]//a[@href="'.route('faq').'"]')->length);
        $this->assertSame(0, $xpath->query('//article[@role="listitem"]')->length);
    }

    public function test_homepage_text_scale_increases_copy_without_resizing_headings_or_glyphs(): void
    {
        $css = file_get_contents(public_path('css/homepage.css'));

        $this->assertIsString($css);
        $this->assertMatchesRegularExpression('/\.home-section-heading\s*>\s*p\s*\{[^}]*font-size:\s*12px;/s', $css);
        $this->assertMatchesRegularExpression('/\.ptl-hero__description\s*\{[^}]*font-size:\s*16px;/s', $css);
        $this->assertMatchesRegularExpression('/\.benefit-item\s+h3\s*\{[^}]*font-size:\s*15px;/s', $css);
        $this->assertMatchesRegularExpression('/\.benefit-item\s+p\s*\{[^}]*font-size:\s*14px;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-products__body\s*>\s*p\s*\{[^}]*font-size:\s*14px;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-order__list\s+h3\s*\{[^}]*font-size:\s*16px;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-order__list\s+p\s*\{[^}]*font-size:\s*14px;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-cta__copy\s+p\s*\{[^}]*font-size:\s*15px;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-cta__action\s+ul\s*\{[^}]*font-size:\s*12px;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-blog__body\s+p\s*\{[^}]*font-size:\s*13px;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-testimonials__card\s+blockquote\s+p\s*\{[^}]*font-size:\s*16px;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-faq__answer\s+p\s*\{[^}]*font-size:\s*13px;/s', $css);
        $this->assertStringContainsString('font-size: calc(clamp(1rem, 1.4vw, 1.18rem) + 1px);', $css);
        $this->assertStringContainsString('font-size: calc(1rem + 1px);', $css);

        $this->assertMatchesRegularExpression('/\.ptl-hero__title\s*\{[^}]*font-size:\s*clamp\(3rem,\s*4\.6vw,\s*4\.2rem\);/s', $css);
        $this->assertMatchesRegularExpression('/\.home-section-heading\s+h2\s*\{[^}]*font-size:\s*clamp\(1\.75rem,\s*2\.6vw,\s*2\.25rem\);/s', $css);
        $this->assertMatchesRegularExpression('/\.benefit-item__icon\s+\.material-symbols-outlined\s*\{[^}]*font-size:\s*28px\s*!important;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-testimonials__quote-mark\s*\{[^}]*font-size:\s*48px;/s', $css);
        $this->assertMatchesRegularExpression('/\.home-testimonials__stars\s*\{[^}]*font-size:\s*18px;/s', $css);
    }

    public function test_mobile_header_content_stretches_to_the_available_width(): void
    {
        $headerCss = file_get_contents(public_path('css/header.css'));

        $this->assertIsString($headerCss);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr);', $headerCss);
        $this->assertStringContainsString('justify-content: stretch;', $headerCss);
        $this->assertStringContainsString('.site-header__mobile-links', $headerCss);
        $this->assertStringContainsString('.site-header__mobile-contact', $headerCss);
        $this->assertGreaterThanOrEqual(3, substr_count($headerCss, 'width: 100%;'));
    }

    public function test_login_and_comments_keep_session_and_csrf_protection(): void
    {
        $login = $this->get('/login');
        $cookieNames = collect($login->headers->getCookies())->map->getName();

        $this->assertTrue($cookieNames->contains('XSRF-TOKEN'));
        $this->assertTrue($cookieNames->contains(config('session.cookie')));
        $commentRoute = app('router')->getRoutes()->match(Request::create('/blog/post-yang-dilindungi/comments', 'POST'));
        $commentMiddleware = app('router')->gatherRouteMiddleware($commentRoute);
        $this->assertContains(StartSession::class, $commentMiddleware);
        $this->assertContains(PreventRequestForgery::class, $commentMiddleware);

        $dashboardRoute = app('router')->getRoutes()->match(Request::create('/kawruh', 'GET'));
        $this->assertContains(StartSession::class, app('router')->gatherRouteMiddleware($dashboardRoute));
    }

    public function test_woff2_delivery_has_the_correct_mime_and_immutable_cache(): void
    {
        $response = $this->get('/font-assets/geist-400');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'font/woff2');
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('immutable', $cacheControl);
        $this->assertSame([], $response->headers->getCookies());
    }

    public function test_dashboard_summary_stays_within_ten_database_queries(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        $queries = 0;
        DB::listen(function () use (&$queries): void {
            $queries++;
        });

        $this->actingAs($admin)->get('/kawruh')->assertOk();

        $this->assertLessThanOrEqual(10, $queries, "Dashboard executed {$queries} database queries.");
    }

    public function test_content_observers_invalidate_only_their_homepage_cache(): void
    {
        Cache::store('array')->put('home_products', 'cached');
        Cache::store('array')->put('home_posts', 'cached');
        Cache::store('array')->put('home_seo_settings', 'cached');
        Cache::store('array')->put('home_response_html', 'cached');
        Cache::store('array')->put('seo_auto_links_runtime', 'cached');
        Cache::store('array')->put('cms.analytics.keep', 'cached');

        Product::query()->create(['sku' => 'TEST-1', 'name' => 'Produk', 'slug' => 'produk']);
        $this->assertFalse(Cache::store('array')->has('home_products'));
        $this->assertFalse(Cache::store('array')->has('home_response_html'));
        $this->assertTrue(Cache::store('array')->has('home_posts'));

        Cache::store('array')->put('home_response_html', 'cached');
        Post::query()->create(['title' => 'Post', 'slug' => 'post', 'content' => 'Isi']);
        $this->assertFalse(Cache::store('array')->has('home_posts'));
        $this->assertFalse(Cache::store('array')->has('home_response_html'));
        $this->assertTrue(Cache::store('array')->has('home_seo_settings'));

        Cache::store('array')->put('home_response_html', 'cached');
        Setting::query()->create(['key' => 'seo_meta_title', 'value' => 'SEO']);
        $this->assertFalse(Cache::store('array')->has('home_seo_settings'));
        $this->assertFalse(Cache::store('array')->has('home_response_html'));
        $this->assertFalse(Cache::store('array')->has('seo_auto_links_runtime'));
        $this->assertTrue(Cache::store('array')->has('cms.analytics.keep'));
    }
}
