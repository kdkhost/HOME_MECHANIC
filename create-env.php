<?php
/**
 * HomeMechanic - Criador de .env básico
 * Script para criar arquivo .env mínimo necessário para o instalador funcionar
 */

echo "🔧 HomeMechanic - Criador de .env\n";
echo "=================================\n\n";

$envFile = __DIR__ . '/.env';
$envExampleFile = __DIR__ . '/.env.example';

// Verificar se .env já existe
if (file_exists($envFile)) {
    echo "⚠️  Arquivo .env já existe!\n";
    echo "   Localização: {$envFile}\n";
    echo "   Tamanho: " . filesize($envFile) . " bytes\n";
    echo "   Modificado: " . date('d/m/Y H:i:s', filemtime($envFile)) . "\n\n";
    
    $response = readline("Deseja sobrescrever? (s/N): ");
    if (strtolower($response) !== 's') {
        echo "❌ Operação cancelada.\n";
        exit(0);
    }
}

// Gerar APP_KEY
function generateAppKey() {
    return 'base64:' . base64_encode(random_bytes(32));
}

// Detectar URL do sistema
function detectSystemUrl() {
    if (isset($_SERVER['HTTP_HOST'])) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'];
    }
    return 'http://localhost';
}

// Conteúdo básico do .env
$envContent = <<<ENV
APP_NAME=HomeMechanic
APP_ENV=local
APP_KEY={APP_KEY}
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL={APP_URL}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database (será configurado pelo instalador)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Cache
BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail (será configurado pelo instalador)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@homemechanic.com.br"
MAIL_FROM_NAME="\${APP_NAME}"

# Configurações HomeMechanic
HOMEMECHANIC_VERSION=1.0.0
HOMEMECHANIC_INSTALLED=false
HOMEMECHANIC_MAINTENANCE=false
HOMEMECHANIC_ANALYTICS_ENABLED=true

# Segurança
BCRYPT_ROUNDS=12
HASH_VERIFY=true

# Sessão
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# CSRF
CSRF_COOKIE_NAME=XSRF-TOKEN
CSRF_HEADER_NAME=X-XSRF-TOKEN

ENV;

// Substituir placeholders
$appKey = generateAppKey();
$appUrl = detectSystemUrl();

$envContent = str_replace('{APP_KEY}', $appKey, $envContent);
$envContent = str_replace('{APP_URL}', $appUrl, $envContent);

// Criar o arquivo
try {
    $result = file_put_contents($envFile, $envContent);
    
    if ($result !== false) {
        echo "✅ Arquivo .env criado com sucesso!\n";
        echo "   Localização: {$envFile}\n";
        echo "   Tamanho: {$result} bytes\n";
        echo "   APP_KEY: {$appKey}\n";
        echo "   APP_URL: {$appUrl}\n\n";
        
        // Definir permissões seguras
        chmod($envFile, 0644);
        echo "🔐 Permissões definidas: 644\n\n";
        
        echo "📋 Próximos passos:\n";
        echo "1. Execute: php clear-cache.php\n";
        echo "2. Acesse: /debug-installer.php (para verificar status)\n";
        echo "3. Acesse: /install (para iniciar instalação)\n\n";
        
        echo "⚠️  IMPORTANTE:\n";
        echo "- O banco de dados será configurado durante a instalação\n";
        echo "- As configurações de email serão definidas no painel admin\n";
        echo "- Mantenha o arquivo .env seguro (não compartilhe a APP_KEY)\n\n";
        
    } else {
        echo "❌ Erro ao criar arquivo .env\n";
        echo "   Verifique as permissões do diretório\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}

echo "🎉 Configuração inicial concluída!\n";
echo "   O sistema está pronto para instalação.\n";
?>