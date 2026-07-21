<?php

namespace App\Providers;

use App\Models\CityPage;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use App\Observers\CityPageObserver;
use App\Observers\PostObserver;
use App\Observers\ProductObserver;
use App\Observers\SettingObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Product::observe(ProductObserver::class);
        Post::observe(PostObserver::class);
        CityPage::observe(CityPageObserver::class);
        Setting::observe(SettingObserver::class);

        Vite::prefetch(concurrency: 3);
    }
}
