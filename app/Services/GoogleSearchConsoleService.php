<?php

namespace App\Services;

use App\Support\SecureSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class GoogleSearchConsoleService
{
    private const SCOPE = 'https://www.googleapis.com/auth/webmasters';
    private const SEARCH_ANALYTICS_ENDPOINT = 'https://www.googleapis.com/webmasters/v3/sites/';

    public function fetch(): array
    {
        $settings = SecureSettings::getMany([
            'google_search_console_site_url',
            'google_service_account_json',
        ]);

        $siteUrl = trim((string) ($settings['google_search_console_site_url'] ?? ''));
        $credentialJson = trim((string) ($settings['google_service_account_json'] ?? ''));

        if ($siteUrl === '' || $credentialJson === '') {
            throw new RuntimeException('Google Search Console Site URL dan Service Account JSON wajib diisi di CMS Settings.');
        }

        $credentials = json_decode($credentialJson, true);
        if (! is_array($credentials)) {
            throw new RuntimeException('Credential Google bukan JSON service account yang valid.');
        }

        $token = $this->accessToken($credentials);

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(25)
            ->post($this->searchAnalyticsUrl($siteUrl), [
                'startDate' => Carbon::now()->subDays(29)->toDateString(),
                'endDate' => Carbon::now()->toDateString(),
                'dimensions' => ['date'],
                'rowLimit' => 30,
            ]);

        if (! $response->successful()) {
            $status = $response->status();
            if ($status === 404) {
                $msg = 'Property Search Console \''.$siteUrl.'\' tidak ditemukan oleh Google untuk service account '
                    .($credentials['client_email'] ?? '').'. Pastikan nilainya sama persis dengan tipe property: '
                    .'URL-prefix harus menyertakan protokol dan trailing slash, sedangkan Domain property memakai format sc-domain:domain.com.';
                throw new RuntimeException($msg);
            }
            $message = $response->json('error.message') ?: $response->body() ?: 'Google Search Console API tidak dapat diakses.';
            throw new RuntimeException((string) $message);
        }

        $rows = $response->json('rows');

        if (! is_array($rows)) {
            return [
                'totals' => [
                    'clicks' => 0,
                    'impressions' => 0,
                    'ctr' => 0.0,
                    'position' => 0.0,
                ],
                'daily' => [],
            ];
        }

        $totals = [
            'clicks' => 0,
            'impressions' => 0,
            'ctr' => 0.0,
            'position' => 0.0,
        ];

        $daily = [];

        foreach ($rows as $row) {
            $keys = $row['keys'] ?? [];
            $date = (string) ($keys[0] ?? '');
            $clicks = (int) ($row['clicks'] ?? 0);
            $impressions = (int) ($row['impressions'] ?? 0);
            $ctr = (float) ($row['ctr'] ?? 0);
            $position = (float) ($row['position'] ?? 0);

            if ($date === '') {
                continue;
            }

            $totals['clicks'] += $clicks;
            $totals['impressions'] += $impressions;

            $daily[] = [
                'date' => $date,
                'clicks' => $clicks,
                'impressions' => $impressions,
                'ctr' => round($ctr * 100, 2),
                'position' => round($position, 1),
            ];
        }

        $count = count($daily);
        $totals['ctr'] = $totals['impressions'] > 0 ? round(($totals['clicks'] / $totals['impressions']) * 100, 2) : 0.0;
        $totals['position'] = $count > 0 ? round(array_sum(array_column($daily, 'position')) / $count, 1) : 0.0;
        $totals['clicks'] = array_sum(array_column($daily, 'clicks'));
        $totals['impressions'] = array_sum(array_column($daily, 'impressions'));

        return compact('totals', 'daily');
    }

    public function submitSitemap(?string $sitemapUrl = null): array
    {
        $settings = SecureSettings::getMany(['google_search_console_site_url', 'google_service_account_json']);
        $siteUrl = trim((string) ($settings['google_search_console_site_url'] ?? ''));
        $credentialJson = trim((string) ($settings['google_service_account_json'] ?? ''));
        if ($siteUrl === '' || $credentialJson === '') {
            throw new RuntimeException('Google Search Console Site URL dan Service Account JSON wajib diisi di CMS Settings.');
        }

        $credentials = json_decode($credentialJson, true);
        if (! is_array($credentials)) throw new RuntimeException('Credential Google bukan JSON service account yang valid.');
        $sitemapUrl = $sitemapUrl ?: rtrim((string) config('app.url'), '/').'/sitemap.xml';

        $response = Http::withToken($this->accessToken($credentials))->acceptJson()->timeout(25)->put(
            self::SEARCH_ANALYTICS_ENDPOINT.rawurlencode($siteUrl).'/sitemaps/'.rawurlencode($sitemapUrl)
        );
        if (! $response->successful()) {
            $message = $response->json('error.message') ?: $response->body() ?: 'Sitemap gagal dikirim ke Google Search Console.';
            throw new RuntimeException((string) $message);
        }

        return ['siteUrl' => $siteUrl, 'sitemapUrl' => $sitemapUrl, 'submittedAt' => now()->toAtomString()];
    }

    private function searchAnalyticsUrl(string $siteUrl): string
    {
        return self::SEARCH_ANALYTICS_ENDPOINT.rawurlencode($siteUrl).'/searchAnalytics/query';
    }

    private function accessToken(array $credentials): string
    {
        $email = trim((string) ($credentials['client_email'] ?? ''));
        $privateKey = (string) ($credentials['private_key'] ?? '');
        $tokenUri = trim((string) ($credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token'));

        if ($email === '' || $privateKey === '') {
            throw new RuntimeException('Credential service account tidak memiliki client_email atau private_key.');
        }

        $now = time();
        $header = $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_UNESCAPED_SLASHES));
        $claims = $this->base64Url(json_encode([
            'iss' => $email,
            'scope' => self::SCOPE,
            'aud' => $tokenUri,
            'iat' => $now,
            'exp' => $now + 3600,
        ], JSON_UNESCAPED_SLASHES));
        $unsignedToken = "{$header}.{$claims}";

        if (! openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Private key service account tidak dapat digunakan untuk menandatangani token.');
        }

        $response = Http::asForm()->acceptJson()->timeout(20)->post($tokenUri, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $unsignedToken . '.' . $this->base64Url($signature),
        ]);

        if (! $response->successful() || ! $response->json('access_token')) {
            $message = $response->json('error_description') ?: $response->json('error') ?: 'Autentikasi Google gagal.';
            throw new RuntimeException((string) $message);
        }

        return (string) $response->json('access_token');
    }

    private function base64Url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
