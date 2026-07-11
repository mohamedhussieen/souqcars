<?php

use App\Exceptions\Handler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'policy.accepted' => \App\Http\Middleware\EnsurePolicyAccepted::class,
            'admin'           => \App\Http\Middleware\EnsureIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(fn (Throwable $e, Request $request) => (new Handler(app()))->render($request, $e));
    })
    ->create();
