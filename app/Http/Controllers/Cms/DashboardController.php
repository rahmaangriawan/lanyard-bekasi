<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CityPage;
use App\Models\Comment;
use App\Models\Inquiry;
use App\Models\Media;
use App\Models\Order;
use App\Models\Page;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $productAggregate = $this->aggregateSeries(Product::class, true);
        $postAggregate = $this->aggregateSeries(Post::class);
        $inquiryAggregate = $this->aggregateSeries(Inquiry::class);
        $moduleCounts = $this->moduleCounts();

        $productTotal = $productAggregate['total'];
        $productPublished = $productAggregate['published'];
        $productDraft = max($productTotal - $productPublished, 0);
        $productPublishedPercent = $this->percent($productPublished, $productTotal);
        $productDraftPercent = $this->percent($productDraft, $productTotal);

        return view('cms.dashboard', [
            'stats' => [
                [
                    'label' => 'Produk',
                    'value' => $productTotal,
                    'caption' => 'KATALOG AKTIF DAN DRAFT',
                    'trend' => $this->trend($productAggregate['series']),
                    'tone' => 'amber',
                    'segments' => [
                        ['label' => 'Aktif', 'value' => $productPublished, 'percent' => $productPublishedPercent, 'tone' => 'success'],
                        ['label' => 'Draft', 'value' => $productDraft, 'percent' => $productDraftPercent, 'tone' => 'blue'],
                    ],
                ],
                [
                    'label' => 'Artikel',
                    'value' => $postAggregate['total'],
                    'caption' => 'KONTEN BLOG CMS',
                    'trend' => $this->trend($postAggregate['series']),
                    'tone' => 'blue',
                    'sparkline' => $postAggregate['series'],
                ],
                [
                    'label' => 'Inquiry',
                    'value' => $inquiryAggregate['total'],
                    'caption' => 'PESAN MASUK CUSTOMER',
                    'trend' => $this->trend($inquiryAggregate['series']),
                    'tone' => 'violet',
                    'sparkline' => $inquiryAggregate['series'],
                ],
            ],
            'recentPosts' => Post::query()
                ->latest('createdAt')
                ->limit(5)
                ->get(['id', 'title', 'slug', 'published', 'createdAt']),
            'recentInquiries' => Inquiry::query()
                ->latest('createdAt')
                ->limit(5)
                ->get(['id', 'name', 'email', 'phone', 'createdAt']),
            'moduleCounts' => [
                ['label' => 'Pages', 'caption' => 'Halaman CMS', 'count' => $moduleCounts['pages'], 'tone' => 'blue', 'href' => route('cms.index', 'pages')],
                ['label' => 'Categories', 'caption' => 'Kategori konten', 'count' => $moduleCounts['categories'], 'tone' => 'green', 'href' => route('cms.index', 'categories')],
                ['label' => 'Portfolio', 'caption' => 'Karya tampil', 'count' => $moduleCounts['portfolio'], 'tone' => 'violet', 'href' => route('cms.index', 'portfolio')],
                ['label' => 'Comments', 'caption' => 'Komentar masuk', 'count' => $moduleCounts['comments'], 'tone' => 'amber', 'href' => route('cms.index', 'comments')],
                ['label' => 'Orders', 'caption' => 'Request order', 'count' => $moduleCounts['orders'], 'tone' => 'blue', 'href' => route('cms.index', 'orders')],
                ['label' => 'City Pages', 'caption' => 'Landing kota', 'count' => $moduleCounts['city_pages'], 'tone' => 'green', 'href' => route('cms.index', 'city-pages')],
                ['label' => 'Settings', 'caption' => 'Key konfigurasi', 'count' => $moduleCounts['settings'], 'tone' => 'violet', 'href' => route('cms.index', 'settings')],
                ['label' => 'Media', 'caption' => 'File upload lokal', 'count' => $moduleCounts['media'], 'tone' => 'amber', 'href' => route('cms.index', 'media')],
            ],
        ]);
    }

    /**
     * @param class-string<Model> $model
     */
    private function aggregateSeries(string $model, bool $includePublished = false): array
    {
        $query = $model::query()->selectRaw('COUNT(*) AS aggregate_total');
        $start = now()->startOfMonth()->subMonths(11);

        for ($index = 0; $index < 12; $index++) {
            $month = $start->copy()->addMonths($index);
            $query->selectRaw(
                "SUM(CASE WHEN createdAt >= ? AND createdAt < ? THEN 1 ELSE 0 END) AS aggregate_month_{$index}",
                [$month->copy()->startOfMonth(), $month->copy()->addMonth()->startOfMonth()],
            );
        }

        if ($includePublished) {
            $query->selectRaw('SUM(CASE WHEN published = ? THEN 1 ELSE 0 END) AS aggregate_published', [true]);
        }

        $row = $query->first();

        return [
            'total' => (int) ($row?->aggregate_total ?? 0),
            'published' => (int) ($row?->aggregate_published ?? 0),
            'series' => array_map(
                fn (int $index) => (int) ($row?->{"aggregate_month_{$index}"} ?? 0),
                range(0, 11),
            ),
        ];
    }

    private function trend(array $series): string
    {
        $current = (int) ($series[11] ?? 0);
        $previous = (int) ($series[10] ?? 0);

        if ($previous === 0) {
            return $current > 0 ? '100%' : '0%';
        }

        return round((($current - $previous) / $previous) * 100, 1) . '%';
    }

    private function moduleCounts(): array
    {
        $row = DB::query()
            ->selectSub(Page::query()->selectRaw('COUNT(*)'), 'pages')
            ->selectSub(Category::query()->selectRaw('COUNT(*)'), 'categories')
            ->selectSub(Portfolio::query()->selectRaw('COUNT(*)'), 'portfolio')
            ->selectSub(Comment::query()->selectRaw('COUNT(*)'), 'comments')
            ->selectSub(Order::query()->selectRaw('COUNT(*)'), 'orders')
            ->selectSub(CityPage::query()->selectRaw('COUNT(*)'), 'city_pages')
            ->selectSub(Setting::query()->selectRaw('COUNT(*)'), 'settings')
            ->selectSub(Media::query()->selectRaw('COUNT(*)'), 'media')
            ->first();

        return collect((array) $row)
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    private function percent(int $value, int $total): int
    {
        if ($total <= 0) {
            return 0;
        }

        return (int) round(($value / $total) * 100);
    }
}
