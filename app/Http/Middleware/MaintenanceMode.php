<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Verificar se o modo de manutenção está ativo
            $maintenanceMode = \DB::table('settings')
                ->where('key', 'maintenance_mode')
                ->value('value');
                
            if ($maintenanceMode === '1' || $maintenanceMode === 'true') {
                $clientIp = $request->ip();
                
                // Verificar se o IP está na lista de permitidos
                $allowedIp = \DB::table('maintenance_ips')
                    ->where('ip_address', $clientIp)
                    ->where('active', true)
                    ->exists();
                
                // Se não está na lista de permitidos
                if (!$allowedIp) {
                    // Rotas admin só para IPs permitidos
                    if ($request->is('admin/*')) {
                        return response()->view('errors.503', [], 503)
                            ->header('Retry-After', 3600);
                    }
                    
                    // Outras rotas também bloqueadas
                    if (!$request->is('admin/*')) {
                        return response()->view('errors.503', [], 503)
                            ->header('Retry-After', 3600);
                    }
                }
            }
        } catch (\Exception $e) {
            // Se houver erro (ex: tabela não existe), continuar normalmente
            // Isso permite que o sistema funcione durante a instalação
        }
        
        return $next($request);
    }
}
