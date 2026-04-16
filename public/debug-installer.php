<?php
/**
 * HomeMechanic - Debug do Instalador
 * Script para diagnosticar problemas específicos do instalador
 */

echo "<!DOCTYPE html>\n";
echo "<html lang='pt-BR'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>HomeMechanic - Debug Instalador</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; }\n";
echo "        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }\n";
echo "        .header { text-align: center; margin-bottom: 30px; }\n";
echo "        .logo { color: #FF6B00; font-size: 2rem; font-weight: bold; }\n";
echo "        .status { padding: 15px; margin: 10px 0; border-radius: 8px; }\n";
echo "        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }\n";
echo "        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }\n";
echo "        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }\n";
echo "        .info { background: #cce7ff; color: #004085; border-left: 4px solid #007bff; }\n";
echo "        .code { background: #f8f9fa; padding: 15px; border-radius: 6px; font-family: monospace; margin: 10px 0; white-space: pre-wrap; }\n";
echo "        .btn { display: inline-block; padding: 12px 24px; background: #FF6B00; color: white; text-decoration: none; border-radius: 6px; margin: 10px 5px; }\n";
echo "        .btn:hover { background: #E55A00; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container'>\n";
echo "        <div class='header'>\n";
echo "            <div class='logo'>🔧 HomeMechanic</div>\n";
echo "            <h2>Debug do Instalador</h2>\n";
echo "            <p>Diagnosticando problemas específicos do instalador</p>\n";
echo "        </div>\n";

// Verificar se está instalado
$installedFile = __DIR__ . '/../storage/installed';
$isInstalled = file_exists($installedFile);

echo "        <h3>📦 Status da Instalação</h3>\n";
if ($isInstalled) {
    echo "        <div class='status warning'>\n";
    echo "            <strong>⚠️ Sistema já instalado</strong><br>\n";
    echo "            Arquivo encontrado: storage/installed<br>\n";
    echo "            Data: " . date('d/m/Y H:i:s', filemtime($installedFile)) . "\n";
    echo "        </div>\n";
} else {
    echo "        <div class='status info'>\n";
    echo "            <strong>📦 Sistema não instalado</strong><br>\n";
    echo "            Arquivo storage/installed não encontrado - OK para instalação\n";
    echo "        </div>\n";
}

// Verificar arquivos do instalador
echo "        <h3>📁 Arquivos do Instalador</h3>\n";
$installerFiles = [
    'app/Modules/Installer/Controllers/InstallerController.php' => 'Controller do Instalador',
    'app/Modules/Installer/Routes/web.php' => 'Rotas do Instalador',
    'app/Modules/Installer/Services/InstallerService.php' => 'Serviço do Instalador',
    'resources/views/modules/installer/requirements.blade.php' => 'View de Requisitos',
    'resources/views/modules/installer/form.blade.php' => 'View do Formulário',
    'app/Providers/ModuleServiceProvider.php' => 'Provider dos Módulos'
];

foreach ($installerFiles as $file => $description) {
    $fullPath = __DIR__ . '/../' . $file;
    if (file_exists($fullPath)) {
        echo "        <div class='status success'>\n";
        echo "            <strong>✅ {$file}</strong><br>\n";
        echo "            {$description} - OK (" . number_format(filesize($fullPath)) . " bytes)\n";
        echo "        </div>\n";
    } else {
        echo "        <div class='status error'>\n";
        echo "            <strong>❌ {$file}</strong><br>\n";
        echo "            {$description} - Arquivo não encontrado\n";
        echo "        </div>\n";
    }
}

// Testar carregamento do Laravel
echo "        <h3>🚀 Teste do Laravel</h3>\n";
try {
    // Tentar carregar o autoloader
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        echo "        <div class='status success'>\n";
        echo "            <strong>✅ Autoloader</strong> - Carregado com sucesso\n";
        echo "        </div>\n";
        
        // Tentar carregar o Laravel
        $appPath = __DIR__ . '/../bootstrap/app.php';
        if (file_exists($appPath)) {
            try {
                $app = require_once $appPath;
                echo "        <div class='status success'>\n";
                echo "            <strong>✅ Laravel App</strong> - Carregado com sucesso\n";
                echo "        </div>\n";
                
                // Tentar obter versão do Laravel
                if (class_exists('Illuminate\Foundation\Application')) {
                    $version = $app->version();
                    echo "        <div class='status info'>\n";
                    echo "            <strong>📋 Versão Laravel:</strong> {$version}\n";
                    echo "        </div>\n";
                }
                
            } catch (Exception $e) {
                echo "        <div class='status error'>\n";
                echo "            <strong>❌ Laravel App</strong><br>\n";
                echo "            Erro: " . $e->getMessage() . "\n";
                echo "        </div>\n";
            }
        } else {
            echo "        <div class='status error'>\n";
            echo "            <strong>❌ bootstrap/app.php</strong> - Arquivo não encontrado\n";
            echo "        </div>\n";
        }
    } else {
        echo "        <div class='status error'>\n";
        echo "            <strong>❌ Autoloader</strong> - vendor/autoload.php não encontrado\n";
        echo "        </div>\n";
    }
} catch (Exception $e) {
    echo "        <div class='status error'>\n";
    echo "            <strong>❌ Erro geral</strong><br>\n";
    echo "            " . $e->getMessage() . "\n";
    echo "        </div>\n";
}

