<?php

namespace App\Console\Commands;

use App\Services\GoogleSearchConsoleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SubmitSitemapToSearchConsole extends Command
{
    protected $signature = 'seo:submit-sitemap {--force : Kirim walaupun sitemap tidak ditandai berubah}';
    protected $description = 'Mengirim sitemap index ke Google Search Console melalui API resmi';

    public function handle(GoogleSearchConsoleService $service): int
    {
        $cache = Cache::store(config('cache.public_store', 'file'));
        if (! $this->option('force') && ! $cache->get('seo_sitemap_dirty', false)) {
            $this->info('Sitemap tidak berubah; submit dilewati.');
            return self::SUCCESS;
        }

        try {
            $result = $service->submitSitemap();
            $cache->forget('seo_sitemap_dirty');
            $cache->put('seo_sitemap_last_submission', $result, now()->addDays(30));
            $this->info('Sitemap berhasil dikirim: '.$result['sitemapUrl']);
            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }
}
