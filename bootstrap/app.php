<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\ModuleServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectTo(
            guests: '/admin/login',
            users: '/admin/dashboard'
        );

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->web(append: [
            \App\Http\Middleware\CheckInstalled::class,
            \App\Http\Middleware\MaintenanceMode::class,
            \App\Http\Middleware\TrackAnalytics::class,
        ]);

        // Alias para middleware de permissao
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 0;
                $minutes = ceil($retryAfter / 60);
                
                $timeMessage = $minutes > 1 
                    ? "Tente novamente em {$minutes} minutos" 
                    : "Tente novamente em {$retryAfter} segundos";
                
                return response()->json([
                    'success' => false,
                    'message' => "Muitas tentativas de login. {$timeMessage}.",
                    'retry_after' => $retryAfter,
                    'retry_after_minutes' => $minutes
                ], 429);
            }
            
            return response()->view('errors.429', [
                'retry_after' => $e->getHeaders()['Retry-After'] ?? 0
            ], 429);
        });
    })->create();
