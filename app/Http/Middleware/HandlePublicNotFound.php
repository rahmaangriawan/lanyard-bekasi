<?php

namespace App\Http\Middleware;

use App\Support\PublicNotFoundReporter;
use App\Support\PublicRedirectUrl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandlePublicNotFound
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldHandle($request, $response)) {
            return $response;
        }

        app(PublicNotFoundReporter::class)->record($request);

        return response()
            ->view('errors.public-404', [], 404)
            ->header('Cache-Control', 'public, no-cache, must-revalidate');
    }

    private function shouldHandle(Request $request, Response $response): bool
    {
        if ($request->method() !== 'GET' || $response->getStatusCode() !== 404) {
            return false;
        }

        if ($request->expectsJson() || PublicRedirectUrl::isProtected(PublicRedirectUrl::normalizeRequestPath($request))) {
            return false;
        }

        $accept = strtolower((string) $request->header('Accept', ''));

        return $accept === '' || str_contains($accept, 'text/html');
    }
}
