<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Registrar os serviços da aplicação.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializar os serviços da aplicação.
     */
    public function boot(): void
    {
        $this->loadModuleRoutes();
    }

    /**
     * Carregar automaticamente as rotas de todos os módulos.
     */
    protected function loadModuleRoutes(): void
    {
        // Carregar rotas web dos módulos
        $webRoutes = glob(app_path('Modules/*/Routes/web.php'));
        foreach ($webRoutes as $routeFile) {
            Route::middleware('web')->group($routeFile);
        }

        // Carregar rotas API dos módulos
        $apiRoutes = glob(app_path('Modules/*/Routes/api.php'));
        foreach ($apiRoutes as $routeFile) {
            Route::middleware('api')
                ->prefix('api')
                ->group($routeFile);
        }
    }
}