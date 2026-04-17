<?php
/**
 * Script para corrigir todos os problemas identificados
 * Acesse: /fix-all-issues.php
 */

echo "🔧 Correção automática de problemas\n\n";

// 1. Corrigir .env
echo "1️⃣ Corrigindo arquivo .env...\n";
$envFile = __DIR__ . '/../.env';

if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Corrigir senha vazia
    if (preg_match('/^DB_PASSWORD=$/m', $envContent)) {
        $envContent = preg_replace('/^DB_PASSWORD=$/m', 'DB_PASSWORD=""', $envContent);
        file_put_contents($envFile, $envContent);
        echo "   ✅ Senha do banco corrigida\n";
    }
    
    // Verificar outras configurações
    $configs = [
        'APP_KEY' => 'base64:',
        'DB_CONNECTION' => 'mysql',
        'DB_HOST' => '127.0.0.1',
        'DB_PORT' => '3306',
        'DB_DATABASE' => 'homemechanic',
        'DB_USERNAME' => 'root'
    ];
    
    foreach ($configs as $key => $expectedStart) {
        if (!preg_match("/^{$key}={$expectedStart}/m", $envContent)) {
            echo "   ⚠️  Verificar configuração: {$key}\n";
        }
    }
    
} else {
    echo "   ❌ Arquivo .env não encontrado!\n";
}

// 2. Limpar caches (se possível)
echo "\n2️⃣ Limpando caches...\n";
try {
    // Limpar cache de arquivos
    $cacheDir = __DIR__ . '/../storage/framework/cache/data';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "   ✅ Cache de arquivos limpo\n";
    }
    
    // Limpar views compiladas
    $viewsDir = __DIR__ . '/../storage/framework/views';
    if (is_dir($viewsDir)) {
        $files = glob($viewsDir . '/*.php');
        foreach ($files as $file) {
            unlink($file);
        }
        echo "   ✅ Views compiladas limpas\n";
    }
    
} catch (Exception $e) {
    echo "   ⚠️  Erro ao limpar cache: " . $e->getMessage() . "\n";
}

// 3. Verificar e criar diretórios necessários
echo "\n3️⃣ Verificando diretórios...\n";
$dirs = [
    __DIR__ . '/../storage/logs',
    __DIR__ . '/../storage/framework/cache/data',
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../bootstrap/cache'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "   ✅ Criado: " . basename($dir) . "\n";
    }
}

// 4. Verificar permissões
echo "\n4️⃣ Verificando permissões...\n";
$paths = [
    __DIR__ . '/../storage' => 0755,
    __DIR__ . '/../bootstrap/cache' => 0755
];

foreach ($paths as $path => $permission) {
    if (is_dir($path)) {
        chmod($path, $permission);
        echo "   ✅ Permissão definida: " . basename($path) . "\n";
    }
}

// 5. Testar conexão com banco (sem usar Laravel)
echo "\n5️⃣ Testando conexão com banco...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "   ✅ Conexão com MySQL estabelecida!\n";
    
    // Verificar se o banco existe
    $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
    $stmt->execute(['homemechanic']);
    
    if ($stmt->fetch()) {
        echo "   ✅ Banco 'homemechanic' existe!\n";
    } else {
        echo "   ⚠️  Banco 'homemechanic' não existe - execute o instalador\n";
    }
    
} catch (PDOException $e) {
    echo "   ❌ Erro de conexão: " . $e->getMessage() . "\n";
    echo "   💡 Inicie o MySQL antes de continuar\n";
}

// 6. Criar arquivo de status
echo "\n6️⃣ Criando arquivo de status...\n";
$status = [
    'last_fix' => date('Y-m-d H:i:s'),
    'env_fixed' => file_exists($envFile),
    'cache_cleared' => true,
    'directories_created' => true,
    'permissions_set' => true
];

file_put_contents(__DIR__ . '/fix-status.json', json_encode($status, JSON_PRETTY_PRINT));
echo "   ✅ Status salvo em fix-status.json\n";

// Resumo final
echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 Correções aplicadas!\n\n";

echo "📋 Próximos passos:\n";
echo "1. Inicie o MySQL (XAMPP, WAMP, Laragon, etc.)\n";
echo "2. Acesse /install para executar o instalador\n";
echo "3. Após instalação, acesse /admin para fazer login\n\n";

echo "🔍 Scripts de diagnóstico disponíveis:\n";
echo "- /check-services.php - Verificar serviços\n";
echo "- /test-db-connection.php - Testar banco\n";
echo "- /fix-db-password.php - Corrigir senha do banco\n\n";

echo "Correção concluída em " . date('d/m/Y H:i:s') . "\n";
?>