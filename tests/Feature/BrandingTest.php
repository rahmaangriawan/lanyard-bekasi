<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'http://localhost',
            'app.prod_url' => 'https://lanyardbekasi.com',
            'cache.public_store' => 'array',
        ]);
    }

    public function test_public_shell_uses_lanyard_bekasi_branding_and_favicons(): void
    {
        $response = $this->get('/')->assertOk();

        $response->assertSee('Lanyard Bekasi');
        $response->assertSee('https://lanyardbekasi.com/', false);
        $response->assertSee('/uploads/lanyard-bekasi-logo.webp', false);
        $response->assertSee('/favicon.ico', false);
        $response->assertSee('/favicon-32x32.png', false);
        $response->assertSee('/apple-touch-icon.png', false);
        $response->assertSee('href="'.route('services').'"', false);
        $response->assertSee('href="'.route('warranty').'"', false);
        $response->assertSee('href="'.route('faq').'"', false);
        $response->assertSee('Halo%20Lanyard%20Bekasi', false);
        $response->assertSee('site-header__desktop-cta', false);
        $response->assertSee('Pesan via WhatsApp');
        $response->assertDontSee('Pusat Tali Lanyard');
    }

    public function test_manifest_and_generated_brand_assets_have_expected_formats(): void
    {
        $manifest = json_decode(
            file_get_contents(public_path('site.webmanifest')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $this->assertSame('Lanyard Bekasi', $manifest['name']);
        $this->assertSame('/favicon-192x192.png', $manifest['icons'][0]['src']);
        $this->assertSame('/favicon-512x512.png', $manifest['icons'][1]['src']);

        $expectedImages = [
            'uploads/lanyard-bekasi-logo.webp' => [500, 122, 'image/webp'],
            'uploads/lanyard-bekasi-favicon.webp' => [1024, 1024, 'image/webp'],
            'favicon-32x32.png' => [32, 32, 'image/png'],
            'favicon-96x96.png' => [96, 96, 'image/png'],
            'apple-touch-icon.png' => [180, 180, 'image/png'],
            'favicon-192x192.png' => [192, 192, 'image/png'],
            'favicon-512x512.png' => [512, 512, 'image/png'],
        ];

        foreach ($expectedImages as $path => [$width, $height, $mime]) {
            $metadata = getimagesize(public_path($path));
            $this->assertIsArray($metadata, $path);
            $this->assertSame($width, $metadata[0], $path);
            $this->assertSame($height, $metadata[1], $path);
            $this->assertSame($mime, $metadata['mime'], $path);
        }

        $ico = file_get_contents(public_path('favicon.ico'));
        $this->assertIsString($ico);
        $this->assertSame("\x00\x00\x01\x00\x03\x00", substr($ico, 0, 6));
    }

    public function test_public_api_exposes_lanyard_bekasi_identity_and_asset_paths(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('app', 'lanyardbekasi-laravel');

        $this->getJson('/api/public-settings')
            ->assertOk()
            ->assertJsonPath('settings.site_title', 'Lanyard Bekasi')
            ->assertJsonPath('settings.site_logo', '/uploads/lanyard-bekasi-logo.webp')
            ->assertJsonPath('settings.site_favicon', '/uploads/lanyard-bekasi-favicon.webp')
            ->assertJsonPath('settings.og_image', '/uploads/lanyard-bekasi-logo.webp')
            ->assertJsonPath('settings.contact_email', 'info@lanyardbekasi.com');
    }

    public function test_rebrand_migration_updates_only_legacy_brand_settings(): void
    {
        foreach ([
            'site_title' => 'Pusat Tali Lanyard',
            'contact_email' => 'hello@pusattalilanyard.com',
            'site_logo' => '/uploads/custom-logo.webp',
            'contact_phone' => '+62 822-1020-0700',
            'social_instagram' => 'https://instagram.com/pusattalilanyard',
        ] as $key => $value) {
            DB::table('setting')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'createdAt' => now(), 'updatedAt' => now()],
            );
        }

        $migration = require database_path('migrations/2026_07_30_000001_rebrand_active_settings_to_lanyard_bekasi.php');
        $migration->up();

        $settings = DB::table('setting')->pluck('value', 'key');
        $this->assertSame('Lanyard Bekasi', $settings['site_title']);
        $this->assertSame('https://lanyardbekasi.com', $settings['site_url']);
        $this->assertSame('info@lanyardbekasi.com', $settings['contact_email']);
        $this->assertSame('/uploads/custom-logo.webp', $settings['site_logo']);
        $this->assertSame('+62 822-1020-0700', $settings['contact_phone']);
        $this->assertSame('', $settings['social_instagram']);
    }
}
