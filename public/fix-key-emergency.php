<?php
/**
 * HomeMechanic - Correção de Emergência da APP_KEY
 * Script para corrigir problemas com APP_KEY e configuração básica
 */

// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para gerar APP_KEY
function generateAppKey() {
    return 'base64:' . base64_encode(random_bytes(32));
}

// Função para verificar e corrigir .env
function fixEnvFile() {
    $envPath = __DIR__ . '/../.env';
    $envExamplePath = __DIR__ . '/../.env.example';
    
    $messages = [];
    
    // Verificar se .env existe
    if (!file_exists($envPath)) {
        if (file_exists($envExamplePath)) {
            copy($envExamplePath, $envPath);
            $messages[] = ['success', 'Arquivo .env criado a partir do .env.example'];
        } else {
            // Criar .env básico
            $basicEnv = "APP_NAME=HomeMechanic
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=America/Sao_Paulo
APP_URL=https://homemechanic.com.br

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homemechanic_2026
DB_USERNAME=homemechanic
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database
";
            file_put_contents($envPath, $basicEnv);
            $messages[] = ['success', 'Arquivo .env básico criado'];
        }
    }
    
    // Ler conteúdo atual do .env
    $envContent = file_get_contents($envPath);
    
    // Verificar e corrigir APP_KEY
    if (strpos($envContent, 'APP_KEY=') === false || preg_match('/APP_KEY=\s*$/', $envContent)) {
        $newKey = generateAppKey();
        
        if (strpos($envContent, 'APP_KEY=') !== false) {
            $envContent = preg_replace('/APP_KEY=.*/', "APP_KEY={$newKey}", $envContent);
            $messages[] = ['success', 'APP_KEY atualizada no .env'];
        } else {
            $envContent = str_replace('APP_NAME=', "APP_KEY={$newKey}\nAPP_NAME=", $envContent);
            $messages[] = ['success', 'APP_KEY adicionada ao .env'];
        }
        
        file_put_contents($envPath, $envContent);
    } else {
        $messages[] = ['info', 'APP_KEY já está configurada'];
    }
    
    return $messages;
}

// Função para limpar caches
function clearCaches() {
    $messages = [];
    
    try {
        // Limpar cache de configuração
        $configCachePath = __DIR__ . '/../bootstrap/cache/config.php';
        if (file_exists($configCachePath)) {
            unlink($configCachePath);
            $messages[] = ['success', 'Cache de configuração limpo'];
        }
        
        // Limpar cache de rotas
        $routeCachePath = __DIR__ . '/../bootstrap/cache/routes-v7.php';
        if (file_exists($routeCachePath)) {
            unlink($routeCachePath);
            $messages[] = ['success', 'Cache de rotas limpo'];
        }
        
        // Limpar cache de views
        $viewsPath = __DIR__ . '/../storage/framework/views';
        if (is_dir($viewsPath)) {
            $files = glob($viewsPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            $messages[] = ['success', 'Cache de views limpo'];
        }
        
        // Limpar cache de dados
        $cachePath = __DIR__ . '/../storage/framework/cache/data';
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            $messages[] = ['success', 'Cache de dados limpo'];
        }
        
    } catch (Exception $e) {
        $messages[] = ['error', 'Erro ao limpar caches: ' . $e->getMessage()];
    }
    
    return $messages;
}

// Função para verificar permissões
function checkPermissions() {
    $directories = [
        'storage' => __DIR__ . '/../storage',
        'bootstrap/cache' => __DIR__ . '/../bootstrap/cache',
        'storage/app' => __DIR__ . '/../storage/app',
        'storage/framework' => __DIR__ . '/../storage/framework',
        'storage/logs' => __DIR__ . '/../storage/logs'
    ];
    
    $messages = [];
    
    foreach ($directories as $name => $path) {
        if (is_dir($path)) {
            if (is_writable($path)) {
                $messages[] = ['success', "Diretório {$name} tem permissão de escrita"];
            } else {
                $messages[] = ['error', "Diretório {$name} NÃO tem permissão de escrita"];
                
                // Tentar corrigir permissão
                if (chmod($path, 0755)) {
                    $messages[] = ['success', "Permissão do diretório {$name} corrigida"];
                } else {
                    $messages[] = ['error', "Falha ao corrigir permissão do diretório {$name}"];
                }
            }
        } else {
            $messages[] = ['warning', "Diretório {$name} não existe"];
            
            // Tentar criar diretório
            if (mkdir($path, 0755, true)) {
                $messages[] = ['success', "Diretório {$name} criado"];
            } else {
                $messages[] = ['error', "Falha ao criar diretório {$name}"];
            }
        }
    }
    
    return $messages;
}

