<?php
/**
 * Script para rodar via browser no servidor:
 * Acesse: https://seudominio.com.br/_migrate_prod.php?key=hm2026
 * APAGUE após usar!
 */

if (($_GET['key'] ?? '') !== 'hm2026') {
    http_response_code(403);
    die('Acesso negado.');
}

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '<pre style="font-family:monospace;font-size:13px;padding:20px;">';
echo "=== DIAGNÓSTICO DO BANCO ===\n\n";

try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Conexão OK: " . DB::connection()->getDatabaseName() . "\n\n";

    // Tabelas existentes
    echo "=== TABELAS ===\n";
    $tables = DB::select('SHOW TABLES');
    foreach ($tables as $t) {
        $t = (array)$t;
        echo "  - " . array_values($t)[0] . "\n";
    }

    // Colunas da tabela users
    echo "\n=== COLUNAS: users ===\n";
    $cols = DB::select('DESCRIBE users');
    foreach ($cols as $c) {
        echo "  {$c->Field} | {$c->Type} | null:{$c->Null} | default:{$c->Default}\n";
    }

    // Dados dos usuários
    echo "\n=== USUÁRIOS ===\n";
    $users = DB::table('users')->select('id','name','email','role','avatar','created_at')->get();
    foreach ($users as $u) {
        echo "  id:{$u->id} | {$u->name} | avatar:" . ($u->avatar ?: 'NULL') . "\n";
    }

    // Rodar migrations pendentes
    echo "\n=== RODANDO MIGRATIONS ===\n";
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();

    // Verificar colunas após migration
    echo "\n=== COLUNAS users (após migrate) ===\n";
    $cols2 = DB::select('DESCRIBE users');
    foreach ($cols2 as $c) {
        echo "  {$c->Field} | {$c->Type}\n";
    }

} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p style="color:red;font-weight:bold;">⚠️ APAGUE ESTE ARQUIVO APÓS O USO!</p>';
