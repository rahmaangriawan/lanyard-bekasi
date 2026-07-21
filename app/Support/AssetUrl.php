<?php

namespace App\Support;

final class AssetUrl
{
    private const LOCAL_DIRECTORIES = ['storage', 'uploads', 'images', 'image', 'media', 'fonts', 'css', 'js', 'vendor'];

    public static function relative(?string $value, ?string $fallback = null): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return $fallback;
        }

        if (preg_match('/^(data:|blob:|#)/i', $value)) {
            return $value;
        }

        $value = str_replace('\\', '/', html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $parts = parse_url($value);

        if (is_array($parts) && isset($parts['scheme'], $parts['host'])) {
            $path = (string) ($parts['path'] ?? '/');
            $firstSegment = explode('/', ltrim($path, '/'))[0] ?? '';
            $isLocalHost = in_array(strtolower((string) $parts['host']), ['localhost', '127.0.0.1', '::1'], true);

            if (! $isLocalHost && ! in_array(strtolower($firstSegment), self::LOCAL_DIRECTORIES, true)) {
                return $value;
            }

            $value = $path;
            if (! empty($parts['query'])) {
                $value .= '?'.$parts['query'];
            }
        }

        $value = '/'.ltrim($value, '/');
        $value = preg_replace('#^/storage/app/public/#i', '/storage/', $value);
        $value = preg_replace('#^/public/storage/#i', '/storage/', $value);
        $value = preg_replace('#^/public/(uploads|images|image|media)/#i', '/$1/', $value);
        $value = preg_replace('#^/storage/storage/#i', '/storage/', $value);

        if ($fallback !== null && self::isManagedPath($value) && ! self::exists($value)) {
            return self::relative($fallback);
        }

        return $value;
    }

    public static function html(?string $html): string
    {
        $html = (string) $html;

        $html = preg_replace_callback(
            '/\b(src|poster)\s*=\s*(["\'])(.*?)\2/is',
            fn (array $match) => $match[1].'='.$match[2].e(self::relative($match[3]) ?? '').$match[2],
            $html,
        );

        $html = preg_replace_callback(
            '/\bsrcset\s*=\s*(["\'])(.*?)\1/is',
            function (array $match): string {
                if (str_starts_with(trim($match[2]), 'data:')) {
                    return $match[0];
                }

                $candidates = array_map(function (string $candidate): string {
                    $parts = preg_split('/\s+/', trim($candidate), 2) ?: [];
                    $url = self::relative($parts[0] ?? '') ?? '';

                    return trim($url.' '.($parts[1] ?? ''));
                }, explode(',', $match[2]));

                return 'srcset='.$match[1].e(implode(', ', $candidates)).$match[1];
            },
            $html,
        );

        $html = preg_replace_callback(
            '/url\(\s*(["\']?)(https?:\/\/[^)"\']+)\1\s*\)/i',
            fn (array $match) => 'url('.$match[1].(self::relative($match[2]) ?? '').$match[1].')',
            $html,
        );

        return $html;
    }

    private static function isManagedPath(string $path): bool
    {
        $firstSegment = explode('/', ltrim((string) parse_url($path, PHP_URL_PATH), '/'))[0] ?? '';

        return in_array(strtolower($firstSegment), self::LOCAL_DIRECTORIES, true);
    }

    private static function exists(string $path): bool
    {
        $path = (string) parse_url($path, PHP_URL_PATH);

        if (str_starts_with($path, '/storage/')) {
            return is_file(storage_path('app/public/'.substr($path, strlen('/storage/'))));
        }

        return is_file(public_path(ltrim($path, '/')));
    }
}
