<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Atualiza apenas uma vez por sessão ou a cada X minutos para evitar queries excessivas
            // Mas para garantir o registro imediato conforme pedido, vamos atualizar se for nulo ou se passaram mais de 5 min
            $lastAccess = $user->last_login_at;
            
            if (!$lastAccess || $lastAccess->diffInMinutes(now()) >= 5) {
                // Usamos updateQuietly para não disparar eventos de 'updated' se houver observers
                $user->updateQuietly([
                    'last_login_at' => now()
                ]);
            }
        }

        return $next($request);
    }
}
