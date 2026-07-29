<?php

namespace App\Support;

final class LoginTurnstile
{
    /**
     * @return array{enabled: bool, siteKey: string, secretKey: string}
     */
    public static function config(): array
    {
        $settings = SecureSettings::getMany([
            'turnstile_login_enabled',
            'turnstile_homepage_enabled',
            'turnstile_site_key',
            'turnstile_secret_key',
        ]);
        $enabledValue = array_key_exists('turnstile_login_enabled', $settings)
            ? $settings['turnstile_login_enabled']
            : ($settings['turnstile_homepage_enabled'] ?? 'false');
        $siteKey = trim($settings['turnstile_site_key'] ?? '');
        $secretKey = trim($settings['turnstile_secret_key'] ?? '');

        return [
            'enabled' => $enabledValue === 'true' && $siteKey !== '' && $secretKey !== '',
            'siteKey' => $siteKey,
            'secretKey' => $secretKey,
        ];
    }

    public static function verify(string $token, ?string $ipAddress, string $secretKey): bool
    {
        return Turnstile::verify($token, $ipAddress, $secretKey);
    }
}
