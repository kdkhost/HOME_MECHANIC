<?php
// Script de correção automática para HomeMechanic System
// Execute este arquivo para tentar corrigir problemas comuns

echo "<h1>🔧 Correção Automática HomeMechanic System</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";

$fixes_applied = 0;
$errors = [];

// 1. Verificar e criar diretórios necessários
echo "<h2>1. Criando diretórios necessários</h2>";
$directories = [
    '../storage/logs',
    '../storage/framework/cache',
    '../storage/framework/sessions',
    '../storage/framework/views',
    '../storage/app/public',
    '../bootstrap/cache'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo '<span class="ok">✓</span> Criado: ' . $dir . '<br>';
            $fixes_applied++;
        } else {
            echo '<span class="error">✗</span> Falha ao criar: ' . $dir . '<br>';
            $errors[] = "Não foi possível criar diretório: $dir";
        }
    } else {
        echo '<span class="ok">✓</span> Já existe: ' . $dir . '<br>';
    }
}

// 2. Corrigir permissões
echo "<h2>2. Corrigindo permissões</h2>";
$permission_dirs = [
    '../storage' => 0777,
    '../bootstrap/cache' => 0777
];

foreach ($permission_dirs as $dir => $perm) {
    if (is_dir($dir)) {
        if (chmod($dir, $perm)) {
            echo '<span class="ok">✓</span> Permissões corrigidas: ' . $dir . '<br>';
            $fixes_applied++;
        } else {
            echo '<span class="warning">⚠</span> Não foi possível alterar permissões: ' . $dir . '<br>';
        }
        
        // Corrigir permissões recursivamente
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                chmod($item->getRealPath(), 0777);
            } else {
                chmod($item->getRealPath(), 0666);
            }
        }
    }
}

// 3. Criar arquivo .env básico se não existir
echo "<h2>3. Verificando arquivo .env</h2>";
if (!file_exists('../.env')) {
    $env_content = 'APP_NAME="HomeMechanic"
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
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=homemechanic_2026
DB_USERNAME=homemechanic_2026
DB_PASSWORD=homemechanic_2026

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@homemechanic.com.br"
MAIL_FROM_NAME="HomeMechanic"
';

    if (file_put_contents('../.env', $env_content)) {
        echo '<span class="ok">✓</span> Arquivo .env criado<br>';
        $fixes_applied++;
    } else {
        echo '<span class="error">✗</span> Falha ao criar .env<br>';
        $errors[] = "Não foi possível criar arquivo .env";
    }
} else {
    echo '<span class="ok">✓</span> Arquivo .env já existe<br>';
}

// 4. Verificar e corrigir index.php
echo "<h2>4. Verificando index.php</h2>";
if (!file_exists('index.php')) {
    $index_content = '<?php

use Illuminate\Http\Request;

define(\'LARAVEL_START\', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.\'/../storage/framework/maintenance.php\')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.\'/../vendor/autoload.php\';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.\'/../bootstrap/app.php\')
    ->handleRequest(Request::capture());
';

    if (file_put_contents('index.php', $index_content)) {
        echo '<span class="ok">✓</span> Arquivo index.php criado<br>';
        $fixes_applied++;
    } else {
        echo '<span class="error">✗</span> Falha ao criar index.php<br>';
        $errors[] = "Não foi possível criar index.php";
    }
} else {
    echo '<span class="ok">✓</span> Arquivo index.php já existe<br>';
}

// 5. Criar symlink para storage
echo "<h2>5. Criando symlink para storage</h2>";
if (!file_exists('storage')) {
    if (symlink('../storage/app/public', 'storage')) {
        echo '<span class="ok">✓</span> Symlink storage criado<br>';
        $fixes_applied++;
    } else {
        echo '<span class="warning">⚠</span> Não foi possível criar symlink (pode não ter permissão)<br>';
    }
} else {
    echo '<span class="ok">✓</span> Symlink storage já existe<br>';
}

// 6. Limpar caches se possível
echo "<h2>6. Limpando caches</h2>";
$cache_files = [
    '../bootstrap/cache/config.php',
    '../bootstrap/cache/routes-v7.php',
    '../bootstrap/cache/services.php'
];

foreach ($cache_files as $cache_file) {
    if (file_exists($cache_file)) {
        if (unlink($cache_file)) {
            echo '<span class="ok">✓</span> Cache removido: ' . basename($cache_file) . '<br>';
            $fixes_applied++;
        } else {
            echo '<span class="warning">⚠</span> Não foi possível remover cache: ' . basename($cache_file) . '<br>';
        }
    }
}

// 7. Verificar se composer install foi executado
echo "<h2>7. Verificando Composer</h2>";
if (!file_exists('../vendor/autoload.php')) {
    echo '<span class="error">✗</span> Composer não foi executado. Execute: composer install<br>';
    $errors[] = "Composer install não foi executado";
} else {
    echo '<span class="ok">✓</span> Composer parece estar OK<br>';
}

// 8. Criar arquivo de teste simples
echo "<h2>8. Criando arquivo de teste</h2>";
$test_content = '<?php
// Teste básico PHP
echo "PHP funcionando: " . PHP_VERSION . "<br>";
echo "Servidor: " . ($_SERVER["SERVER_SOFTWARE"] ?? "Desconhecido") . "<br>";
echo "Data/Hora: " . date("Y-m-d H:i:s") . "<br>";

if (file_exists("../vendor/autoload.php")) {
    echo "Autoloader: OK<br>";
} else {
    echo "Autoloader: ERRO<br>";
}

if (file_exists("../.env")) {
    echo ".env: OK<br>";
} else {
    echo ".env: ERRO<br>";
}
?>';

if (file_put_contents('test.php', $test_content)) {
    echo '<span class="ok">✓</span> Arquivo test.php criado<br>';
    echo 'Acesse <a href="test.php" target="_blank">test.php</a> para verificar se PHP está funcionando<br>';
    $fixes_applied++;
}

// Resumo
echo "<hr>";
echo "<h2>📊 Resumo da Correção</h2>";
echo "<p><strong>Correções aplicadas:</strong> $fixes_applied</p>";

if (count($errors) > 0) {
    echo "<p><strong>Erros encontrados:</strong></p>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li class='error'>$error</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='ok'>✓ Nenhum erro crítico encontrado!</p>";
}

echo "<h3>Próximos passos:</h3>";
echo "<ol>";
echo "<li>Acesse <a href='test.php'>test.php</a> para verificar se PHP está funcionando</li>";
echo "<li>Se test.php funcionar, tente acessar a página principal novamente</li>";
echo "<li>Se ainda houver erro 500, execute via SSH:</li>";
echo "<ul>";
echo "<li><code>composer install --no-dev --optimize-autoloader</code></li>";
echo "<li><code>php artisan key:generate</code></li>";
echo "<li><code>php artisan config:clear</code></li>";
echo "<li><code>php artisan cache:clear</code></li>";
echo "</ul>";
echo "</ol>";

echo "<p><em>Script executado em: " . date('Y-m-d H:i:s') . "</em></p>";
?>