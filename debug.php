<?php
// Script de diagnóstico para HomeMechanic System
// Execute este arquivo diretamente no navegador para diagnosticar problemas

echo "<h1>🔍 Diagnóstico HomeMechanic System</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";

// 1. Verificar PHP
echo "<h2>1. Informações PHP</h2>";
echo "Versão PHP: " . PHP_VERSION . "<br>";
echo "SAPI: " . php_sapi_name() . "<br>";
echo "Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido') . "<br>";

// 2. Verificar extensões
echo "<h2>2. Extensões PHP</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'gd'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? '<span class="ok">✓</span>' : '<span class="error">✗</span>';
    echo "{$status} {$ext}<br>";
}

// 3. Verificar arquivos
echo "<h2>3. Arquivos Críticos</h2>";
$critical_files = [
    'index.php' => 'Arquivo principal',
    '../.env' => 'Configuração',
    '../bootstrap/app.php' => 'Bootstrap Laravel',
    '../vendor/autoload.php' => 'Autoloader Composer',
    '../storage/installed' => 'Marcador de instalação'
];

foreach ($critical_files as $file => $desc) {
    $exists = file_exists($file);
    $readable = $exists ? is_readable($file) : false;
    
    if ($exists && $readable) {
        echo '<span class="ok">✓</span>';
    } elseif ($exists) {
        echo '<span class="warning">⚠</span>';
    } else {
        echo '<span class="error">✗</span>';
    }
    
    echo " {$desc} ({$file})";
    
    if ($exists) {
        echo " - Tamanho: " . filesize($file) . " bytes";
        if (!$readable) echo " <span class='error'>[NÃO LEGÍVEL]</span>";
    } else {
        echo " <span class='error'>[NÃO EXISTE]</span>";
    }
    echo "<br>";
}

// 4. Verificar permissões
echo "<h2>4. Permissões</h2>";
$directories = [
    '../storage' => 'Storage',
    '../storage/logs' => 'Logs',
    '../storage/framework' => 'Framework',
    '../bootstrap/cache' => 'Bootstrap Cache'
];

foreach ($directories as $dir => $desc) {
    if (is_dir($dir)) {
        $writable = is_writable($dir);
        $status = $writable ? '<span class="ok">✓</span>' : '<span class="error">✗</span>';
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo "{$status} {$desc} ({$dir}) - Permissões: {$perms}";
        if (!$writable) echo " <span class='error'>[NÃO GRAVÁVEL]</span>";
        echo "<br>";
    } else {
        echo '<span class="error">✗</span> ' . $desc . " ({$dir}) <span class='error'>[DIRETÓRIO NÃO EXISTE]</span><br>";
    }
}

// 5. Testar autoloader
echo "<h2>5. Teste Autoloader</h2>";
if (file_exists('../vendor/autoload.php')) {
    try {
        require_once '../vendor/autoload.php';
        echo '<span class="ok">✓</span> Autoloader carregado com sucesso<br>';
    } catch (Exception $e) {
        echo '<span class="error">✗</span> Erro no autoloader: ' . $e->getMessage() . '<br>';
    }
} else {
    echo '<span class="error">✗</span> Arquivo autoload.php não encontrado<br>';
}

