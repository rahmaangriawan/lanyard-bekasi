<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\SecureSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    /** @var list<string> */
    private array $temporaryDirectories = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            if (is_dir($directory)) {
                @unlink($directory.DIRECTORY_SEPARATOR.'proof.txt');
                @rmdir($directory);
            }
        }

        parent::tearDown();
    }

    public function test_storage_route_rejects_canonicalized_prefix_sibling_traversal(): void
    {
        $directory = storage_path('app/public-security-test');
        $this->temporaryDirectories[] = $directory;
        mkdir($directory, 0777, true);
        file_put_contents($directory.DIRECTORY_SEPARATOR.'proof.txt', 'not public');

        $this->get('/storage/%2e%2e/public-security-test/proof.txt')
            ->assertNotFound()
            ->assertDontSee('not public');
    }

    public function test_legacy_asset_routes_reject_canonicalized_prefix_sibling_traversal(): void
    {
        $directory = public_path('images-security-test');
        $this->temporaryDirectories[] = $directory;
        mkdir($directory, 0777, true);
        file_put_contents($directory.DIRECTORY_SEPARATOR.'proof.txt', 'not public');

        $this->get('/images/%2e%2e/images-security-test/proof.txt')
            ->assertNotFound()
            ->assertDontSee('not public');
    }

    public function test_initial_import_fails_closed_without_explicit_admin_credentials(): void
    {
        Storage::fake('local');

        $this->artisan('content:import-initial')
            ->expectsOutput('CMS_ADMIN_EMAIL dan CMS_ADMIN_PASSWORD wajib diisi saat membuat admin awal.')
            ->assertFailed();

        $this->assertDatabaseCount('user', 0);
    }

    public function test_security_headers_include_csp_but_not_hsts_over_local_http(): void
    {
        $response = $this->get('/login')
            ->assertOk()
            ->assertHeader('Content-Security-Policy')
            ->assertHeaderMissing('Strict-Transport-Security')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringNotContainsString(
            'upgrade-insecure-requests',
            (string) $response->headers->get('Content-Security-Policy'),
        );
    }

    public function test_hsts_is_sent_only_for_production_https_requests(): void
    {
        $previousEnvironment = app()->environment();
        app()->instance('env', 'production');

        try {
            $this->withServerVariables([
                'HTTPS' => 'on',
                'SERVER_PORT' => 443,
                'HTTP_X_FORWARDED_PROTO' => 'https',
            ])
                ->get('/login')
                ->assertHeader(
                    'Strict-Transport-Security',
                    'max-age=31536000; includeSubDomains',
                );
        } finally {
            app()->instance('env', $previousEnvironment);
        }
    }

    public function test_cors_allows_configured_origin(): void
    {
        $allowedOrigin = config('cors.allowed_origins.0');

        $this->withHeader('Origin', $allowedOrigin)
            ->getJson('/api/health')
            ->assertHeader('Access-Control-Allow-Origin', $allowedOrigin);
    }

    public function test_cors_rejects_unknown_origin(): void
    {
        $response = $this->withHeader('Origin', 'https://attacker.invalid')
            ->getJson('/api/health');

        $this->assertNotContains(
            $response->headers->get('Access-Control-Allow-Origin'),
            ['*', 'https://attacker.invalid'],
        );
    }

    public function test_cms_settings_api_rejects_unknown_keys(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);

        $this->actingAs($admin)
            ->postJson('/api/cms/settings', [
                'site_title' => 'Valid',
                'unexpected_runtime_key' => 'blocked',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('settings');

        $this->assertDatabaseMissing('setting', ['key' => 'unexpected_runtime_key']);
    }

    public function test_cms_settings_api_masks_and_preserves_existing_secrets(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        SecureSettings::put('turnstile_secret_key', 'real-secret');

        $this->actingAs($admin)
            ->getJson('/api/cms/settings')
            ->assertOk()
            ->assertJsonPath('settings.turnstile_secret_key', '[configured]')
            ->assertDontSee('real-secret');

        $this->actingAs($admin)
            ->postJson('/api/cms/settings', [
                'turnstile_secret_key' => '[configured]',
            ])
            ->assertOk();

        $this->assertSame('real-secret', SecureSettings::get('turnstile_secret_key'));
        $this->assertNotSame(
            'real-secret',
            Setting::query()->where('key', 'turnstile_secret_key')->value('value'),
        );
    }

    public function test_cms_sidebar_links_to_public_site_in_a_new_tab(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);

        $this->actingAs($admin)
            ->get('/kawruh/settings')
            ->assertOk()
            ->assertSee('Situs')
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false);
    }
}
