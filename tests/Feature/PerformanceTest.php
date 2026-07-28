<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
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
