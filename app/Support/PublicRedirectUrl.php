<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PublicRedirectUrl
{
    private const PROTECTED_PATHS = [
        '/api',
        '/build',
        '/confirm-password',
        '/css',
        '/dashboard',
        '/email',
        '/font-assets',
        '/forgot-password',
        '/images',
        '/js',
        '/kawruh',
        '/login',
        '/logout',
        '/media',
        '/register',
        '/reset-password',
        '/storage',
        '/uploads',
        '/vendor',
        '/verify-email',
    ];

    private const PROTECTED_EXACT_PATHS = [
        '/favicon.ico',
        '/city-sitemap.xml',
        '/page-sitemap.xml',
        '/post-sitemap.xml',
        '/product-sitemap.xml',
        '/robots.txt',
        '/sitemap.xml',
        '/sitemap.xsl',
        '/up',
    ];

    public static function normalizeSource(string $value, Request $request): string
    {
        $value = trim($value);
        self::assertSafeCharacters($value);

        if ($value === '' || str_starts_with($value, '//')) {
            throw new InvalidArgumentException('URL asal harus berupa path situs yang valid.');
        }

        $parts = parse_url($value);
        if ($parts === false) {
            throw new InvalidArgumentException('URL asal tidak valid.');
        }

        if (isset($parts['scheme']) || isset($parts['host'])) {
            $scheme = strtolower((string) ($parts['scheme'] ?? ''));
            $host = strtolower((string) ($parts['host'] ?? ''));
            if (! in_array($scheme, ['http', 'https'], true) || ! in_array($host, self::allowedHosts($request), true)) {
                throw new InvalidArgumentException('URL asal absolut harus menggunakan domain situs ini.');
            }
        } elseif (! str_starts_with($value, '/')) {
            throw new InvalidArgumentException('URL asal harus diawali dengan /.');
        }

        return self::normalizePath((string) ($parts['path'] ?? '/'));
    }

    public static function normalizeRequestPath(Request $request): string
    {
        return self::normalizePath('/'.ltrim($request->path(), '/'));
    }

    public static function normalizePath(string $path): string
    {
        $path = '/'.ltrim(trim($path), '/');
        $path = preg_replace('#/+#', '/', $path) ?: '/';
        $path = $path === '/' ? '/' : rtrim($path, '/');

        return Str::lower($path);
    }

    public static function isProtected(string $path): bool
    {
        $path = self::normalizePath($path);
        if (in_array($path, self::PROTECTED_EXACT_PATHS, true)) {
            return true;
        }

        foreach (self::PROTECTED_PATHS as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }

    public static function validateTarget(string $value): void
    {
        $value = trim($value);
        self::assertSafeCharacters($value);

        if ($value === '' || str_starts_with($value, '//')) {
            throw new InvalidArgumentException('URL tujuan harus berupa path internal atau URL http/https.');
        }

        $parts = parse_url($value);
        if ($parts === false) {
            throw new InvalidArgumentException('URL tujuan tidak valid.');
        }

        if (str_starts_with($value, '/')) {
            return;
        }

        if (! isset($parts['host']) || ! in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)) {
            throw new InvalidArgumentException('URL eksternal wajib menggunakan http:// atau https://.');
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new InvalidArgumentException('URL tujuan tidak boleh berisi kredensial.');
        }
    }

    public static function internalTargetPath(string $target, Request $request): ?string
    {
        $parts = parse_url($target);
        if ($parts === false) {
            return null;
        }

        if (str_starts_with($target, '/')) {
            return self::normalizePath((string) ($parts['path'] ?? '/'));
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        if ($host !== '' && in_array($host, self::allowedHosts($request), true)) {
            return self::normalizePath((string) ($parts['path'] ?? '/'));
        }

        return null;
    }

    public static function destination(string $target, Request $request): string
    {
        $fragment = null;
        $targetWithoutFragment = $target;
        if (str_contains($targetWithoutFragment, '#')) {
            [$targetWithoutFragment, $fragment] = explode('#', $targetWithoutFragment, 2);
        }

        $targetQuery = [];
        $base = $targetWithoutFragment;
        if (str_contains($targetWithoutFragment, '?')) {
            [$base, $rawTargetQuery] = explode('?', $targetWithoutFragment, 2);
            parse_str($rawTargetQuery, $targetQuery);
        }

        $query = array_replace($request->query(), $targetQuery);
        $destination = $base;
        if ($query !== []) {
            $destination .= '?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }
        if ($fragment !== null && $fragment !== '') {
            $destination .= '#'.$fragment;
        }

        return $destination;
    }

    private static function allowedHosts(Request $request): array
    {
        return collect([
            strtolower($request->getHost()),
            strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST)),
        ])->filter()->unique()->values()->all();
    }

    private static function assertSafeCharacters(string $value): void
    {
        if (preg_match('/[\x00-\x20\x7F\\\\]/', $value)) {
            throw new InvalidArgumentException('URL mengandung karakter yang tidak diperbolehkan.');
        }
    }
}
