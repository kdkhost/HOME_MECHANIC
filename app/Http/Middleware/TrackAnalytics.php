<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackAnalytics
{
    private AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Só rastrear requisições GET bem-sucedidas
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // Não rastrear rotas administrativas
            if (!$request->is('admin/*') && !$request->is('install*')) {
                $this->analyticsService->trackVisit($request);
            }
        }

        return $response;
    }
}