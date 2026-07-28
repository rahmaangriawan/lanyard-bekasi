<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

final class MediaUpload
{
    /**
     * @return array{filename: string, filepath: string, mimetype: string, size: int, url: string}
     */
    public static function store(UploadedFile $file): array
    {
        $mime = strtolower((string) $file->getMimeType());

        if (in_array($mime, ['image/jpeg', 'image/png'], true)
            && function_exists('imagecreatefromstring')
            && function_exists('imagewebp')) {
            $bytes = file_get_contents($file->getRealPath());
            $image = is_string($bytes) ? @imagecreatefromstring($bytes) : false;

            if ($image !== false) {
                imagepalettetotruecolor($image);
                imagealphablending($image, false);
                imagesavealpha($image, true);

                ob_start();
                $written = imagewebp($image, null, 82);
                $webp = ob_get_clean();
                imagedestroy($image);

                if ($written && is_string($webp) && $webp !== '') {
                    $filename = Str::uuid().'.webp';
                    $path = 'cms/'.$filename;
                    Storage::disk('public')->put($path, $webp, ['visibility' => 'public']);

                    return self::metadata($filename, $path, 'image/webp', strlen($webp));
                }
            }
        }

        $path = $file->store('cms', 'public');
        if (! is_string($path) || $path === '') {
            throw new RuntimeException('File gagal disimpan ke storage publik.');
        }

        return self::metadata(
            basename($path),
            $path,
            $mime !== '' ? $mime : 'application/octet-stream',
            (int) $file->getSize(),
        );
    }

    /**
     * @return array{filename: string, filepath: string, mimetype: string, size: int, url: string}
     */
    private static function metadata(string $filename, string $path, string $mime, int $size): array
    {
        if (! Storage::disk('public')->exists($path)) {
            throw new RuntimeException('File gagal disimpan ke storage publik.');
        }

        return [
            'filename' => $filename,
            'filepath' => $path,
            'mimetype' => $mime,
            'size' => $size,
            'url' => Storage::disk('public')->url($path),
        ];
    }
}
