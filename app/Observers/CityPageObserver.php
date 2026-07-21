<?php

namespace App\Observers;

use App\Models\CityPage;
use Illuminate\Support\Facades\Cache;

class CityPageObserver
{
    private function clearCache(): void
    {
        $cache = Cache::store(config('cache.public_store', 'file'));
        $cache->forget('public_sitemap_cities');
        $cache->put('seo_sitemap_dirty', true, now()->addDays(7));
    }

    /**
     * Handle the CityPage "created" event.
     */
    public function created(CityPage $cityPage): void
    {
        $this->clearCache();
    }

    /**
     * Handle the CityPage "updated" event.
     */
    public function updated(CityPage $cityPage): void
    {
        $this->clearCache();
    }

    /**
     * Handle the CityPage "deleted" event.
     */
    public function deleted(CityPage $cityPage): void
    {
        $this->clearCache();
    }

    /**
     * Handle the CityPage "restored" event.
     */
    public function restored(CityPage $cityPage): void
    {
        $this->clearCache();
    }

    /**
     * Handle the CityPage "force deleted" event.
     */
    public function forceDeleted(CityPage $cityPage): void
    {
        $this->clearCache();
    }
}