// Verificar .env
echo "        <h3>⚙️ Configuração</h3>\n";
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo "        <div class='status success'>\n";
    echo "            <strong>✅ Arquivo .env</strong> - Encontrado\n";
    echo "        </div>\n";
    
    // Ler algumas configurações importantes
    $envContent = file_get_contents($envFile);
    $appDebug = preg_match('/APP_DEBUG=(.+)/', $envContent, $matches) ? trim($matches[1]) : 'não definido';
    $appEnv = preg_match('/APP_ENV=(.+)/', $envContent, $matches) ? trim($matches[1]) : 'não definido';
    
    echo "        <div class='code'>\n";
    echo "APP_ENV={$appEnv}\n";
    echo "APP_DEBUG={$appDebug}\n";
    echo "        </div>\n";
} else {
    echo "        <div class='status warning'>\n";
    echo "            <strong>⚠️ Arquivo .env</strong> - Não encontrado (normal para primeira instalação)\n";
    echo "        </div>\n";
}

// Verificar logs de erro
echo "        <h3>📋 Logs de Erro</h3>\n";
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $logLines = explode("\n", $logContent);
    $recentLines = array_slice($logLines, -20); // Últimas 20 linhas
    
    echo "        <div class='status info'>\n";
    echo "            <strong>📄 Últimas linhas do log:</strong>\n";
    echo "        </div>\n";
    echo "        <div class='code'>\n";
    echo htmlspecialchars(implode("\n", $recentLines));
    echo "        </div>\n";
} else {
    echo "        <div class='status info'>\n";
    echo "            <strong>📄 Log Laravel</strong> - Nenhum log encontrado ainda\n";
    echo "        </div>\n";
}

// Teste de rota direta
echo "        <h3>🔗 Teste de Rotas</h3>\n";
echo "        <div class='status info'>\n";
echo "            <strong>🧪 Testes manuais:</strong><br>\n";
echo "            <a href='/install' class='btn'>Testar /install</a>\n";
echo "            <a href='/install/configuracao' class='btn'>Testar /install/configuracao</a>\n";
echo "            <a href='/' class='btn'>Testar Homepage</a>\n";
echo "        </div>\n";

// Verificar permissões críticas
echo "        <h3>🔐 Permissões Críticas</h3>\n";
$criticalDirs = [
    'storage' => 'Diretório de armazenamento',
    'storage/logs' => 'Logs do sistema',
    'storage/framework' => 'Cache do framework',
    'bootstrap/cache' => 'Cache do bootstrap'
];

foreach ($criticalDirs as $dir => $description) {
    $fullPath = __DIR__ . '/../' . $dir;
    if (is_dir($fullPath)) {
        if (is_writable($fullPath)) {
            echo "        <div class='status success'>\n";
            echo "            <strong>✅ {$dir}</strong> - {$description} (Gravável)\n";
            echo "        </div>\n";
        } else {
            echo "        <div class='status error'>\n";
            echo "            <strong>❌ {$dir}</strong> - {$description} (Sem permissão de escrita)\n";
            echo "        </div>\n";
        }
    } else {
        echo "        <div class='status warning'>\n";
        echo "            <strong>⚠️ {$dir}</strong> - {$description} (Diretório não existe)\n";
        echo "        </div>\n";
    }
}

// Instruções de correção
echo "        <h3>🔧 Instruções de Correção</h3>\n";
echo "        <div class='status info'>\n";
echo "            <strong>Se o instalador não funcionar:</strong><br><br>\n";
echo "            <strong>1.</strong> Execute: <code>php clear-cache.php</code><br>\n";
echo "            <strong>2.</strong> Verifique permissões: <code>chmod -R 755 storage bootstrap/cache</code><br>\n";
echo "            <strong>3.</strong> Se .env não existe, crie um básico:<br>\n";
echo "        </div>\n";

echo "        <div class='code'>\n";
echo "APP_NAME=HomeMechanic\n";
echo "APP_ENV=local\n";
echo "APP_KEY=\n";
echo "APP_DEBUG=true\n";
echo "APP_TIMEZONE=America/Sao_Paulo\n";
echo "APP_URL=http://localhost\n";
echo "\n";
echo "LOG_CHANNEL=stack\n";
echo "LOG_DEPRECATIONS_CHANNEL=null\n";
echo "LOG_LEVEL=debug\n";
echo "        </div>\n";

echo "        <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>\n";
echo "            <p><strong>HomeMechanic v1.0.0</strong> - Debug do Instalador</p>\n";
echo "            <p style='color: #666; font-size: 0.9rem;'>Executado em " . date('d/m/Y H:i:s') . "</p>\n";
echo "        </div>\n";
echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
?>