// Função para testar Laravel
function testLaravel() {
    $messages = [];
    
    try {
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
            $messages[] = ['success', 'Autoloader carregado'];
            
            if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
                $app = require_once __DIR__ . '/../bootstrap/app.php';
                $messages[] = ['success', 'Laravel app carregado'];
                
                // Testar se o .env existe e tem APP_KEY
                $envPath = __DIR__ . '/../.env';
                if (file_exists($envPath)) {
                    $envContent = file_get_contents($envPath);
                    
                    // Verificar APP_KEY no arquivo .env diretamente
                    if (preg_match('/APP_KEY=(.+)/', $envContent, $matches)) {
                        $appKey = trim($matches[1]);
                        if (!empty($appKey) && $appKey !== '') {
                            $messages[] = ['success', 'APP_KEY encontrada no .env: ' . substr($appKey, 0, 20) . '...'];
                        } else {
                            $messages[] = ['error', 'APP_KEY vazia no .env'];
                        }
                    } else {
                        $messages[] = ['error', 'APP_KEY não encontrada no .env'];
                    }
                    
                    // Verificar APP_NAME
                    if (preg_match('/APP_NAME=(.+)/', $envContent, $matches)) {
                        $appName = trim($matches[1], '"\'');
                        $messages[] = ['info', 'APP_NAME: ' . $appName];
                    }
                    
                    // Verificar configuração do banco
                    if (preg_match('/DB_DATABASE=(.+)/', $envContent, $matches)) {
                        $dbName = trim($matches[1], '"\'');
                        $messages[] = ['info', 'Banco configurado: ' . $dbName];
                    }
                    
                } else {
                    $messages[] = ['error', 'Arquivo .env não encontrado'];
                }
                
                // Testar se consegue inicializar o Laravel (sem usar container)
                try {
                    // Verificar se as classes básicas do Laravel estão disponíveis
                    if (class_exists('Illuminate\Foundation\Application')) {
                        $messages[] = ['success', 'Classes do Laravel disponíveis'];
                    } else {
                        $messages[] = ['error', 'Classes do Laravel não encontradas'];
                    }
                    
                    // Verificar se o app foi criado
                    if (is_object($app)) {
                        $messages[] = ['success', 'Instância do Laravel criada'];
                    } else {
                        $messages[] = ['error', 'Falha ao criar instância do Laravel'];
                    }
                    
                } catch (Exception $e) {
                    $messages[] = ['error', 'Erro ao inicializar Laravel: ' . $e->getMessage()];
                }
                
            } else {
                $messages[] = ['error', 'bootstrap/app.php não encontrado'];
            }
        } else {
            $messages[] = ['error', 'vendor/autoload.php não encontrado'];
        }
    } catch (Exception $e) {
        $messages[] = ['error', 'Erro ao testar Laravel: ' . $e->getMessage()];
    }
    
    return $messages;
}

// Processar ações
$allMessages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fix_env'])) {
        $allMessages = array_merge($allMessages, fixEnvFile());
    }
    
    if (isset($_POST['clear_cache'])) {
        $allMessages = array_merge($allMessages, clearCaches());
    }
    
    if (isset($_POST['check_permissions'])) {
        $allMessages = array_merge($allMessages, checkPermissions());
    }
    
    if (isset($_POST['test_laravel'])) {
        $allMessages = array_merge($allMessages, testLaravel());
    }
    
    if (isset($_POST['fix_all'])) {
        $allMessages = array_merge($allMessages, fixEnvFile());
        $allMessages = array_merge($allMessages, clearCaches());
        $allMessages = array_merge($allMessages, checkPermissions());
        $allMessages = array_merge($allMessages, testLaravel());
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeMechanic - Correção de Emergência</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 40px; 
            background: #f4f6f9; 
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .logo { 
            color: #FF6B00; 
            font-size: 2rem; 
            font-weight: bold; 
        }
        .message { 
            padding: 10px 15px; 
            margin: 8px 0; 
            border-radius: 6px; 
            border-left: 4px solid;
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            border-color: #28a745; 
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            border-color: #dc3545; 
        }
        .warning { 
            background: #fff3cd; 
            color: #856404; 
            border-color: #ffc107; 
        }
        .info { 
            background: #cce7ff; 
            color: #004085; 
            border-color: #007bff; 
        }
        .btn { 
            display: inline-block; 
            padding: 10px 20px; 
            background: #FF6B00; 
            color: white; 
            text-decoration: none; 
            border: none;
            border-radius: 6px; 
            margin: 5px; 
            cursor: pointer;
        }
        .btn:hover { 
            background: #E55A00; 
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .action-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .action-card h4 {
            margin-top: 0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔧 HomeMechanic</div>
            <h2>Correção de Emergência</h2>
            <p>Ferramentas para corrigir problemas comuns de configuração</p>
        </div>

        <?php if (!empty($allMessages)): ?>
            <div style="margin-bottom: 20px;">
                <h3>Resultados:</h3>
                <?php foreach ($allMessages as $message): ?>
                    <div class="message <?= $message[0] ?>">
                        <?= htmlspecialchars($message[1]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="actions">
            <div class="action-card">
                <h4>🔑 Corrigir .env</h4>
                <p>Criar/corrigir arquivo .env e gerar nova APP_KEY</p>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="fix_env" class="btn">Corrigir .env</button>
                </form>
            </div>

            <div class="action-card">
                <h4>🧹 Limpar Cache</h4>
                <p>Limpar todos os caches do Laravel</p>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="clear_cache" class="btn">Limpar Cache</button>
                </form>
            </div>

            <div class="action-card">
                <h4>🔐 Verificar Permissões</h4>
                <p>Verificar e corrigir permissões de diretórios</p>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="check_permissions" class="btn">Verificar</button>
                </form>
            </div>

            <div class="action-card">
                <h4>🧪 Testar Laravel</h4>
                <p>Testar se o Laravel está carregando corretamente</p>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="test_laravel" class="btn">Testar</button>
                </form>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <form method="POST" style="display: inline;">
                <button type="submit" name="fix_all" class="btn" style="font-size: 1.1rem; padding: 15px 30px;">
                    🚀 Corrigir Tudo Automaticamente
                </button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="/install" class="btn btn-secondary">🔧 Voltar ao Instalador</a>
            <a href="/debug-installation.php" class="btn btn-secondary">🐛 Debug Detalhado</a>
            <a href="/test-installation.php" class="btn btn-secondary">🧪 Teste Completo</a>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>HomeMechanic v1.0.0</strong> - Correção de Emergência</p>
            <p style="color: #666; font-size: 0.9rem;">Executado em <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>