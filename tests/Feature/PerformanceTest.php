<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
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
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
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
        $this->assertContains(ValidateCsrfToken::class, $commentMiddleware);

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
        Cache::store('array')->put('seo_auto_links_runtime', 'cached');
        Cache::store('array')->put('cms.analytics.keep', 'cached');

        Product::query()->create(['sku' => 'TEST-1', 'name' => 'Produk', 'slug' => 'produk']);
        $this->assertFalse(Cache::store('array')->has('home_products'));
        $this->assertTrue(Cache::store('array')->has('home_posts'));

        Post::query()->create(['title' => 'Post', 'slug' => 'post', 'content' => 'Isi']);
        $this->assertFalse(Cache::store('array')->has('home_posts'));
        $this->assertTrue(Cache::store('array')->has('home_seo_settings'));

        Setting::query()->create(['key' => 'seo_meta_title', 'value' => 'SEO']);
        $this->assertFalse(Cache::store('array')->has('home_seo_settings'));
        $this->assertFalse(Cache::store('array')->has('seo_auto_links_runtime'));
        $this->assertTrue(Cache::store('array')->has('cms.analytics.keep'));
    }
}
