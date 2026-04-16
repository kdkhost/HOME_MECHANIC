<?php
/**
 * HomeMechanic - Script de Limpeza de Cache
 * Execute este script para limpar todos os caches do Laravel
 */

echo "🔧 HomeMechanic - Limpeza de Cache\n";
echo "==================================\n\n";

// Definir o diretório base
$base_dir = __DIR__;

// Função para executar comandos
function runCommand($command, $description) {
    echo "📋 {$description}...\n";
    
    $output = [];
    $return_var = 0;
    
    exec($command . ' 2>&1', $output, $return_var);
    
    if ($return_var === 0) {
        echo "✅ Sucesso: {$description}\n";
        if (!empty($output)) {
            echo "   " . implode("\n   ", $output) . "\n";
        }
    } else {
        echo "❌ Erro: {$description}\n";
        if (!empty($output)) {
            echo "   " . implode("\n   ", $output) . "\n";
        }
    }
    echo "\n";
    
    return $return_var === 0;
}

// Função para limpar diretório
function clearDirectory($dir, $description) {
    echo "📂 {$description}...\n";
    
    if (!is_dir($dir)) {
        echo "⚠️  Diretório não encontrado: {$dir}\n\n";
        return false;
    }
    
    $files = glob($dir . '/*');
    $count = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            if (unlink($file)) {
                $count++;
            }
        } elseif (is_dir($file) && basename($file) !== '.' && basename($file) !== '..') {
            // Recursivamente limpar subdiretórios
            $subfiles = glob($file . '/*');
            foreach ($subfiles as $subfile) {
                if (is_file($subfile)) {
                    if (unlink($subfile)) {
                        $count++;
                    }
                }
            }
        }
    }
    
    echo "✅ Removidos {$count} arquivos de cache\n\n";
    return true;
}

// Verificar se estamos no diretório correto
if (!file_exists($base_dir . '/artisan')) {
    echo "❌ Erro: Este script deve ser executado na raiz do projeto Laravel\n";
    echo "   Arquivo 'artisan' não encontrado\n";
    exit(1);
}

// Mudar para o diretório do projeto
chdir($base_dir);

echo "📍 Diretório atual: " . getcwd() . "\n\n";

// 1. Limpar cache de configuração
runCommand('php artisan config:clear', 'Limpando cache de configuração');

// 2. Limpar cache de rotas
runCommand('php artisan route:clear', 'Limpando cache de rotas');

// 3. Limpar cache de views
runCommand('php artisan view:clear', 'Limpando cache de views');

// 4. Limpar cache de eventos
runCommand('php artisan event:clear', 'Limpando cache de eventos');

// 5. Limpar cache geral da aplicação
runCommand('php artisan cache:clear', 'Limpando cache da aplicação');

// 6. Limpar diretórios de cache manualmente
clearDirectory($base_dir . '/bootstrap/cache', 'Limpando bootstrap/cache');
clearDirectory($base_dir . '/storage/framework/cache/data', 'Limpando storage/framework/cache/data');
clearDirectory($base_dir . '/storage/framework/sessions', 'Limpando sessões');
clearDirectory($base_dir . '/storage/framework/views', 'Limpando views compiladas');
clearDirectory($base_dir . '/storage/logs', 'Limpando logs (mantendo .gitignore)');

// 7. Recriar caches otimizados
echo "🔄 Recriando caches otimizados...\n\n";

runCommand('php artisan config:cache', 'Criando cache de configuração');
runCommand('php artisan route:cache', 'Criando cache de rotas');
runCommand('php artisan view:cache', 'Criando cache de views');

// 8. Otimizar autoloader do Composer
runCommand('composer dump-autoload -o', 'Otimizando autoloader do Composer');

// 9. Verificar permissões
echo "🔐 Verificando permissões...\n";

$directories_to_check = [
    'storage',
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($directories_to_check as $dir) {
    $full_path = $base_dir . '/' . $dir;
    if (is_dir($full_path)) {
        if (is_writable($full_path)) {
            echo "✅ {$dir} - Gravável\n";
        } else {
            echo "⚠️  {$dir} - Sem permissão de escrita\n";
            // Tentar corrigir permissões (funciona apenas em alguns servidores)
            @chmod($full_path, 0755);
        }
    } else {
        echo "❌ {$dir} - Diretório não encontrado\n";
        // Tentar criar o diretório
        @mkdir($full_path, 0755, true);
    }
}

echo "\n";

// 10. Verificar status final
echo "📊 Status Final\n";
echo "===============\n\n";

// Verificar se o Laravel está funcionando
$output = [];
exec('php artisan --version 2>&1', $output, $return_var);

if ($return_var === 0) {
    echo "✅ Laravel funcionando: " . implode(' ', $output) . "\n";
} else {
    echo "❌ Problema com Laravel: " . implode(' ', $output) . "\n";
}

// Verificar tamanho dos caches
$cache_dirs = [
    'bootstrap/cache' => 'Bootstrap Cache',
    'storage/framework/cache' => 'Framework Cache',
    'storage/framework/views' => 'Views Cache'
];

foreach ($cache_dirs as $dir => $name) {
    $full_path = $base_dir . '/' . $dir;
    if (is_dir($full_path)) {
        $size = 0;
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($full_path));
        foreach ($files as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        echo "📁 {$name}: " . formatBytes($size) . "\n";
    }
}

echo "\n";

// Instruções finais
echo "🎉 Limpeza de cache concluída!\n";
echo "==============================\n\n";
echo "📋 Próximos passos:\n";
echo "1. Teste o sistema acessando: http://seu-dominio.com/test-fix.php\n";
echo "2. Se tudo estiver OK, acesse: http://seu-dominio.com/\n";
echo "3. Para instalar o sistema: http://seu-dominio.com/install\n";
echo "4. Painel admin: http://seu-dominio.com/admin/login\n\n";

echo "⚠️  Se ainda houver problemas:\n";
echo "- Verifique os logs em storage/logs/laravel.log\n";
echo "- Confirme que PHP 8.4+ está sendo usado\n";
echo "- Verifique permissões dos diretórios storage/ e bootstrap/cache/\n\n";

echo "🔧 HomeMechanic v1.0.0 - Sistema pronto para uso!\n";

// Função auxiliar para formatar bytes
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}
?>