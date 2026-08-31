<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            return response(
                "<h1>Original Boot Error:</h1>" .
                "<p><strong>Exception:</strong> " . get_class($e) . "</p>" .
                "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>" .
                "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>" .
                "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>",
                500
            );
        });
    })->create();

// Dynamic storage path for serverless Vercel read-only filesystem
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL') || env('APP_STORAGE')) {
    $storagePath = env('APP_STORAGE', '/tmp/storage');
    $app->useStoragePath($storagePath);
}

return $app;
