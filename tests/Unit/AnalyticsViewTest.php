<?php

namespace Tests\Unit;

use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class AnalyticsViewTest extends TestCase
{
    public function test_analytics_page_renders_trend_charts_and_top_pages(): void
    {
        view()->share('errors', new ViewErrorBag);

        $daily = [
            ['date' => '2026-07-16', 'activeUsers' => 15, 'pageViews' => 37],
            ['date' => '2026-07-14', 'activeUsers' => 12, 'pageViews' => 25],
            ['date' => '2026-07-15', 'activeUsers' => 18, 'pageViews' => 42],
        ];

        $html = view('cms.utility', [
            'title' => 'Analitik',
            'description' => 'Ringkasan analitik.',
            'connections' => [
                ['status' => 'Aktif', 'label' => 'GA4', 'value' => '123456'],
            ],
            'metrics' => [
                ['source' => 'GA4', 'value' => '45', 'label' => 'Pengguna Aktif'],
            ],
            'gaData' => [
                'daily' => $daily,
                'topPages' => [['path' => '/blog/test', 'title' => 'Test', 'views' => 42]],
            ],
            'gscData' => [
                'daily' => [
                    ['date' => '2026-07-14', 'clicks' => 4, 'impressions' => 80],
                    ['date' => '2026-07-15', 'clicks' => 6, 'impressions' => 110],
                ],
            ],
            'integrationErrors' => [],
        ])->render();

        $this->assertStringContainsString('Traffic Website', $html);
        $this->assertStringContainsString('Performa Pencarian Organik', $html);
        $this->assertStringContainsString('/blog/test', $html);
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'class="analytics-chart"'));
        $this->assertGreaterThanOrEqual(4, substr_count($html, '<polyline'));
        $this->assertLessThan(strpos($html, '2026-07-16: 15 pengguna'), strpos($html, '2026-07-14: 12 pengguna'));
    }
}
