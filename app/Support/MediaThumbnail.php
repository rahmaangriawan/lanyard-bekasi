<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class MediaThumbnail
{
    public const MAX_WIDTH = 480;

    public static function path(Media $media): string
    {
        return 'thumbnails/media/'.$media->getKey().'-'.self::MAX_WIDTH.'.webp';
    }

    public static function url(Media $media): ?string
    {
        $path = self::path($media);

        return Storage::disk('public')->exists($path) ? Storage::disk('public')->url($path) : null;
    }

    public static function generate(Media $media, bool $force = false): ?string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return null;
        }

        if (! preg_match('#^image/(?:jpeg|png|webp)$#i', (string) $media->mimetype)) {
            return null;
        }

        $target = self::path($media);
        if (! $force && Storage::disk('public')->exists($target)) {
            return Storage::disk('public')->url($target);
        }

        $source = self::sourcePath($media);
        if ($source === null || ! is_file($source)) {
            return null;
        }

        try {
            $bytes = file_get_contents($source);
            $image = is_string($bytes) ? imagecreatefromstring($bytes) : false;
            if ($image === false) {
                return null;
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $targetWidth = min(self::MAX_WIDTH, $width);
            $targetHeight = max(1, (int) round($height * ($targetWidth / max(1, $width))));
            $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

            ob_start();
            $written = imagewebp($thumbnail, null, 82);
            $output = ob_get_clean();
            imagedestroy($thumbnail);
            imagedestroy($image);

            if (! $written || ! is_string($output) || $output === '') {
                return null;
            }

            Storage::disk('public')->put($target, $output, ['visibility' => 'public']);

            return Storage::disk('public')->url($target);
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public static function generateResponsive(Media $media, bool $force = false): int
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return 0;
        }

        if (! preg_match('#^image/(?:jpeg|png|webp)$#i', (string) $media->mimetype)) {
            return 0;
        }

        $source = self::sourcePath($media);
        $diskPath = $media->disk_path;
        if ($source === null || $diskPath === '' || ! is_file($source)) {
            return 0;
        }

        $bytes = file_get_contents($source);
        $image = is_string($bytes) ? imagecreatefromstring($bytes) : false;
        if ($image === false) {
            return 0;
        }

        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);
        $base = preg_replace('/\.[^.]+$/', '', $diskPath) ?: $diskPath;
        $generated = 0;

        foreach ([320, 640] as $width) {
            if ($sourceWidth <= $width) {
                continue;
            }
            $target = $base.'-'.$width.'w.webp';
            if (! $force && Storage::disk('public')->exists($target)) {
                $generated++;
                continue;
            }

            $height = max(1, (int) round($sourceHeight * ($width / $sourceWidth)));
            $resized = imagecreatetruecolor($width, $height);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
            ob_start();
            $written = imagewebp($resized, null, 80);
            $output = ob_get_clean();
            imagedestroy($resized);

            if ($written && is_string($output) && $output !== '') {
                Storage::disk('public')->put($target, $output, ['visibility' => 'public']);
                $generated++;
            }
        }

        imagedestroy($image);

        return $generated;
    }

    private static function sourcePath(Media $media): ?string
    {
        if ($media->disk_path !== '' && Storage::disk('public')->exists($media->disk_path)) {
            return Storage::disk('public')->path($media->disk_path);
        }

        $path = rawurldecode((string) parse_url($media->preview_url, PHP_URL_PATH));
        if (str_starts_with($path, '/storage/')) {
            $diskPath = ltrim(substr($path, strlen('/storage/')), '/');
            if (Storage::disk('public')->exists($diskPath)) {
                return Storage::disk('public')->path($diskPath);
            }
        }

        $publicPath = public_path(ltrim($path, '/'));

        return is_file($publicPath) ? $publicPath : null;
    }
}
