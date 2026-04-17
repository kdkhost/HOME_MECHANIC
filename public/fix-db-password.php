<?php
/**
 * Script para corrigir senha do banco de dados
 * Acesse: /fix-db-password.php
 */

echo "🔧 Correção da senha do banco de dados\n\n";

$envFile = __DIR__ . '/../.env';

if (!file_exists($envFile)) {
    echo "❌ Arquivo .env não encontrado!\n";
    exit(1);
}

// Ler arquivo .env
$envContent = file_get_contents($envFile);

// Verificar se a senha está vazia
if (preg_match('/^DB_PASSWORD=$/m', $envContent)) {
    echo "⚠️  Senha do banco está vazia!\n";
    echo "💡 Opções de correção:\n\n";
    
    echo "1. Definir senha vazia explicitamente (para desenvolvimento local):\n";
    echo "   DB_PASSWORD=\"\"\n\n";
    
    echo "2. Definir uma senha (recomendado para produção):\n";
    echo "   DB_PASSWORD=\"sua_senha_aqui\"\n\n";
    
    // Aplicar correção automática para desenvolvimento
    $newContent = preg_replace('/^DB_PASSWORD=$/m', 'DB_PASSWORD=""', $envContent);
    
    if (file_put_contents($envFile, $newContent)) {
        echo "✅ Correção aplicada! Senha definida como vazia explicitamente.\n";
        echo "🔄 Limpe o cache de configuração:\n";
        echo "   php artisan config:clear\n";
        echo "   php artisan cache:clear\n\n";
        
        // Tentar limpar cache automaticamente
        try {
            if (function_exists('exec')) {
                exec('cd ' . dirname($envFile) . ' && php artisan config:clear 2>&1', $output1);
                exec('cd ' . dirname($envFile) . ' && php artisan cache:clear 2>&1', $output2);
                echo "✅ Cache limpo automaticamente!\n";
            }
        } catch (Exception $e) {
            echo "⚠️  Limpe o cache manualmente.\n";
        }
        
    } else {
        echo "❌ Erro ao salvar arquivo .env\n";
    }
    
} else {
    echo "✅ Senha do banco já está configurada corretamente.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Correção concluída em " . date('d/m/Y H:i:s') . "\n";
?>