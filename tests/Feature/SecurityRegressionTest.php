<?php

namespace Tests\Feature;

use App\Models\CityPage;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use App\Support\SecureSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_rich_content_is_sanitized_when_rendered(): void
    {
        $payload = '<h2>Judul Aman</h2><p onclick="alert(1)">Paragraf</p>'
            .'<script>alert(1)</script><img src="javascript:alert(1)" onerror="alert(1)">'
            .'<table><tbody><tr><td>Data aman</td></tr></tbody></table>';

        Post::query()->create([
            'title' => 'Artikel Legacy',
            'slug' => 'artikel-legacy',
            'content' => $payload,
            'published' => true,
            'createdAt' => now(),
        ]);
        CityPage::query()->create([
            'title' => 'Kota Legacy',
            'slug' => 'kota-legacy',
            'content' => $payload,
            'published' => true,
            'createdAt' => now(),
        ]);

        foreach (['/blog/artikel-legacy', '/area/kota-legacy'] as $url) {
            $response = $this->get($url)->assertOk();
            $html = $response->getContent();

            $this->assertStringContainsString('Judul Aman', $html);
            $this->assertStringContainsString('Data aman', $html);
            $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
            $this->assertStringNotContainsString('onclick="alert(1)"', $html);
            $this->assertStringNotContainsString('onerror="alert(1)"', $html);
            $this->assertStringNotContainsString('src="javascript:', $html);
        }
    }

    public function test_structured_data_cannot_be_broken_out_by_database_text(): void
    {
        $payload = '</script><script>alert("schema-xss")</script>';
        Post::query()->create([
            'title' => $payload,
            'slug' => 'schema-breakout',
            'content' => '<p>Konten aman.</p>',
            'published' => true,
            'createdAt' => now(),
        ]);

        $html = $this->get('/blog/schema-breakout')->assertOk()->getContent();

        $this->assertStringNotContainsString($payload, $html);
        $this->assertStringContainsString('\u003C/script\u003E', $html);
    }

    public function test_web_mutations_require_csrf_but_public_api_submission_remains_stateless(): void
    {
        $post = Post::query()->create([
            'title' => 'Artikel CSRF',
            'slug' => 'artikel-csrf',
            'content' => '<p>Konten.</p>',
            'published' => true,
            'createdAt' => now(),
        ]);

        $originalEnvironment = $this->app->environment();
        $this->app['env'] = 'production';

        try {
            $this->withMiddleware(PreventRequestForgery::class)
                ->post('/login', ['email' => 'admin@example.com', 'password' => 'password'])
                ->assertStatus(419);
            $this->withMiddleware(PreventRequestForgery::class)
                ->post('/blog/artikel-csrf/comments', [
                    'name' => 'Pengunjung',
                    'email' => 'visitor@example.com',
                    'content' => 'Komentar tanpa token.',
                ])
                ->assertStatus(419);
        } finally {
            $this->app['env'] = $originalEnvironment;
        }

        $this->postJson('/api/comments', [
            'postId' => $post->id,
            'name' => 'Pengunjung',
            'email' => 'visitor@example.com',
            'content' => 'Komentar API stateless.',
        ])->assertCreated();
    }

    public function test_inquiry_turnstile_uses_decrypted_secret_and_fails_closed(): void
    {
        SecureSettings::put('turnstile_homepage_enabled', 'true');
        SecureSettings::put('turnstile_secret_key', 'plain-turnstile-secret');
        Http::fake(fn ($request) => Http::response([
            'success' => $request['response'] === 'valid-token',
        ]));

        $this->postJson('/api/inquiries', [
            'name' => 'Calon Pelanggan',
            'email' => 'customer@example.com',
            'phone' => '081234567890',
            'message' => 'Saya ingin meminta quotation lanyard.',
            'turnstileToken' => 'valid-token',
        ])->assertCreated();

        Http::assertSent(fn ($request) => $request['secret'] === 'plain-turnstile-secret'
            && $request['response'] === 'valid-token');

        $this->postJson('/api/inquiries', [
            'name' => 'Calon Pelanggan',
            'email' => 'other@example.com',
            'phone' => '081234567891',
            'message' => 'Permintaan ini harus ditolak.',
            'turnstileToken' => 'invalid-token',
        ])->assertStatus(400);
    }

    public function test_named_limiters_use_balanced_thresholds(): void
    {
        $request = Request::create('/login', 'POST', server: ['REMOTE_ADDR' => '203.0.113.40']);

        $this->assertLimiter('login-global', $request, 20);
        $this->assertLimiter('password-email', $request, 3);
        $this->assertLimiter('password-reset', $request, 5);
        $this->assertLimiter('public-api', $request, 120);

        $user = User::factory()->create();
        $request->setUserResolver(fn () => $user);
        $this->assertLimiter('cms-api', $request, 120);
    }

    public function test_public_and_cms_api_limiters_return_429_with_retry_after(): void
    {
        RateLimiter::for('public-api', fn () => Limit::perMinute(2)->by('public-api-test'));

        $this->getJson('/api/turnstile-config')->assertOk();
        $this->getJson('/api/turnstile-config')->assertOk();
        $this->getJson('/api/turnstile-config')
            ->assertStatus(429)
            ->assertHeader('Retry-After');
        $this->getJson('/api/health')->assertOk();

        RateLimiter::for('cms-api', fn () => Limit::perMinute(2)->by('cms-api-test'));
        $writer = User::factory()->create(['role' => User::ROLE_WRITER]);
        Sanctum::actingAs($writer);

        $this->getJson('/api/cms/posts')->assertOk();
        $this->getJson('/api/cms/posts')->assertOk();
        $this->getJson('/api/cms/posts')
            ->assertStatus(429)
            ->assertHeader('Retry-After');
    }

    public function test_unknown_role_is_denied_by_web_and_api_guards(): void
    {
        $staff = User::factory()->create(['role' => 'STAFF']);

        $this->actingAs($staff)->get('/kawruh/posts')->assertForbidden();

        Sanctum::actingAs($staff);
        $this->getJson('/api/cms/posts')->assertForbidden();
    }

    public function test_sql_injection_payload_is_treated_as_search_text(): void
    {
        Product::query()->create([
            'sku' => 'SAFE-001',
            'name' => 'Produk Aman Satu',
            'slug' => 'produk-aman-satu',
            'published' => true,
            'createdAt' => now(),
        ]);
        Product::query()->create([
            'sku' => 'SAFE-002',
            'name' => 'Produk Aman Dua',
            'slug' => 'produk-aman-dua',
            'published' => true,
            'createdAt' => now(),
        ]);
        $payload = "' OR 1=1 UNION SELECT password FROM user --";

        $this->getJson('/api/products?q='.rawurlencode($payload))
            ->assertOk()
            ->assertJsonCount(0, 'products');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Post::query()->create([
            'title' => 'Artikel Rahasia',
            'slug' => 'artikel-rahasia',
            'content' => '<p>Konten.</p>',
            'published' => true,
            'createdAt' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/kawruh/posts?q='.rawurlencode($payload))
            ->assertOk()
            ->assertDontSee('Artikel Rahasia');
    }

    public function test_product_rich_text_is_sanitized_on_write_and_public_read(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Product::query()->create([
            'sku' => 'XSS-001',
            'name' => 'Produk XSS',
            'slug' => 'produk-xss',
            'published' => true,
            'createdAt' => now(),
        ]);

        Sanctum::actingAs($admin);
        $this->putJson('/api/cms/products/XSS-001', [
            'published' => true,
            'description' => '<h2>Deskripsi Aman</h2><img src="https://example.com/image.webp" onerror="alert(1)">'
                .'<a href="javascript:alert(1)">tautan</a><script>alert(1)</script>',
        ])->assertOk();

        $stored = (string) Product::query()->where('sku', 'XSS-001')->value('description');
        $this->assertStringContainsString('<h2>Deskripsi Aman</h2>', $stored);
        $this->assertStringNotContainsString('onerror', $stored);
        $this->assertStringNotContainsString('javascript:', $stored);
        $this->assertStringNotContainsString('<script', $stored);

        $this->getJson('/api/products/produk-xss')
            ->assertOk()
            ->assertJsonPath('product.description', $stored);
    }

    private function assertLimiter(string $name, Request $request, int $expectedAttempts): void
    {
        $callback = RateLimiter::limiter($name);
        $this->assertNotNull($callback);
        $limit = $callback($request);
        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertSame($expectedAttempts, $limit->maxAttempts);
        $this->assertSame(60, $limit->decaySeconds);
    }
}
