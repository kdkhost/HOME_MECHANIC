<?php
/**
 * Script para testar funcionalidades do admin
 * Acesse: /test-admin-functions.php
 */

echo "🧪 Teste das Funcionalidades Admin\n\n";

// Carregar Laravel
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    echo "✅ Laravel carregado com sucesso!\n\n";
    
    // Testar rotas
    echo "🔍 Testando rotas disponíveis...\n";
    
    $routes = [
        'admin.dashboard.index' => 'Dashboard',
        'admin.analytics.index' => 'Analytics',
        'admin.gallery.index' => 'Galeria',
        'admin.blog.index' => 'Blog',
        'admin.contact.index' => 'Mensagens',
        'admin.settings.index' => 'Configurações',
        'admin.users.index' => 'Usuários',
        'admin.documentation.index' => 'Documentação',
        'admin.login' => 'Login',
        'admin.logout' => 'Logout'
    ];
    
    $routeCollection = app('router')->getRoutes();
    
    foreach ($routes as $routeName => $description) {
        try {
            $route = $routeCollection->getByName($routeName);
            if ($route) {
                echo "   ✅ {$description}: {$routeName}\n";
            } else {
                echo "   ❌ {$description}: {$routeName} (não encontrada)\n";
            }
        } catch (Exception $e) {
            echo "   ❌ {$description}: {$routeName} (erro: {$e->getMessage()})\n";
        }
    }
    
    echo "\n🔍 Testando controllers...\n";
    
    $controllers = [
        'App\Modules\Dashboard\Controllers\DashboardController' => 'Dashboard',
        'App\Modules\Analytics\Controllers\AnalyticsController' => 'Analytics',
        'App\Modules\Gallery\Controllers\GalleryController' => 'Galeria',
        'App\Modules\Blog\Controllers\BlogController' => 'Blog',
        'App\Modules\Contact\Controllers\ContactController' => 'Contato',
        'App\Modules\Settings\Controllers\SettingsController' => 'Configurações',
        'App\Modules\Users\Controllers\UsersController' => 'Usuários',
        'App\Modules\Auth\Controllers\AuthController' => 'Autenticação'
    ];
    
    foreach ($controllers as $class => $description) {
        if (class_exists($class)) {
            echo "   ✅ {$description}: {$class}\n";
        } else {
            echo "   ❌ {$description}: {$class} (não encontrada)\n";
        }
    }
    
    echo "\n🔍 Testando views...\n";
    
    $views = [
        'layouts.admin' => 'Layout Admin',
        'modules.dashboard.index' => 'Dashboard',
        'modules.analytics.index' => 'Analytics',
        'modules.blog.index' => 'Blog',
        'modules.contact.index' => 'Contato',
        'modules.settings.index' => 'Configurações',
        'modules.users.index' => 'Usuários'
    ];
    
    foreach ($views as $viewName => $description) {
        try {
            $viewPath = resource_path('views/' . str_replace('.', '/', $viewName) . '.blade.php');
            if (file_exists($viewPath)) {
                echo "   ✅ {$description}: {$viewName}\n";
            } else {
                echo "   ❌ {$description}: {$viewName} (arquivo não encontrado)\n";
            }
        } catch (Exception $e) {
            echo "   ❌ {$description}: {$viewName} (erro: {$e->getMessage()})\n";
        }
    }
    
    echo "\n🔍 Testando configurações...\n";
    
    // Testar .env
    if (file_exists(__DIR__ . '/../.env')) {
        echo "   ✅ Arquivo .env existe\n";
        
        $envVars = ['APP_KEY', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME'];
        foreach ($envVars as $var) {
            $value = env($var);
            if ($value !== null) {
                echo "   ✅ {$var}: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "\n";
            } else {
                echo "   ❌ {$var}: não definida\n";
            }
        }
    } else {
        echo "   ❌ Arquivo .env não encontrado\n";
    }
    
    // Testar conexão com banco
    echo "\n🔍 Testando conexão com banco...\n";
    try {
        DB::connection()->getPdo();
        echo "   ✅ Conexão com banco estabelecida\n";
        
        // Testar tabela users
        if (Schema::hasTable('users')) {
            $userCount = DB::table('users')->count();
            echo "   ✅ Tabela users existe ({$userCount} registros)\n";
        } else {
            echo "   ⚠️  Tabela users não existe (execute o instalador)\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Erro de conexão: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao carregar Laravel: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 RESUMO:\n";
echo "- Verifique os itens marcados com ❌\n";
echo "- Inicie o MySQL se necessário\n";
echo "- Execute o instalador se as tabelas não existirem\n";
echo "- Acesse /admin para testar o painel\n\n";

echo "Teste concluído em " . date('d/m/Y H:i:s') . "\n";
?>