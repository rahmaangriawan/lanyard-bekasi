<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Support\SecureSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecureSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_encrypts_and_decrypts_secret_settings(): void
    {
        SecureSettings::put('turnstile_secret_key', 'super-secret-value');

        $raw = Setting::query()->where('key', 'turnstile_secret_key')->value('value');

        $this->assertIsString($raw);
        $this->assertStringStartsWith('enc:v1:', $raw);
        $this->assertStringNotContainsString('super-secret-value', $raw);
        $this->assertSame('super-secret-value', SecureSettings::get('turnstile_secret_key'));
    }

    public function test_it_leaves_non_secret_settings_plain(): void
    {
        SecureSettings::put('site_title', 'Lanyard Bekasi');

        $raw = Setting::query()->where('key', 'site_title')->value('value');

        $this->assertSame('Lanyard Bekasi', $raw);
        $this->assertSame('Lanyard Bekasi', SecureSettings::get('site_title'));
    }

    public function test_it_reads_legacy_plaintext_secrets(): void
    {
        Setting::query()->create([
            'key' => 'otp_api_key',
            'value' => 'legacy-plain-key',
        ]);

        $this->assertSame('legacy-plain-key', SecureSettings::get('otp_api_key'));
    }

    public function test_public_payload_masks_secrets(): void
    {
        SecureSettings::put('turnstile_secret_key', 'hidden');
        SecureSettings::put('site_title', 'Visible');

        $public = SecureSettings::forPublic(SecureSettings::getMany([
            'turnstile_secret_key',
            'site_title',
        ]));

        $this->assertSame('[configured]', $public['turnstile_secret_key']);
        $this->assertSame('Visible', $public['site_title']);
    }

    public function test_migrate_plaintext_secrets_is_idempotent(): void
    {
        Setting::query()->create([
            'key' => 'google_service_account_json',
            'value' => '{"type":"service_account"}',
        ]);

        $this->assertSame(1, SecureSettings::migratePlaintextSecrets());
        $this->assertSame(0, SecureSettings::migratePlaintextSecrets());
        $this->assertSame('{"type":"service_account"}', SecureSettings::get('google_service_account_json'));
    }
}
