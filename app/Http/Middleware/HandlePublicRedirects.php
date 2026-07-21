<?php

namespace App\Http\Middleware;

use App\Support\PublicRedirectRegistry;
use App\Support\PublicRedirectUrl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandlePublicRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        $sourcePath = PublicRedirectUrl::normalizeRequestPath($request);
        if (PublicRedirectUrl::isProtected($sourcePath)) {
            return $next($request);
        }

        $canonicalRedirect = $this->canonicalRedirect($request, $sourcePath);
        if ($canonicalRedirect !== null) {
            return redirect()->away($canonicalRedirect, 301);
        }

        $target = PublicRedirectRegistry::target($sourcePath);
        if ($target === null) {
            return $next($request);
        }

        return redirect()->away(PublicRedirectUrl::destination($target, $request), 301);
    }

    private function canonicalRedirect(Request $request, string $normalizedPath): ?string
    {
        $rawPath = (string) parse_url($request->getRequestUri(), PHP_URL_PATH);
        $query = $request->query();
        $filteredQuery = collect($query)->reject(function (mixed $value, string $key): bool {
            $key = strtolower($key);
            return str_starts_with($key, 'utm_') || in_array($key, ['gclid', 'fbclid', 'msclkid'], true);
        })->all();

        $pathChanged = $rawPath !== $normalizedPath;
        $queryChanged = $filteredQuery !== $query;
        if (! $pathChanged && ! $queryChanged) return null;

        $destination = $normalizedPath;
        if ($filteredQuery !== []) $destination .= '?'.http_build_query($filteredQuery, '', '&', PHP_QUERY_RFC3986);

        return $request->getSchemeAndHttpHost().$destination;
    }
}
