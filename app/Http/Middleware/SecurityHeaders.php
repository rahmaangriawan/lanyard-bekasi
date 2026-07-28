<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $isProductionHttps = app()->environment('production') && $request->isSecure();

        if ((bool) env('CSP_ENABLED', true)) {
            $directives = [
                "default-src 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "script-src 'self' 'unsafe-inline' https://challenges.cloudflare.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "font-src 'self' data: https://fonts.gstatic.com",
                "img-src 'self' data: https:",
                "connect-src 'self' https://challenges.cloudflare.com",
                "frame-src 'self' https://www.google.com https://challenges.cloudflare.com",
            ];

            if ($isProductionHttps) {
                $directives[] = 'upgrade-insecure-requests';
            }

            $response->headers->set('Content-Security-Policy', implode('; ', $directives));
        }

        if (
            $isProductionHttps
            && (bool) env('HSTS_ENABLED', true)
        ) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($request->is('kawruh*') || $request->is('login*') || $request->is('api/cms*')) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        return $response;
    }
}
