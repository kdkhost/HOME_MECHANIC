<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Verificar se .env existe e tem APP_KEY válida
$envPath = __DIR__.'/../.env';
$envInstallerPath = __DIR__.'/../.env.installer';

// Função para verificar se APP_KEY é válida
function hasValidAppKey($envPath) {
    if (!file_exists($envPath)) {
        return false;
    }
    
    $content = file_get_contents($envPath);
    
    // Verificar se tem APP_KEY e se não está vazia
    if (preg_match('/^APP_KEY=(.+)$/m', $content, $matches)) {
        $key = trim($matches[1]);
        // Verificar se a chave não está vazia e tem tamanho mínimo
        return !empty($key) && strlen($key) > 10;
    }
    
    return false;
}

// Se .env não existe OU não tem APP_KEY válida, copiar do .env.installer
if (!hasValidAppKey($envPath) && file_exists($envInstallerPath)) {
    // Fazer backup do .env atual se existir
    if (file_exists($envPath)) {
        $backupPath = $envPath . '.backup.' . date('YmdHis');
        @copy($envPath, $backupPath);
    }
    
    // Copiar .env.installer para .env
    copy($envInstallerPath, $envPath);
    
    // Limpar caches do Laravel se existirem
    $cachePaths = [
        __DIR__.'/../bootstrap/cache/config.php',
        __DIR__.'/../bootstrap/cache/routes-v7.php',
        __DIR__.'/../bootstrap/cache/services.php',
    ];
    
    foreach ($cachePaths as $cachePath) {
        if (file_exists($cachePath)) {
            @unlink($cachePath);
        }
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
