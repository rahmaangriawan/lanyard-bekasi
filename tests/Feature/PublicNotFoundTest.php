<?php

namespace Tests\Feature;

use App\Models\NotFoundUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNotFoundTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.public_store' => 'array']);
    }

    public function test_missing_public_page_renders_branded_noindex_404_and_records_normalized_path(): void
    {
        $this->get('/Halaman-Hilang/?utm_source=iklan', ['Accept' => 'text/html'])
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('Kembali ke Beranda')
            ->assertSee('noindex, follow')
            ->assertDontSee('<link rel="canonical"', false);

        $this->assertDatabaseHas('not_found_url', [
            'path' => '/halaman-hilang',
            'occurrences' => 1,
        ]);
        $this->assertSame(
            now()->toDateString(),
            NotFoundUrl::query()->where('path', '/halaman-hilang')->firstOrFail()->reportDate->toDateString(),
        );
    }

    public function test_missing_product_uses_the_public_404_page_without_becoming_ok(): void
    {
        $this->get('/produk/tidak-ada', ['Accept' => 'text/html'])
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan');

        $this->assertDatabaseHas('not_found_url', ['path' => '/produk/tidak-ada']);
    }

    public function test_api_assets_and_post_requests_are_not_replaced_or_recorded(): void
    {
        $this->getJson('/api/tidak-ada')->assertNotFound();
        $this->get('/uploads/tidak-ada.webp', ['Accept' => 'image/webp'])->assertNotFound();
        $this->post('/blog/tidak-ada/comments')->assertNotFound();

        $this->assertDatabaseMissing('not_found_url', ['path' => '/api/tidak-ada']);
        $this->assertDatabaseMissing('not_found_url', ['path' => '/uploads/tidak-ada.webp']);
        $this->assertDatabaseMissing('not_found_url', ['path' => '/blog/tidak-ada/comments']);
    }

    public function test_same_missing_path_is_recorded_once_per_minute(): void
    {
        $headers = ['Accept' => 'text/html'];
        $this->get('/cache-terbatas', $headers)->assertNotFound();
        $this->get('/CACHE-TERBATAS/', $headers)->assertNotFound();

        $this->assertDatabaseHas('not_found_url', [
            'path' => '/cache-terbatas',
            'occurrences' => 1,
        ]);
    }

    public function test_admin_can_filter_the_report_and_prefill_a_redirect(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        NotFoundUrl::query()->create([
            'path' => '/artikel-lama',
            'reportDate' => now()->toDateString(),
            'occurrences' => 4,
            'firstSeenAt' => now()->subHour(),
            'lastSeenAt' => now(),
        ]);
        NotFoundUrl::query()->create([
            'path' => '/url-lain',
            'reportDate' => now()->toDateString(),
            'occurrences' => 1,
            'firstSeenAt' => now(),
            'lastSeenAt' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/kawruh/not-found-urls?days=30&q=artikel')
            ->assertOk()
            ->assertSee('Laporan URL 404')
            ->assertSee('/artikel-lama')
            ->assertDontSee('/url-lain')
            ->assertSee('sourcePath=%2Fartikel-lama', false);

        $this->actingAs($admin)
            ->get('/kawruh/redirects/create?sourcePath=%2Fartikel-lama')
            ->assertOk()
            ->assertSee('value="/artikel-lama"', false);
    }

    public function test_admin_report_search_treats_sql_wildcards_as_literal_url_characters(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);

        foreach (['/promo_2026', '/promoa2026', '/diskon%akhir'] as $path) {
            NotFoundUrl::query()->create([
                'path' => $path,
                'reportDate' => now()->toDateString(),
                'occurrences' => 1,
                'firstSeenAt' => now(),
                'lastSeenAt' => now(),
            ]);
        }

        $this->actingAs($admin)
            ->get('/kawruh/not-found-urls?q=promo_2026')
            ->assertOk()
            ->assertSee('/promo_2026')
            ->assertDontSee('/promoa2026')
            ->assertDontSee('/diskon%akhir');
    }

    public function test_old_records_are_pruned_when_a_new_public_404_is_recorded(): void
    {
        $old = NotFoundUrl::query()->create([
            'path' => '/terlalu-lama',
            'reportDate' => now()->subDays(91)->toDateString(),
            'occurrences' => 1,
            'firstSeenAt' => now()->subDays(91),
            'lastSeenAt' => now()->subDays(91),
        ]);

        $this->get('/baru', ['Accept' => 'text/html'])->assertNotFound();

        $this->assertDatabaseMissing('not_found_url', ['id' => $old->id]);
    }
}
