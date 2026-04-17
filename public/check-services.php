<?php
/**
 * Script para verificar serviços necessários
 * Acesse: /check-services.php
 */

echo "🔍 Verificando serviços necessários...\n\n";

// Verificar se o MySQL está rodando
function checkMysqlService() {
    echo "📊 Verificando MySQL...\n";
    
    // Tentar conectar
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        echo "✅ MySQL está rodando!\n";
        
        // Verificar versão
        $stmt = $pdo->query('SELECT VERSION() as version');
        $version = $stmt->fetch(PDO::FETCH_ASSOC)['version'];
        echo "   Versão: {$version}\n";
        
        return true;
        
    } catch (PDOException $e) {
        echo "❌ MySQL não está rodando ou não acessível!\n";
        echo "   Erro: " . $e->getMessage() . "\n";
        
        echo "\n💡 Como iniciar o MySQL:\n";
        echo "   Windows (XAMPP): Abra o XAMPP Control Panel e inicie MySQL\n";
        echo "   Windows (WAMP): Abra o WAMP e inicie MySQL\n";
        echo "   Windows (Laragon): Abra o Laragon e inicie MySQL\n";
        echo "   Windows (Serviço): net start mysql\n";
        echo "   Linux: sudo systemctl start mysql\n";
        echo "   macOS: brew services start mysql\n\n";
        
        return false;
    }
}

// Verificar PHP
function checkPhpVersion() {
    echo "🐘 Verificando PHP...\n";
    $version = PHP_VERSION;
    echo "✅ PHP {$version} está rodando!\n";
    
    // Verificar extensões necessárias
    $required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json'];
    $missing = [];
    
    foreach ($required as $ext) {
        if (!extension_loaded($ext)) {
            $missing[] = $ext;
        }
    }
    
    if (empty($missing)) {
        echo "✅ Todas as extensões PHP necessárias estão instaladas!\n";
    } else {
        echo "❌ Extensões PHP faltando: " . implode(', ', $missing) . "\n";
    }
    
    return empty($missing);
}

// Verificar Composer
function checkComposer() {
    echo "📦 Verificando Composer...\n";
    
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        echo "✅ Dependências do Composer instaladas!\n";
        return true;
    } else {
        echo "❌ Dependências do Composer não instaladas!\n";
        echo "💡 Execute: composer install\n";
        return false;
    }
}

// Verificar permissões de arquivos
function checkPermissions() {
    echo "🔐 Verificando permissões...\n";
    
    $paths = [
        __DIR__ . '/../storage',
        __DIR__ . '/../bootstrap/cache',
        __DIR__ . '/../.env'
    ];
    
    $issues = [];
    
    foreach ($paths as $path) {
        if (!is_writable($path)) {
            $issues[] = $path;
        }
    }
    
    if (empty($issues)) {
        echo "✅ Permissões estão corretas!\n";
        return true;
    } else {
        echo "❌ Problemas de permissão em:\n";
        foreach ($issues as $path) {
            echo "   - {$path}\n";
        }
        return false;
    }
}

// Executar verificações
$mysqlOk = checkMysqlService();
echo "\n";

$phpOk = checkPhpVersion();
echo "\n";

$composerOk = checkComposer();
echo "\n";

$permissionsOk = checkPermissions();
echo "\n";

// Resumo
echo str_repeat("=", 50) . "\n";
echo "📋 RESUMO:\n";
echo "MySQL: " . ($mysqlOk ? "✅ OK" : "❌ PROBLEMA") . "\n";
echo "PHP: " . ($phpOk ? "✅ OK" : "❌ PROBLEMA") . "\n";
echo "Composer: " . ($composerOk ? "✅ OK" : "❌ PROBLEMA") . "\n";
echo "Permissões: " . ($permissionsOk ? "✅ OK" : "❌ PROBLEMA") . "\n";

if ($mysqlOk && $phpOk && $composerOk && $permissionsOk) {
    echo "\n🎉 Todos os serviços estão funcionando!\n";
    echo "💡 Você pode acessar: /admin para fazer login\n";
} else {
    echo "\n⚠️  Corrija os problemas acima antes de continuar.\n";
}

echo "\nVerificação concluída em " . date('d/m/Y H:i:s') . "\n";
?>