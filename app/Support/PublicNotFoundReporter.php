<?php

namespace App\Support;

use App\Models\NotFoundUrl;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicNotFoundReporter
{
    private const RETENTION_DAYS = 90;

    public function record(Request $request): void
    {
        $path = PublicRedirectUrl::normalizeRequestPath($request);
        $date = now()->toDateString();
        $cache = Cache::store(config('cache.public_store', 'file'));
        $throttleKey = 'public_not_found:seen:'.sha1($date.'|'.$path);

        if (! $cache->add($throttleKey, true, now()->addMinute())) {
            return;
        }

        try {
            $this->pruneExpired($cache);

            $record = NotFoundUrl::query()->firstOrCreate(
                ['path' => $path, 'reportDate' => $date],
                [
                    'occurrences' => 0,
                    'firstSeenAt' => now(),
                    'lastSeenAt' => now(),
                ],
            );

            $record->increment('occurrences', 1, ['lastSeenAt' => now()]);
        } catch (QueryException) {
            // Deploy tetap aman bila request datang sebelum migration selesai.
        }
    }

    private function pruneExpired($cache): void
    {
        $pruneKey = 'public_not_found:pruned:'.now()->toDateString();
        if (! $cache->add($pruneKey, true, now()->endOfDay())) {
            return;
        }

        NotFoundUrl::query()
            ->where('lastSeenAt', '<', now()->subDays(self::RETENTION_DAYS))
            ->delete();
    }
}
