<?php

use App\Http\Middleware\MoonshineBasicAuth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'moonshine.basic' => MoonshineBasicAuth::class,
        ]);
        $middleware->trustProxies(at: [
            '127.0.0.1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
