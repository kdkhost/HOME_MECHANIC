<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationMiddleware
{
    /**
     * Handle an incoming request.
     * Detecta e processa impersonacao de usuarios.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se ha uma sessao de impersonacao ativa
        if (session()->has('impersonate')) {
            $impersonatedUserId = session('impersonate.user_id');
            $originalUserId = session('impersonate.original_user_id');

            // Verificar se o usuario atual e o impersonado
            if (auth()->check() && auth()->id() === $impersonatedUserId) {
                // Carregar usuario original para verificacoes
                $originalUser = User::find($originalUserId);

                if ($originalUser && ($originalUser->isSuperAdmin() || $originalUser->permission_level >= 50)) {
                    // Compartilhar dados de impersonacao com as views
                    view()->share('impersonating', true);
                    view()->share('originalUser', $originalUser);
                    view()->share('impersonatedUser', auth()->user());
                } else {
                    // Usuario original nao tem permissao - encerrar impersonacao
                    session()->forget('impersonate');
                }
            }
        }

        return $next($request);
    }
}
