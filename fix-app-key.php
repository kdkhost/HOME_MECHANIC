<?php
/**
 * HomeMechanic - Correção Imediata da APP_KEY
 * Script para corrigir o erro "No application encryption key has been specified"
 */

echo "🔧 HomeMechanic - Correção da APP_KEY\n";
echo "====================================\n\n";

$envFile = __DIR__ . '/.env';

// Função para gerar APP_KEY
function generateAppKey() {
    return 'base64:' . base64_encode(random_bytes(32));
}

// Verificar se .env existe
if (!file_exists($envFile)) {
    echo "❌ Arquivo .env não encontrado!\n";
    echo "   Criando arquivo .env básico...\n\n";
    
    // Criar .env básico
    $envContent = <<<ENV
APP_NAME=HomeMechanic
APP_ENV=local
APP_KEY={APP_KEY}
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@homemechanic.com.br"
MAIL_FROM_NAME="\${APP_NAME}"

BCRYPT_ROUNDS=12
HASH_VERIFY=true

SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CSRF_COOKIE_NAME=XSRF-TOKEN
CSRF_HEADER_NAME=X-XSRF-TOKEN
ENV;

    $appKey = generateAppKey();
    $envContent = str_replace('{APP_KEY}', $appKey, $envContent);
    
    file_put_contents($envFile, $envContent);
    chmod($envFile, 0644);
    
    echo "✅ Arquivo .env criado com APP_KEY: {$appKey}\n\n";
    
} else {
    echo "📄 Arquivo .env encontrado. Verificando APP_KEY...\n";
    
    $envContent = file_get_contents($envFile);
    
    // Verificar se APP_KEY existe e não está vazia
    if (preg_match('/^APP_KEY=(.*)$/m', $envContent, $matches)) {
        $currentKey = trim($matches[1]);
        
        if (empty($currentKey) || $currentKey === 'base64:' || strlen($currentKey) < 10) {
            echo "⚠️  APP_KEY está vazia ou inválida: '{$currentKey}'\n";
            echo "   Gerando nova APP_KEY...\n";
            
            $newKey = generateAppKey();
            $newContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$newKey}", $envContent);
            
            file_put_contents($envFile, $newContent);
            echo "✅ Nova APP_KEY definida: {$newKey}\n\n";
            
        } else {
            echo "✅ APP_KEY já existe e parece válida: {$currentKey}\n\n";
        }
    } else {
        echo "⚠️  APP_KEY não encontrada no arquivo .env\n";
        echo "   Adicionando APP_KEY...\n";
        
        $newKey = generateAppKey();
        $envContent = "APP_KEY={$newKey}\n" . $envContent;
        
        file_put_contents($envFile, $envContent);
        echo "✅ APP_KEY adicionada: {$newKey}\n\n";
    }
}

// Limpar caches do Laravel
echo "🧹 Limpando caches do Laravel...\n";

$commands = [
    'php artisan config:clear' => 'Limpando cache de configuração',
    'php artisan cache:clear' => 'Limpando cache da aplicação',
    'php artisan route:clear' => 'Limpando cache de rotas',
    'php artisan view:clear' => 'Limpando cache de views'
];

foreach ($commands as $command => $description) {
    echo "   {$description}...\n";
    $output = [];
    $returnVar = 0;
    exec($command . ' 2>&1', $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "   ✅ Sucesso\n";
    } else {
        echo "   ⚠️  Aviso: " . implode(' ', $output) . "\n";
    }
}

echo "\n";

// Verificar se o problema foi resolvido
echo "🧪 Testando se o problema foi resolvido...\n";

// Tentar carregar o Laravel
try {
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
        
        if (file_exists(__DIR__ . '/bootstrap/app.php')) {
            $app = require_once __DIR__ . '/bootstrap/app.php';
            
            // Tentar acessar a configuração
            $config = $app->make('config');
            $appKey = $config->get('app.key');
            
            if (!empty($appKey)) {
                echo "✅ SUCESSO! APP_KEY carregada corretamente: " . substr($appKey, 0, 20) . "...\n";
                echo "✅ Laravel inicializado sem erros!\n\n";
            } else {
                echo "❌ APP_KEY ainda não está sendo carregada\n\n";
            }
        }
    }
} catch (Exception $e) {
    echo "⚠️  Ainda há problemas: " . $e->getMessage() . "\n\n";
}

// Instruções finais
echo "📋 Próximos passos:\n";
echo "1. Teste o sistema: http://seu-dominio.com/\n";
echo "2. Se ainda houver erro, teste: http://seu-dominio.com/debug-installer.php\n";
echo "3. Para instalar: http://seu-dominio.com/install\n\n";

echo "⚠️  Se o problema persistir:\n";
echo "- Verifique se o arquivo .env tem permissões corretas (644)\n";
echo "- Confirme que o PHP pode ler o arquivo .env\n";
echo "- Execute: chmod 644 .env\n";
echo "- Execute: php artisan config:cache\n\n";

echo "🎉 Correção da APP_KEY concluída!\n";
?>