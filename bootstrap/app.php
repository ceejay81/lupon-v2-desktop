<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '_native/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });

        // Gracefully handle 419 Page Expired errors so the app never shows a hard 500/419 error page
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                return response()->json(['message' => 'Session expired. Please refresh the page.'], 419);
            }

            return redirect()->back()->withInput()->with('error', 'Your session expired due to inactivity. Please review your input and try again.');
        });
    })->create();

if (isset($_ENV['APP_STORAGE_PATH'])) {
    $app->useStoragePath($_ENV['APP_STORAGE_PATH']);
} elseif (isset($_SERVER['APP_STORAGE_PATH'])) {
    $app->useStoragePath($_SERVER['APP_STORAGE_PATH']);
}

return $app;
