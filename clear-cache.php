<?php
/**
 * HomeMechanic - Script de Limpeza de Cache
 * 
 * Este script funciona via browser OU terminal.
 * Compatível com hospedagem compartilhada (sem exec()).
 * Utiliza o bootstrap do Laravel para chamar Artisan diretamente.
 */

$isCli = php_sapi_name() === 'cli';
$nl    = $isCli ? "\n" : "<br>";
$bold  = function($t) use ($isCli) { return $isCli ? $t : "<strong>{$t}</strong>"; };

if (!$isCli) {
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>HomeMechanic - Limpeza de Cache</title>';
    echo '<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:2rem;max-width:800px;margin:auto;}';
    echo '.ok{color:#4ade80;}.err{color:#f87171;}.warn{color:#fbbf24;}.title{color:#FF6B00;font-size:1.3rem;font-weight:bold;}';
    echo 'pre{background:#16213e;padding:1rem;border-radius:8px;overflow-x:auto;}</style></head><body>';
}

echo "{$nl}";
echo $bold("🔧 HomeMechanic - Limpeza de Cache") . $nl;
echo str_repeat('=', 40) . $nl . $nl;

// ── Bootstrap do Laravel ──────────────────────────────────
$base_dir = __DIR__;

if (!file_exists($base_dir . '/artisan')) {
    echo "❌ Erro: Execute na raiz do projeto Laravel{$nl}";
    exit(1);
}

// Carregar autoloader e app do Laravel
require $base_dir . '/vendor/autoload.php';
$app = require_once $base_dir . '/bootstrap/app.php';

try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
} catch (Throwable $e) {
    echo "⚠️  Bootstrap parcial — prosseguindo com limpeza manual...{$nl}";
}

$results = [];

// ── Função auxiliar para rodar Artisan commands ───────────
function artisanCall(string $command, string $label, &$results, $nl) {
    try {
        Illuminate\Support\Facades\Artisan::call($command);
        $output = trim(Illuminate\Support\Facades\Artisan::output());
        echo "✅ {$label}" . ($output ? " — {$output}" : "") . $nl;
        $results[] = ['ok', $label];
    } catch (Throwable $e) {
        echo "❌ {$label}: " . $e->getMessage() . $nl;
        $results[] = ['err', $label, $e->getMessage()];
    }
}

// ── Função para limpar diretório manualmente ─────────────
function clearDir(string $dir, string $label, &$results, $nl) {
    if (!is_dir($dir)) {
        echo "⚠️  {$label}: diretório não encontrado{$nl}";
        @mkdir($dir, 0755, true);
        return;
    }

    $count = 0;
    $iter  = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iter as $item) {
        if ($item->isFile() && $item->getBasename() !== '.gitignore') {
            @unlink($item->getRealPath()) && $count++;
        }
    }

    echo "✅ {$label}: {$count} arquivo(s) removido(s){$nl}";
    $results[] = ['ok', $label];
}

echo $bold("1️⃣  Limpando caches via Artisan") . $nl;
echo str_repeat('-', 40) . $nl;

artisanCall('config:clear', 'Cache de configuração', $results, $nl);
artisanCall('route:clear',  'Cache de rotas',        $results, $nl);
artisanCall('view:clear',   'Views compiladas',      $results, $nl);
artisanCall('cache:clear',  'Cache de aplicação',    $results, $nl);

try {
    artisanCall('event:clear', 'Cache de eventos', $results, $nl);
} catch (Throwable $e) {
    // event:clear nem sempre existe
}

echo $nl . $bold("2️⃣  Limpando diretórios de cache") . $nl;
echo str_repeat('-', 40) . $nl;

clearDir($base_dir . '/storage/framework/cache/data', 'Framework cache/data', $results, $nl);
clearDir($base_dir . '/storage/framework/views',      'Views compiladas',     $results, $nl);
clearDir($base_dir . '/storage/framework/sessions',   'Sessões',              $results, $nl);

echo $nl . $bold("3️⃣  Recriando caches otimizados") . $nl;
echo str_repeat('-', 40) . $nl;

artisanCall('config:cache', 'Gerar config cache', $results, $nl);
artisanCall('route:cache',  'Gerar route cache',  $results, $nl);

echo $nl . $bold("4️⃣  Verificando permissões") . $nl;
echo str_repeat('-', 40) . $nl;

$dirs = [
    'storage', 'storage/app', 'storage/app/public',
    'storage/framework', 'storage/framework/cache',
    'storage/framework/sessions', 'storage/framework/views',
    'storage/logs', 'bootstrap/cache'
];

foreach ($dirs as $d) {
    $p = $base_dir . '/' . $d;
    if (!is_dir($p)) {
        @mkdir($p, 0755, true);
        echo "📁 {$d} — criado{$nl}";
    } elseif (!is_writable($p)) {
        @chmod($p, 0755);
        echo "⚠️  {$d} — sem permissão (tentei corrigir){$nl}";
    } else {
        echo "✅ {$d} — OK{$nl}";
    }
}

// ── Resumo ─────────────────────────────────────────────────
$ok  = count(array_filter($results, fn($r) => $r[0] === 'ok'));
$err = count(array_filter($results, fn($r) => $r[0] === 'err'));

echo $nl . str_repeat('=', 40) . $nl;
echo $bold("🎉 Concluído! {$ok} sucesso(s), {$err} erro(s)") . $nl . $nl;

echo "📋 Próximos passos:{$nl}";
echo "  1. Teste: https://homemechanic.com.br/{$nl}";
echo "  2. Painel: https://homemechanic.com.br/admin/login{$nl}";
echo $nl;

if ($err > 0) {
    echo "⚠️  Erros encontrados:{$nl}";
    foreach ($results as $r) {
        if ($r[0] === 'err') echo "  ❌ {$r[1]}: {$r[2]}{$nl}";
    }
    echo $nl;
}

echo "🔧 HomeMechanic v1.0.0 — Cache limpo com sucesso!{$nl}";

if (!$isCli) echo '</body></html>';