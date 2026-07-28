<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureCmsUser;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HandlePublicNotFound;
use App\Http\Middleware\HandlePublicRedirects;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $trustedProxies = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TRUSTED_PROXIES', '127.0.0.1,::1')),
        )));
        $middleware->trustProxies(at: $trustedProxies);

        $middleware->prepend(HandlePublicRedirects::class);
        $middleware->append(SecurityHeaders::class);
        $middleware->append(HandlePublicNotFound::class);

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'cms' => EnsureCmsUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
