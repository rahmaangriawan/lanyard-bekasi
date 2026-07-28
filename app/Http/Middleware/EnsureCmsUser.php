<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCmsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->canAccessCms()) {
            abort(403);
        }

        return $next($request);
    }
}
