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
            // Verificar se é rota de instalação primeiro (para evitar loops)
            $isInstallRoute = $request->is('install*') || 
                             $request->is('test-*') || 
                             $request->is('debug-*') ||
                             $request->is('check-*') ||
                             $request->is('simple-*');
            
            // Verificar se sistema está instalado
            $installedFile = storage_path('installed');
            $isInstalled = file_exists($installedFile);
            
            // Se não está instalado E não é rota de instalação → redirecionar para instalação
            if (!$isInstalled && !$isInstallRoute) {
                return redirect('/install');
            }
            
            // Se está instalado E é rota de instalação → redirecionar para home
            if ($isInstalled && $isInstallRoute && !$request->is('test-*') && !$request->is('debug-*')) {
                return redirect('/');
            }
            
            return $next($request);
        } catch (\Exception $e) {
            // Se houver qualquer erro (ex: .env inválido, banco não conecta)
            // E não é rota de instalação, redirecionar para instalação
            if (!$request->is('install*') && 
                !$request->is('test-*') && 
                !$request->is('debug-*') &&
                !$request->is('check-*') &&
                !$request->is('simple-*')) {
                return redirect('/install');
            }
            
            // Se já está em rota de instalação, deixar passar
            return $next($request);
        }
    }
}
