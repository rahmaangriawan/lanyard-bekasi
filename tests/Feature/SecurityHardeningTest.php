<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Post;
use App\Models\User;
use App\Support\SecureSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_initial_import_bootstraps_admin_only_when_none_exists(): void
    {
        Storage::fake('local');
        foreach (range(1, 6) as $index) {
            Post::query()->create([
                'title' => "Artikel {$index}",
                'slug' => "artikel-bootstrap-{$index}",
                'content' => '<p>Konten</p>',
                'published' => true,
            ]);
        }

        $previousName = getenv('CMS_ADMIN_NAME');
        $previousEmail = getenv('CMS_ADMIN_EMAIL');
        $previousPassword = getenv('CMS_ADMIN_PASSWORD');
        putenv('CMS_ADMIN_NAME=Administrator');
        putenv('CMS_ADMIN_EMAIL=bootstrap@example.com');
        putenv('CMS_ADMIN_PASSWORD=bootstrap-password');
        $_ENV['CMS_ADMIN_NAME'] = $_SERVER['CMS_ADMIN_NAME'] = 'Administrator';
        $_ENV['CMS_ADMIN_EMAIL'] = $_SERVER['CMS_ADMIN_EMAIL'] = 'bootstrap@example.com';
        $_ENV['CMS_ADMIN_PASSWORD'] = $_SERVER['CMS_ADMIN_PASSWORD'] = 'bootstrap-password';

        try {
            $this->artisan('content:import-initial')
                ->expectsOutput('Initial admin account is ready: bootstrap@example.com')
                ->expectsOutput('Initial public content already exists; import skipped.')
                ->assertSuccessful();
        } finally {
            $this->restoreEnvironmentVariable('CMS_ADMIN_NAME', $previousName);
            $this->restoreEnvironmentVariable('CMS_ADMIN_EMAIL', $previousEmail);
            $this->restoreEnvironmentVariable('CMS_ADMIN_PASSWORD', $previousPassword);
        }

        $admin = User::query()->firstOrFail();
        $this->assertSame(User::ROLE_ADMIN, $admin->role);
        $this->assertSame('administrator', $admin->slug);
        $this->assertTrue(Hash::check('bootstrap-password', $admin->password));
    }

    public function test_initial_import_preserves_existing_admin_credentials(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create([
            'name' => 'Nama dari Profil',
            'email' => 'old@example.com',
            'password' => 'old-password',
            'role' => 'ADMIN',
        ]);

        foreach (range(1, 6) as $index) {
            Post::query()->create([
                'title' => "Artikel {$index}",
                'slug' => "artikel-{$index}",
                'content' => '<p>Konten</p>',
                'published' => true,
            ]);
        }

        $previousEmail = getenv('CMS_ADMIN_EMAIL');
        $previousPassword = getenv('CMS_ADMIN_PASSWORD');

        putenv('CMS_ADMIN_EMAIL=admin@example.com');
        putenv('CMS_ADMIN_PASSWORD=new-secure-password');
        $_ENV['CMS_ADMIN_EMAIL'] = $_SERVER['CMS_ADMIN_EMAIL'] = 'admin@example.com';
        $_ENV['CMS_ADMIN_PASSWORD'] = $_SERVER['CMS_ADMIN_PASSWORD'] = 'new-secure-password';

        try {
            $this->artisan('content:import-initial')
                ->expectsOutput('Existing admin credentials preserved; environment bootstrap skipped.')
                ->expectsOutput('Initial public content already exists; import skipped.')
                ->assertSuccessful();
        } finally {
            $this->restoreEnvironmentVariable('CMS_ADMIN_EMAIL', $previousEmail);
            $this->restoreEnvironmentVariable('CMS_ADMIN_PASSWORD', $previousPassword);
        }

        $admin->refresh();

        $this->assertSame('Nama dari Profil', $admin->name);
        $this->assertSame('old@example.com', $admin->email);
        $this->assertTrue(Hash::check('old-password', $admin->password));
        $this->assertFalse(Hash::check('new-secure-password', $admin->password));
    }

    public function test_failed_login_uses_indonesian_message(): void
    {
        app()->setLocale('id');

        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'correct-password',
            'role' => 'ADMIN',
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ]);
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
        $this->assertStringContainsString(
            'https://static.cloudflareinsights.com',
            (string) $response->headers->get('Content-Security-Policy'),
        );
        $this->assertStringContainsString(
            'https://cloudflareinsights.com',
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

    private function restoreEnvironmentVariable(string $key, string|false $value): void
    {
        if ($value === false) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);

            return;
        }

        putenv("{$key}={$value}");
        $_ENV[$key] = $_SERVER[$key] = $value;
    }
}
