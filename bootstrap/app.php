<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo('/login');
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);
        $middleware->alias([
            'auth.iot' => \App\Http\Middleware\IotApiAuth::class,
            'auth.api' => \App\Http\Middleware\ApiAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect('/login')->withErrors(['login' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $allowed = $e->getHeaders()['Allow'] ?? 'POST';
                return response()->json([
                    'success' => false,
                    'message' => "Metode HTTP {$request->method()} tidak didukung untuk endpoint ini.",
                    'supported_methods' => explode(', ', $allowed),
                    'endpoint' => '/' . ltrim($request->path(), '/'),
                ], 405);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint API tidak ditemukan.',
                    'endpoint' => '/' . ltrim($request->path(), '/'),
                ], 404);
            }
        });
    })->create();