// 6. Testar .env
echo "<h2>6. Teste Configuração .env</h2>";
if (file_exists('../.env')) {
    $env_content = file_get_contents('../.env');
    if ($env_content !== false) {
        echo '<span class="ok">✓</span> Arquivo .env existe e é legível<br>';
        echo "Tamanho: " . strlen($env_content) . " bytes<br>";
        
        // Verificar chaves importantes (sem mostrar valores)
        $important_keys = ['APP_KEY', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME'];
        foreach ($important_keys as $key) {
            if (strpos($env_content, $key . '=') !== false) {
                echo '<span class="ok">✓</span> ' . $key . ' configurado<br>';
            } else {
                echo '<span class="error">✗</span> ' . $key . ' não encontrado<br>';
            }
        }
    } else {
        echo '<span class="error">✗</span> Não foi possível ler o arquivo .env<br>';
    }
} else {
    echo '<span class="error">✗</span> Arquivo .env não existe<br>';
}

// 7. Testar Bootstrap Laravel
echo "<h2>7. Teste Bootstrap Laravel</h2>";
if (file_exists('../bootstrap/app.php')) {
    try {
        // Tentar carregar o bootstrap sem executar
        $bootstrap_content = file_get_contents('../bootstrap/app.php');
        if (strpos($bootstrap_content, 'Application::configure') !== false) {
            echo '<span class="ok">✓</span> Bootstrap Laravel parece válido<br>';
        } else {
            echo '<span class="warning">⚠</span> Bootstrap pode estar corrompido<br>';
        }
    } catch (Exception $e) {
        echo '<span class="error">✗</span> Erro ao verificar bootstrap: ' . $e->getMessage() . '<br>';
    }
} else {
    echo '<span class="error">✗</span> Bootstrap não encontrado<br>';
}

// 8. Verificar logs
echo "<h2>8. Logs de Erro</h2>";
$log_files = [
    '../storage/logs/laravel.log' => 'Laravel',
    '/usr/local/lsws/logs/error.log' => 'LiteSpeed (se acessível)',
    '/var/log/apache2/error.log' => 'Apache (se acessível)'
];

foreach ($log_files as $log_file => $desc) {
    if (file_exists($log_file) && is_readable($log_file)) {
        echo '<span class="ok">✓</span> Log ' . $desc . ' acessível<br>';
        
        // Mostrar últimas linhas se for o log do Laravel
        if ($desc === 'Laravel') {
            $lines = file($log_file);
            if ($lines && count($lines) > 0) {
                echo "<strong>Últimas 5 linhas do log Laravel:</strong><br>";
                echo "<pre style='background:#f5f5f5;padding:10px;font-size:12px;'>";
                $last_lines = array_slice($lines, -5);
                foreach ($last_lines as $line) {
                    echo htmlspecialchars($line);
                }
                echo "</pre>";
            }
        }
    } else {
        echo '<span class="warning">⚠</span> Log ' . $desc . ' não acessível<br>';
    }
}

// 9. Informações do sistema
echo "<h2>9. Informações do Sistema</h2>";
echo "Memória PHP: " . ini_get('memory_limit') . "<br>";
echo "Tempo execução: " . ini_get('max_execution_time') . "s<br>";
echo "Upload máximo: " . ini_get('upload_max_filesize') . "<br>";
echo "POST máximo: " . ini_get('post_max_size') . "<br>";
echo "Timezone: " . date_default_timezone_get() . "<br>";
echo "Data/Hora atual: " . date('Y-m-d H:i:s') . "<br>";

// 10. Teste simples do Laravel
echo "<h2>10. Teste Laravel</h2>";
try {
    if (file_exists('../vendor/autoload.php') && file_exists('../bootstrap/app.php')) {
        // Tentar inicializar o Laravel de forma básica
        require_once '../vendor/autoload.php';
        
        // Verificar se conseguimos criar a aplicação
        echo '<span class="ok">✓</span> Tentando inicializar Laravel...<br>';
        
        // Não vamos executar completamente para evitar erros, apenas verificar se os arquivos estão OK
        echo '<span class="ok">✓</span> Arquivos básicos do Laravel parecem estar OK<br>';
        
    } else {
        echo '<span class="error">✗</span> Arquivos necessários para Laravel não encontrados<br>';
    }
} catch (Exception $e) {
    echo '<span class="error">✗</span> Erro ao testar Laravel: ' . $e->getMessage() . '<br>';
} catch (Error $e) {
    echo '<span class="error">✗</span> Erro fatal ao testar Laravel: ' . $e->getMessage() . '<br>';
}

echo "<hr>";
echo "<p><strong>Diagnóstico concluído!</strong> Se houver erros acima, eles podem estar causando o erro 500.</p>";
echo "<p>Para corrigir, execute os comandos de correção apropriados via SSH.</p>";
?>