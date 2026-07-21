<?php

namespace App\Support;

use App\Models\Post;
use RuntimeException;

final class OpenGraphImage
{
    private const WIDTH = 1200;
    private const HEIGHT = 630;

    public static function render(Post $post): string
    {
        if (! function_exists('imagecreatetruecolor')) {
            throw new RuntimeException('Ekstensi GD diperlukan untuk membuat Open Graph image.');
        }

        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagealphablending($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 15, 23, 42));

        $source = self::loadSource((string) $post->featuredImage);
        if ($source) {
            self::cover($canvas, $source);
            imagedestroy($source);
        }

        $overlay = imagecolorallocatealpha($canvas, 8, 15, 26, 24);
        imagefilledrectangle($canvas, 0, 0, self::WIDTH, self::HEIGHT, $overlay);
        $green = imagecolorallocate($canvas, 20, 135, 63);
        imagefilledrectangle($canvas, 0, 0, 18, self::HEIGHT, $green);

        $font = public_path('fonts/geist/Geist-700.ttf');
        $regular = public_path('fonts/geist/Geist-400.ttf');
        if (! is_file($font)) $font = $regular;
        if (! is_file($font)) throw new RuntimeException('Font Open Graph tidak ditemukan.');

        $white = imagecolorallocate($canvas, 255, 255, 255);
        $muted = imagecolorallocate($canvas, 218, 225, 232);
        imagettftext($canvas, 25, 0, 76, 96, $green, $font, 'PUSAT TALI LANYARD');

        $title = trim((string) $post->title) ?: 'Artikel Pusat Tali Lanyard';
        $lines = self::wrap($title, $font, 53, 980, 3);
        $y = 210;
        foreach ($lines as $line) {
            imagettftext($canvas, 53, 0, 76, $y, $white, $font, $line);
            $y += 72;
        }

        $category = trim((string) ($post->category?->name ?? 'Inspirasi Lanyard'));
        imagettftext($canvas, 24, 0, 76, 555, $muted, $regular, $category.'  •  pusattalilanyard.com');

        ob_start();
        imagepng($canvas, null, 8);
        $bytes = (string) ob_get_clean();
        imagedestroy($canvas);

        return $bytes;
    }

    private static function loadSource(string $value): mixed
    {
        $path = (string) parse_url(AssetUrl::relative($value) ?? '', PHP_URL_PATH);
        if ($path === '') return null;
        $candidate = str_starts_with($path, '/storage/')
            ? storage_path('app/public/'.substr($path, 9))
            : public_path(ltrim($path, '/'));
        if (! is_file($candidate)) return null;

        return match (strtolower(pathinfo($candidate, PATHINFO_EXTENSION))) {
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($candidate) : null,
            'png' => @imagecreatefrompng($candidate),
            'jpg', 'jpeg' => @imagecreatefromjpeg($candidate),
            default => null,
        };
    }

    private static function cover(mixed $canvas, mixed $source): void
    {
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = max(self::WIDTH / $sourceWidth, self::HEIGHT / $sourceHeight);
        $cropWidth = (int) round(self::WIDTH / $scale);
        $cropHeight = (int) round(self::HEIGHT / $scale);
        $sourceX = max(0, (int) (($sourceWidth - $cropWidth) / 2));
        $sourceY = max(0, (int) (($sourceHeight - $cropHeight) / 2));
        imagecopyresampled($canvas, $source, 0, 0, $sourceX, $sourceY, self::WIDTH, self::HEIGHT, $cropWidth, $cropHeight);
    }

    private static function wrap(string $text, string $font, int $size, int $maxWidth, int $maxLines): array
    {
        $words = preg_split('/\s+/u', $text) ?: [];
        $lines = [];
        $line = '';
        foreach ($words as $word) {
            $candidate = trim($line.' '.$word);
            $box = imagettfbbox($size, 0, $font, $candidate);
            if ($line !== '' && ($box[2] - $box[0]) > $maxWidth) {
                $lines[] = $line;
                $line = $word;
                if (count($lines) === $maxLines - 1) break;
            } else {
                $line = $candidate;
            }
        }
        if ($line !== '' && count($lines) < $maxLines) $lines[] = $line;

        return $lines;
    }
}
