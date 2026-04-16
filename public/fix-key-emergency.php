<?php
/**
 * HomeMechanic - Correção de Emergência da APP_KEY
 * Script que funciona mesmo quando o Laravel não consegue carregar
 */

// Função para gerar APP_KEY
function generateAppKey() {
    return 'base64:' . base64_encode(random_bytes(32));
}

// Detectar URL do sistema
function detectSystemUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $protocol . '://' . $host;
}

$envFile = __DIR__ . '/../.env';
$fixed = false;
$messages = [];

// Verificar e corrigir .env
if (!file_exists($envFile)) {
    // Criar .env completo
    $appKey = generateAppKey();
    $appUrl = detectSystemUrl();
    
    $envContent = <<<ENV
APP_NAME=HomeMechanic
APP_ENV=local
APP_KEY={$appKey}
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL={$appUrl}

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

    if (file_put_contents($envFile, $envContent)) {
        chmod($envFile, 0644);
        $messages[] = "✅ Arquivo .env criado com APP_KEY: {$appKey}";
        $fixed = true;
    } else {
        $messages[] = "❌ Erro ao criar arquivo .env";
    }
    
} else {
    // Verificar e corrigir APP_KEY existente
    $envContent = file_get_contents($envFile);
    
    if (preg_match('/^APP_KEY=(.*)$/m', $envContent, $matches)) {
        $currentKey = trim($matches[1]);
        
        if (empty($currentKey) || $currentKey === 'base64:' || strlen($currentKey) < 10) {
            $newKey = generateAppKey();
            $newContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$newKey}", $envContent);
            
            if (file_put_contents($envFile, $newContent)) {
                $messages[] = "✅ APP_KEY corrigida: {$newKey}";
                $fixed = true;
            } else {
                $messages[] = "❌ Erro ao atualizar APP_KEY";
            }
        } else {
            $messages[] = "✅ APP_KEY já existe: " . substr($currentKey, 0, 20) . "...";
        }
    } else {
        // Adicionar APP_KEY no início do arquivo
        $newKey = generateAppKey();
        $newContent = "APP_KEY={$newKey}\n" . $envContent;
        
        if (file_put_contents($envFile, $newContent)) {
            $messages[] = "✅ APP_KEY adicionada: {$newKey}";
            $fixed = true;
        } else {
            $messages[] = "❌ Erro ao adicionar APP_KEY";
        }
    }
}

// Limpar caches se possível
if ($fixed) {
    $cacheCommands = [
        'php artisan config:clear',
        'php artisan cache:clear',
        'php artisan route:clear',
        'php artisan view:clear'
    ];
    
    foreach ($cacheCommands as $command) {
        $output = [];
        $returnVar = 0;
        @exec($command . ' 2>/dev/null', $output, $returnVar);
        
        if ($returnVar === 0) {
            $messages[] = "✅ Cache limpo: " . explode(' ', $command)[2];
        }
    }
}

// HTML Response
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeMechanic - Correção de Emergência</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { color: #FF6B00; font-size: 2rem; font-weight: bold; }
        .status { padding: 15px; margin: 10px 0; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .info { background: #cce7ff; color: #004085; border-left: 4px solid #007bff; }
        .btn { display: inline-block; padding: 12px 24px; background: #FF6B00; color: white; text-decoration: none; border-radius: 6px; margin: 10px 5px; }
        .btn:hover { background: #E55A00; }
        .code { background: #f8f9fa; padding: 15px; border-radius: 6px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔧 HomeMechanic</div>
            <h2>Correção de Emergência - APP_KEY</h2>
            <p>Resolvendo erro: "No application encryption key has been specified"</p>
        </div>

        <h3>📋 Resultado da Correção</h3>
        <?php foreach ($messages as $message): ?>
            <div class="status <?= strpos($message, '✅') !== false ? 'success' : (strpos($message, '❌') !== false ? 'error' : 'info') ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>

        <?php if ($fixed): ?>
            <div class="status success">
                <strong>🎉 PROBLEMA RESOLVIDO!</strong><br>
                A APP_KEY foi corrigida e os caches foram limpos.
            </div>
        <?php endif; ?>

        <h3>🧪 Teste o Sistema</h3>
        <div class="status info">
            <strong>Próximos passos:</strong><br><br>
            <a href="/" class="btn">🏠 Testar Homepage</a>
            <a href="/install" class="btn">⚙️ Ir para Instalador</a>
            <a href="/debug-installer.php" class="btn">🔍 Debug Completo</a>
        </div>

        <h3>📄 Informações do Arquivo .env</h3>
        <div class="code">
<?php
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    $lines = explode("\n", $envContent);
    $displayLines = array_slice($lines, 0, 15); // Primeiras 15 linhas
    echo htmlspecialchars(implode("\n", $displayLines));
    if (count($lines) > 15) {
        echo "\n... (arquivo continua)";
    }
} else {
    echo "Arquivo .env não encontrado";
}
?>
        </div>

        <h3>⚠️ Se Ainda Houver Problemas</h3>
        <div class="status info">
            <strong>Execute no servidor via SSH:</strong><br><br>
            <code>cd /home/homemechanic/public_html</code><br>
            <code>php fix-app-key.php</code><br>
            <code>chmod 644 .env</code><br>
            <code>php artisan config:cache</code>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>HomeMechanic v1.0.0</strong> - Correção de Emergência</p>
            <p style="color: #666; font-size: 0.9rem;">Executado em <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>