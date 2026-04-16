<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $installedFile = storage_path('installed');
            $isInstalled = file_exists($installedFile);
            $isInstallRoute = $request->is('install*');
            
            // Se não está instalado E não é rota de instalação → redirecionar para instalação
            if (!$isInstalled && !$isInstallRoute) {
                return redirect('/install');
            }
            
            // Se está instalado E é rota de instalação → redirecionar para home
            if ($isInstalled && $isInstallRoute) {
                return redirect('/');
            }
            
            return $next($request);
        } catch (\Exception $e) {
            // Se houver qualquer erro (ex: .env não existe), redirecionar para instalação
            if (!$request->is('install*')) {
                return redirect('/install');
            }
            return $next($request);
        }
    }
}
