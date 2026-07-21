<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Support\MediaThumbnail;
use Illuminate\Console\Command;

class GenerateMediaThumbnails extends Command
{
    protected $signature = 'media:thumbnails {--force : Regenerate thumbnails that already exist}';

    protected $description = 'Generate 480px WebP previews for Media Library images';

    public function handle(): int
    {
        $generated = 0;
        $fallback = 0;

        Media::query()->orderBy('id')->chunkById(100, function ($media) use (&$generated, &$fallback): void {
            foreach ($media as $item) {
                if (MediaThumbnail::generate($item, (bool) $this->option('force')) !== null) {
                    $generated++;
                } else {
                    $fallback++;
                }
                MediaThumbnail::generateResponsive($item, (bool) $this->option('force'));
            }
        });

        $this->info("Thumbnail tersedia: {$generated}; fallback original: {$fallback}.");

        return self::SUCCESS;
    }
}
