<?php

namespace App\Services;

use App\Support\SecureSettings;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class GoogleAnalyticsService
{
    private const SCOPE = 'https://www.googleapis.com/auth/analytics.readonly';

    private const BASE_URL = 'https://analyticsdata.googleapis.com/v1beta';

    public function fetch(): array
    {
        $settings = SecureSettings::getMany([
            'google_analytics_property_id',
            'google_service_account_json',
        ]);

        $propertyId = trim((string) ($settings['google_analytics_property_id'] ?? ''));
        $credentialJson = trim((string) ($settings['google_service_account_json'] ?? ''));

        if ($propertyId === '' || $credentialJson === '') {
            throw new RuntimeException('Google Analytics Property ID dan Service Account JSON wajib diisi di CMS Settings.');
        }

        $credentials = json_decode($credentialJson, true);
        if (! is_array($credentials)) {
            throw new RuntimeException('Credential Google bukan JSON service account yang valid.');
        }

        $token = $this->accessToken($credentials);

        $totalsAndDaily = $this->fetchTotalsAndDaily($token, $propertyId);
        $topPages = $this->fetchTopPages($token, $propertyId);

        return [
            'totals' => $totalsAndDaily['totals'],
            'daily' => $totalsAndDaily['daily'],
            'topPages' => $topPages,
        ];
    }

    private function fetchTotalsAndDaily(string $token, string $propertyId): array
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(25)
            ->post(self::BASE_URL.'/properties/'.rawurlencode($propertyId).':runReport', [
                'dateRanges' => [
                    ['startDate' => '30daysAgo', 'endDate' => 'today'],
                ],
                'metrics' => [
                    ['name' => 'activeUsers'],
                    ['name' => 'newUsers'],
                    ['name' => 'sessions'],
                    ['name' => 'screenPageViews'],
                    ['name' => 'bounceRate'],
                ],
                'dimensions' => [
                    ['name' => 'date'],
                ],
                'metricAggregations' => ['TOTAL'],
                'keepEmptyRows' => false,
                'returnPropertyQuota' => false,
            ]);

        if (! $response->successful()) {
            $message = $response->json('error.message') ?: 'Gagal mengambil data Google Analytics.';
            throw new RuntimeException((string) $message);
        }

        $data = $response->json();

        return [
            'totals' => $this->parseTotals($data),
            'daily' => $this->parseDailyRows($data),
        ];
    }

    private function fetchTopPages(string $token, string $propertyId): array
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(25)
            ->post(self::BASE_URL.'/properties/'.rawurlencode($propertyId).':runReport', [
                'dateRanges' => [
                    ['startDate' => '30daysAgo', 'endDate' => 'today'],
                ],
                'metrics' => [
                    ['name' => 'screenPageViews'],
                ],
                'dimensions' => [
                    ['name' => 'pagePath'],
                    ['name' => 'pageTitle'],
                ],
                'orderBys' => [
                    [
                        'metric' => ['metricName' => 'screenPageViews'],
                        'desc' => true,
                    ],
                ],
                'limit' => 10,
                'keepEmptyRows' => false,
                'returnPropertyQuota' => false,
            ]);

        if (! $response->successful()) {
            $message = $response->json('error.message') ?: 'Gagal mengambil data top pages Google Analytics.';
            throw new RuntimeException((string) $message);
        }

        $data = $response->json();
        $rows = $data['rows'] ?? [];
        $pages = [];

        foreach ($rows as $row) {
            $dimensionValues = $row['dimensionValues'] ?? [];
            $metricValues = $row['metricValues'] ?? [];

            $pages[] = [
                'path' => $dimensionValues[0]['value'] ?? '/',
                'title' => $dimensionValues[1]['value'] ?? '',
                'views' => (int) ($metricValues[0]['value'] ?? 0),
            ];
        }

        return $pages;
    }

    private function parseTotals(array $data): array
    {
        $totalsRow = $data['totals'][0] ?? null;

        if ($totalsRow === null) {
            return [
                'activeUsers' => 0,
                'newUsers' => 0,
                'sessions' => 0,
                'pageViews' => 0,
                'bounceRate' => 0.0,
            ];
        }

        $metrics = $totalsRow['metricValues'] ?? [];

        return [
            'activeUsers' => (int) ($metrics[0]['value'] ?? 0),
            'newUsers' => (int) ($metrics[1]['value'] ?? 0),
            'sessions' => (int) ($metrics[2]['value'] ?? 0),
            'pageViews' => (int) ($metrics[3]['value'] ?? 0),
            'bounceRate' => (float) ($metrics[4]['value'] ?? 0),
        ];
    }

    private function parseDailyRows(array $data): array
    {
        $rows = $data['rows'] ?? [];
        $daily = [];

        foreach ($rows as $row) {
            $dimensionValues = $row['dimensionValues'] ?? [];
            $metricValues = $row['metricValues'] ?? [];

            $dateRaw = $dimensionValues[0]['value'] ?? '';
            $formattedDate = $dateRaw !== ''
                ? substr($dateRaw, 0, 4).'-'.substr($dateRaw, 4, 2).'-'.substr($dateRaw, 6, 2)
                : '';

            $daily[] = [
                'date' => $formattedDate,
                'activeUsers' => (int) ($metricValues[0]['value'] ?? 0),
                'pageViews' => (int) ($metricValues[3]['value'] ?? 0),
            ];
        }

        return $daily;
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
            'assertion' => $unsignedToken.'.'.$this->base64Url($signature),
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
