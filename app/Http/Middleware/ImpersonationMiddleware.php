<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationMiddleware
{
    /**
     * Rotas que requerem nivel minimo 50 (Admin/Superadmin) para acessar.
     * Usuario comum (nivel 10) nao pode acessar durante impersonacao.
     */
    private array $restrictedRoutes = [
        'admin.settings.*',
        'admin.permissions.*',
        'admin.seo.*',
        'admin.analytics.*',
        'admin.sponsors.*',
        'admin.upload.*',
        'admin.backup.*',
        'admin.maintenance.*',
        'admin.system.*',
    ];

    /**
     * Handle an incoming request.
     * Detecta e processa impersonacao de usuarios e aplica restricoes de seguranca.
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
                // Carregar usuarios
                $originalUser = User::find($originalUserId);
                $impersonatedUser = auth()->user();

                if ($originalUser && ($originalUser->isSuperAdmin() || $originalUser->permission_level >= 50)) {
                    // Compartilhar dados de impersonacao com as views
                    view()->share('impersonating', true);
                    view()->share('originalUser', $originalUser);
                    view()->share('impersonatedUser', $impersonatedUser);

                    // BLOQUEAR ACESSO A ROTAS RESTRITAS se o usuario impersonado for nivel 10
                    if ($impersonatedUser && $impersonatedUser->permission_level < 50) {
                        $currentRoute = $request->route()->getName() ?? '';

                        foreach ($this->restrictedRoutes as $pattern) {
                            // Converter wildcard para regex
                            $regex = '/^' . str_replace(['.', '*'], ['\.', '.*'], $pattern) . '$/';
                            if (preg_match($regex, $currentRoute)) {
                                // Bloquear acesso - redirecionar para dashboard
                                return redirect()->route('admin.dashboard.index')
                                    ->with('error', 'Esta área requer permissões de administrador. Você está acessando como usuário comum.');
                            }
                        }

                        // BLOQUEAR ACESSO A OUTROS USUARIOS (exceto si proprio)
                        if (str_starts_with($currentRoute, 'admin.users.') && $currentRoute !== 'admin.users.index') {
                            $userId = $request->route('id') ?? $request->route('user');
                            if ($userId && $userId != $impersonatedUser->id) {
                                return redirect()->route('admin.users.index')
                                    ->with('error', 'Você só pode acessar seu próprio perfil.');
                            }
                        }
                    }
                } else {
                    // Usuario original nao tem permissao - encerrar impersonacao
                    session()->forget('impersonate');
                }
            }
        }

        return $next($request);
    }
}
