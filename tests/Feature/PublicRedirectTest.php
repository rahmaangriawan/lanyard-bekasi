<?php

namespace Tests\Feature;

use App\Models\PublicRedirect;
use App\Models\User;
use App\Support\PublicRedirectRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PublicRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'app.url' => 'http://localhost',
            'cache.public_store' => 'array',
        ]);
    }

    public function test_redirect_dashboard_requires_an_admin_and_lists_the_menu(): void
    {
        $this->get('/kawruh/redirects')->assertRedirect('/login');

        $admin = User::factory()->create(['role' => 'ADMIN']);
        $this->actingAs($admin)
            ->get('/kawruh/redirects')
            ->assertOk()
            ->assertSee('Redirect 301')
            ->assertSee('/kawruh/redirects');

        $this->actingAs($admin)
            ->get('/kawruh/redirects/create')
            ->assertOk()
            ->assertSee('Simpan Redirect')
            ->assertDontSee('General')
            ->assertDontSee('>SEO<', false)
            ->assertDontSee('/vendor/quill/quill.js');
    }

    public function test_admin_can_create_normalized_redirect_and_override_an_existing_page(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);

        $this->actingAs($admin)->post('/kawruh/redirects', [
            'sourcePath' => 'http://localhost/PRODUK/?campaign=old#ignored',
            'targetUrl' => '/area',
            'active' => '1',
        ])->assertRedirect('/kawruh/redirects');

        $this->assertDatabaseHas('redirect', [
            'sourcePath' => '/produk',
            'targetUrl' => '/area',
            'active' => true,
        ]);

        $this->get('/PrOdUk/')->assertRedirect('/area')->assertStatus(301);
        $this->head('/produk')->assertRedirect('/area')->assertStatus(301);
    }

    public function test_redirect_preserves_query_target_override_and_fragment(): void
    {
        PublicRedirect::query()->create([
            'sourcePath' => '/url-lama/bertingkat',
            'targetUrl' => 'https://example.com/baru?utm=target#bagian',
            'active' => true,
        ]);

        $this->get('/url-lama/bertingkat?utm=source&ref=iklan')
            ->assertStatus(301)
            ->assertRedirect('https://example.com/baru?utm=target&ref=iklan#bagian');
    }

    public function test_inactive_redirect_post_requests_and_protected_routes_are_not_redirected(): void
    {
        PublicRedirect::query()->create([
            'sourcePath' => '/produk',
            'targetUrl' => '/area',
            'active' => false,
        ]);
        PublicRedirect::query()->create([
            'sourcePath' => '/dashboard',
            'targetUrl' => 'https://example.com/admin',
            'active' => true,
        ]);
        PublicRedirect::query()->create([
            'sourcePath' => '/blog/tidak-ada/comments',
            'targetUrl' => '/area',
            'active' => true,
        ]);

        $this->get('/produk')->assertOk();
        $this->get('/dashboard')->assertRedirect('/kawruh');
        $this->post('/blog/tidak-ada/comments')->assertNotFound();
    }

    public function test_validation_rejects_unsafe_duplicate_self_and_chain_redirects(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        $this->actingAs($admin)->post('/kawruh/redirects', [
            'sourcePath' => '/awal',
            'targetUrl' => '/akhir',
            'active' => '1',
        ])->assertSessionHasNoErrors();

        $cases = [
            ['/login', '/tujuan', 'sourcePath'],
            ['https://domain-lain.test/lama', '/tujuan', 'sourcePath'],
            ['/javascript', 'javascript:alert(1)', 'targetUrl'],
            ['/spasi', 'https://example.com/tidak valid', 'targetUrl'],
            ['/protocol-relative', '//example.com/tujuan', 'targetUrl'],
            ['/awal', '/lain', 'sourcePath'],
            ['/sendiri', '/sendiri/', 'targetUrl'],
            ['/sebelum-awal', '/awal', 'targetUrl'],
            ['/akhir', '/sesudah-akhir', 'sourcePath'],
        ];

        foreach ($cases as [$source, $target, $field]) {
            $this->actingAs($admin)->from('/kawruh/redirects/create')->post('/kawruh/redirects', [
                'sourcePath' => $source,
                'targetUrl' => $target,
                'active' => '1',
            ])->assertRedirect('/kawruh/redirects/create')->assertSessionHasErrors($field);
        }
    }

    public function test_toggle_delete_and_model_changes_invalidate_only_redirect_cache(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        $redirect = PublicRedirect::query()->create([
            'sourcePath' => '/lama',
            'targetUrl' => '/baru',
            'active' => false,
        ]);
        Cache::store('array')->put(PublicRedirectRegistry::CACHE_KEY, ['stale' => '/value']);
        Cache::store('array')->put('home_products', 'keep');
        Cache::store('array')->put('cms.analytics.keep', 'keep');

        $this->actingAs($admin)
            ->post("/kawruh/redirects/{$redirect->id}/toggle")
            ->assertSessionHasNoErrors();

        $this->assertTrue($redirect->fresh()->active);
        $this->assertFalse(Cache::store('array')->has(PublicRedirectRegistry::CACHE_KEY));
        $this->assertTrue(Cache::store('array')->has('home_products'));
        $this->assertTrue(Cache::store('array')->has('cms.analytics.keep'));

        Cache::store('array')->put(PublicRedirectRegistry::CACHE_KEY, ['stale' => '/value']);
        $this->actingAs($admin)->delete("/kawruh/redirects/{$redirect->id}");
        $this->assertDatabaseMissing('redirect', ['id' => $redirect->id]);
        $this->assertFalse(Cache::store('array')->has(PublicRedirectRegistry::CACHE_KEY));
    }

    public function test_admin_can_update_and_bulk_delete_redirects(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        $first = PublicRedirect::query()->create([
            'sourcePath' => '/pertama',
            'targetUrl' => '/tujuan-pertama',
            'active' => true,
        ]);
        $second = PublicRedirect::query()->create([
            'sourcePath' => '/kedua',
            'targetUrl' => '/tujuan-kedua',
            'active' => true,
        ]);

        $this->actingAs($admin)->patch("/kawruh/redirects/{$first->id}", [
            'sourcePath' => '/PERTAMA-BARU/',
            'targetUrl' => 'https://example.com/final',
            'active' => '1',
        ])->assertRedirect('/kawruh/redirects')->assertSessionHasNoErrors();

        $this->assertDatabaseHas('redirect', [
            'id' => $first->id,
            'sourcePath' => '/pertama-baru',
            'targetUrl' => 'https://example.com/final',
        ]);

        $this->actingAs($admin)->post('/kawruh/redirects/bulk', [
            'selected' => [$first->id, $second->id],
            'bulk_action' => 'delete',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('redirect', ['id' => $first->id]);
        $this->assertDatabaseMissing('redirect', ['id' => $second->id]);
    }
}
