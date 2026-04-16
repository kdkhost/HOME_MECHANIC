<?php
/**
 * Verificar Rotas do Instalador
 * Script para diagnosticar problemas com rotas
 */

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificar Rotas - HomeMechanic</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #FF6B00;
            border-bottom: 3px solid #FF6B00;
            padding-bottom: 10px;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 5px solid;
        }
        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .alert-info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        code {
            background: #f8f9fa;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
        .route-item {
            background: #f8f9fa;
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #FF6B00;
            border-radius: 5px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #FF6B00;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Verificar Rotas do Instalador</h1>
";

$basePath = dirname(__DIR__);

// Verificar se Laravel está carregado
try {
    require $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    
    echo "<div class='alert alert-success'>
        ✅ Laravel carregado com sucesso
    </div>";
    
    // Listar rotas do instalador
    echo "<h2>Rotas Registradas</h2>";
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    
    $installerRoutes = [];
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'install') !== false) {
            $installerRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $uri,
                'name' => $route->getName(),
                'action' => $route->getActionName()
            ];
        }
    }
    
    if (empty($installerRoutes)) {
        echo "<div class='alert alert-error'>
            ❌ Nenhuma rota de instalador encontrada!<br>
            <small>Isso pode indicar que o cache de rotas está desatualizado.</small>
        </div>";
        
        echo "<h3>Solução:</h3>";
        echo "<pre>php artisan route:clear
php artisan route:cache</pre>";
    } else {
        echo "<div class='alert alert-success'>
            ✅ Encontradas " . count($installerRoutes) . " rotas do instalador
        </div>";
        
        foreach ($installerRoutes as $route) {
            $methodColor = strpos($route['method'], 'POST') !== false ? '#28a745' : '#17a2b8';
            echo "<div class='route-item'>
                <strong style='color: {$methodColor};'>{$route['method']}</strong> 
                <code>/{$route['uri']}</code><br>
                <small>Nome: {$route['name']}</small><br>
                <small>Action: {$route['action']}</small>
            </div>";
        }
    }
    
    // Verificar especificamente a rota test-database
    echo "<h2>Verificação Específica</h2>";
    
    $testDatabaseExists = false;
    foreach ($installerRoutes as $route) {
        if (strpos($route['uri'], 'test-database') !== false || strpos($route['uri'], 'testar-banco') !== false) {
            $testDatabaseExists = true;
            echo "<div class='alert alert-success'>
                ✅ Rota de teste de banco encontrada: <code>{$route['uri']}</code>
            </div>";
        }
    }
    
    if (!$testDatabaseExists) {
        echo "<div class='alert alert-error'>
            ❌ Rota de teste de banco NÃO encontrada!<br>
            <small>Execute os comandos abaixo para corrigir:</small>
        </div>";
        
        echo "<pre>cd " . $basePath . "
git pull origin master
php artisan route:clear
php artisan route:cache
php artisan config:clear</pre>";
    }
    
    // Verificar arquivo de rotas
    echo "<h2>Arquivo de Rotas</h2>";
    
    $routeFile = $basePath . '/app/Modules/Installer/Routes/web.php';
    if (file_exists($routeFile)) {
        echo "<div class='alert alert-success'>
            ✅ Arquivo de rotas existe: <code>app/Modules/Installer/Routes/web.php</code>
        </div>";
        
        $routeContent = file_get_contents($routeFile);
        if (strpos($routeContent, 'test-database') !== false) {
            echo "<div class='alert alert-success'>
                ✅ Rota 'test-database' está definida no arquivo
            </div>";
        } else {
            echo "<div class='alert alert-error'>
                ❌ Rota 'test-database' NÃO está no arquivo<br>
                <small>Você precisa fazer git pull!</small>
            </div>";
        }
        
        echo "<h3>Conteúdo do Arquivo:</h3>";
        echo "<pre>" . htmlspecialchars($routeContent) . "</pre>";
    } else {
        echo "<div class='alert alert-error'>
            ❌ Arquivo de rotas NÃO existe!
        </div>";
    }
    
    // Verificar cache
    echo "<h2>Cache de Rotas</h2>";
    
    $routeCacheFile = $basePath . '/bootstrap/cache/routes-v7.php';
    if (file_exists($routeCacheFile)) {
        $cacheTime = filemtime($routeCacheFile);
        $cacheAge = time() - $cacheTime;
        
        echo "<div class='alert alert-info'>
            ⚠️ Cache de rotas existe<br>
            <small>Criado: " . date('d/m/Y H:i:s', $cacheTime) . " (" . round($cacheAge / 60) . " minutos atrás)</small>
        </div>";
        
        echo "<p><strong>O cache pode estar desatualizado!</strong> Execute:</p>";
        echo "<pre>php artisan route:clear
php artisan route:cache</pre>";
    } else {
        echo "<div class='alert alert-success'>
            ✅ Sem cache de rotas (rotas carregadas dinamicamente)
        </div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-error'>
        <strong>❌ Erro ao carregar Laravel:</strong><br>
        {$e->getMessage()}
    </div>";
}

echo "
        <div style='margin-top: 30px; text-align: center;'>
            <a href='/install/steps' class='btn'>Ir para Instalador</a>
            <a href='/test-env-auto.php' class='btn'>Teste de .env</a>
        </div>
        
        <div style='margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center;'>
            <strong>🔧 HomeMechanic System</strong><br>
            <small>Diagnóstico de Rotas</small>
        </div>
    </div>
</body>
</html>";
