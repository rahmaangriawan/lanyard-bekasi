<?php

namespace App\Support;

use App\Models\PublicRedirect;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

class PublicRedirectRegistry
{
    public const CACHE_KEY = 'public_redirects_v1';

    public static function target(string $sourcePath): ?string
    {
        try {
            $redirects = Cache::store(config('cache.public_store', 'file'))->rememberForever(
                self::CACHE_KEY,
                fn (): array => PublicRedirect::query()
                    ->where('active', true)
                    ->pluck('targetUrl', 'sourcePath')
                    ->all()
            );
        } catch (QueryException) {
            return null;
        }

        $target = $redirects[$sourcePath] ?? null;

        return is_string($target) && $target !== '' ? $target : null;
    }

    public static function forget(): void
    {
        Cache::store(config('cache.public_store', 'file'))->forget(self::CACHE_KEY);
    }
}
