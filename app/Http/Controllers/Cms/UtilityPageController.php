<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\NotFoundUrl;
use App\Services\GoogleAnalyticsService;
use App\Services\GoogleSearchConsoleService;
use App\Support\SecureSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

class UtilityPageController extends Controller
{
    public function analytics(GoogleAnalyticsService $analytics, GoogleSearchConsoleService $searchConsole): View
    {
        $settings = SecureSettings::getMany([
            'google_analytics_property_id',
            'google_search_console_site_url',
            'google_service_account_json',
        ]);
        $propertyId = trim($settings['google_analytics_property_id'] ?? '');
        $siteUrl = trim($settings['google_search_console_site_url'] ?? '');
        $credential = trim($settings['google_service_account_json'] ?? '');
        $hasCredential = $credential !== '';
        $credentialFingerprint = sha1($credential);
        $connections = [];
        $metrics = [];
        $integrationErrors = [];
        $gaData = [
            'totals' => [],
            'daily' => [],
            'topPages' => [],
        ];
        $gscData = [
            'totals' => [],
            'daily' => [],
        ];

        $connections[] = $this->statusCard('GA4', $propertyId ?: 'Belum diset', $propertyId !== '', 'chart');
        $connections[] = $this->statusCard('Search Console', $siteUrl ?: 'Belum diset', $siteUrl !== '', 'search');
        $connections[] = $this->statusCard('Service Account', $hasCredential ? 'Tersimpan' : 'Belum diset', $hasCredential, 'key');

        if ($propertyId !== '' && $hasCredential) {
            try {
                $gaData = Cache::remember('cms.analytics.ga4.'.sha1($propertyId.'|'.$credentialFingerprint), now()->addMinutes(10), fn () => $analytics->fetch());
                $metrics[] = $this->metricCard('Pengguna Aktif', number_format($gaData['totals']['activeUsers'] ?? 0, 0, ',', '.'), 'GA4');
                $metrics[] = $this->metricCard('Sesi', number_format($gaData['totals']['sessions'] ?? 0, 0, ',', '.'), 'GA4');
                $metrics[] = $this->metricCard('Page Views', number_format($gaData['totals']['pageViews'] ?? 0, 0, ',', '.'), 'GA4');
            } catch (Throwable $exception) {
                report($exception);
                $integrationErrors[] = 'GA4: '.$exception->getMessage();
            }
        }

        if ($siteUrl !== '' && $hasCredential) {
            try {
                $gscData = Cache::remember('cms.analytics.gsc.'.sha1($siteUrl.'|'.$credentialFingerprint), now()->addMinutes(10), fn () => $searchConsole->fetch());
                $metrics[] = $this->metricCard('Klik Organik', number_format($gscData['totals']['clicks'] ?? 0, 0, ',', '.'), 'Search Console');
                $metrics[] = $this->metricCard('Impresi', number_format($gscData['totals']['impressions'] ?? 0, 0, ',', '.'), 'Search Console');
                $metrics[] = $this->metricCard('Posisi Rata-rata', number_format($gscData['totals']['position'] ?? 0, 1, ',', '.'), 'Search Console');
            } catch (Throwable $exception) {
                report($exception);
                $integrationErrors[] = 'Search Console: '.$exception->getMessage();
            }
        }

        return view('cms.utility', [
            'title' => 'Analitik',
            'eyebrow' => 'Insights',
            'description' => 'Ringkasan GA4 dan Search Console 30 hari terakhir, dibaca langsung dari integrasi Google di Settings.',
            'connections' => $connections,
            'metrics' => $metrics,
            'gaData' => $gaData,
            'gscData' => $gscData,
            'integrationErrors' => $integrationErrors,
        ]);
    }

    private function statusCard(string $label, string $value, bool $configured, string $icon): array
    {
        return [
            'label' => $label,
            'value' => $value,
            'description' => $configured ? 'Konfigurasi tersimpan dan siap digunakan.' : 'Lengkapi nilainya di CMS Settings.',
            'status' => $configured ? 'Aktif' : 'Perlu setup',
            'icon' => $icon,
            'tone' => $configured ? 'success' : 'warning',
        ];
    }

    private function metricCard(string $label, string $value, string $source): array
    {
        return [
            'label' => $label,
            'value' => $value,
            'source' => $source,
        ];
    }

    public function profile(): View
    {
        return view('cms.profile', [
            'user' => auth()->user(),
        ]);
    }

    public function notFoundReport(Request $request): View
    {
        $days = in_array($request->integer('days'), [7, 30, 90], true) ? $request->integer('days') : 30;
        $search = mb_substr(trim((string) $request->query('q', '')), 0, 512);
        $escapedSearch = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);
        $from = now()->subDays($days - 1)->toDateString();

        $items = NotFoundUrl::query()
            ->selectRaw('path, SUM(occurrences) as occurrences, MIN(firstSeenAt) as firstSeenAt, MAX(lastSeenAt) as lastSeenAt')
            ->where('reportDate', '>=', $from)
            ->when($search !== '', fn ($query) => $query->whereRaw("path LIKE ? ESCAPE '!'", ["%{$escapedSearch}%"]))
            ->groupBy('path')
            ->orderByDesc('occurrences')
            ->orderByDesc('lastSeenAt')
            ->paginate(25)
            ->withQueryString();

        return view('cms.not-found-report', compact('days', 'search', 'items'));
    }
}
