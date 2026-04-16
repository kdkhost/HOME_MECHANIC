<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Verificar se .env existe, se não, copiar do .env.installer
$envPath = __DIR__.'/../.env';
$envInstallerPath = __DIR__.'/../.env.installer';

if (!file_exists($envPath) && file_exists($envInstallerPath)) {
    // Copiar .env.installer para .env para permitir que o instalador funcione
    copy($envInstallerPath, $envPath);
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
