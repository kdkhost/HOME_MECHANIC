<?php
/**
 * HomeMechanic - Debug de Instalação
 * Script para capturar erros detalhados durante a instalação
 */

// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>\n";
echo "<html lang='pt-BR'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>HomeMechanic - Debug de Instalação</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; }\n";
echo "        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }\n";
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
echo "            <h2>Debug de Instalação</h2>\n";
echo "            <p>Diagnóstico detalhado de problemas na instalação</p>\n";
echo "        </div>\n";

// Verificar se Laravel pode ser carregado
echo "        <h3>🚀 Teste de Carregamento do Laravel</h3>\n";
try {
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        echo "        <div class='status success'>\n";
        echo "            <strong>✅ Autoloader</strong> - Carregado com sucesso\n";
        echo "        </div>\n";
        
        if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            echo "        <div class='status success'>\n";
            echo "            <strong>✅ Laravel App</strong> - Carregado com sucesso\n";
            echo "        </div>\n";
            
            // Testar configuração sem usar container
            try {
                // Verificar .env diretamente
                $envPath = __DIR__ . '/../.env';
                if (file_exists($envPath)) {
                    $envContent = file_get_contents($envPath);
                    
                    if (preg_match('/APP_KEY=(.+)/', $envContent, $matches)) {
                        $appKey = trim($matches[1]);
                        if (!empty($appKey) && $appKey !== '') {
                            echo "        <div class='status success'>\n";
                            echo "            <strong>✅ APP_KEY</strong> - Encontrada: " . substr($appKey, 0, 20) . "...\n";
                            echo "        </div>\n";
                        } else {
                            echo "        <div class='status error'>\n";
                            echo "            <strong>❌ APP_KEY</strong> - Vazia no .env\n";
                            echo "        </div>\n";
                        }
                    } else {
                        echo "        <div class='status error'>\n";
                        echo "            <strong>❌ APP_KEY</strong> - Não encontrada no .env\n";
                        echo "        </div>\n";
                    }
                } else {
                    echo "        <div class='status error'>\n";
                    echo "            <strong>❌ Arquivo .env</strong> - Não encontrado\n";
                    echo "        </div>\n";
                }
                
            } catch (Exception $e) {
                echo "        <div class='status error'>\n";
                echo "            <strong>❌ Erro de Configuração</strong><br>\n";
                echo "            " . htmlspecialchars($e->getMessage()) . "\n";
                echo "        </div>\n";
            }
        }
    } else {
        echo "        <div class='status error'>\n";
        echo "            <strong>❌ Autoloader</strong> - vendor/autoload.php não encontrado\n";
        echo "        </div>\n";
    }
} catch (Exception $e) {
    echo "        <div class='status error'>\n";
    echo "        <strong>❌ Erro Laravel</strong><br>\n";
    echo "        " . htmlspecialchars($e->getMessage()) . "\n";
    echo "        </div>\n";
}

// Testar conexão com banco (se dados fornecidos via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "        <h3>🗄️ Teste de Conexão com Banco</h3>\n";
    
    $dbHost = $_POST['db_host'] ?? '';
    $dbPort = $_POST['db_port'] ?? '3306';
    $dbName = $_POST['db_name'] ?? '';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_password'] ?? '';
    
    if ($dbHost && $dbName && $dbUser) {
        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 10
            ]);
            
            $pdo->query('SELECT 1');
            
            echo "        <div class='status success'>\n";
            echo "            <strong>✅ Conexão DB</strong> - Sucesso\n";
            echo "        </div>\n";
            
            // Testar criação de tabela
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS test_table (id INT PRIMARY KEY AUTO_INCREMENT, test VARCHAR(50))");
                $pdo->exec("DROP TABLE test_table");
                
                echo "        <div class='status success'>\n";
                echo "            <strong>✅ Permissões DB</strong> - CREATE/DROP OK\n";
                echo "        </div>\n";
            } catch (PDOException $e) {
                echo "        <div class='status error'>\n";
                echo "            <strong>❌ Permissões DB</strong><br>\n";
                echo "            " . htmlspecialchars($e->getMessage()) . "\n";
                echo "        </div>\n";
            }
            
        } catch (PDOException $e) {
            echo "        <div class='status error'>\n";
            echo "            <strong>❌ Conexão DB</strong><br>\n";
            echo "            " . htmlspecialchars($e->getMessage()) . "\n";
            echo "        </div>\n";
        }
    }
}

// Verificar migrations
echo "        <h3>📋 Verificação de Migrations</h3>\n";
$migrationsDir = __DIR__ . '/../database/migrations';
if (is_dir($migrationsDir)) {
    $migrations = glob($migrationsDir . '/*.php');
    echo "        <div class='status info'>\n";
    echo "            <strong>📁 Migrations encontradas:</strong> " . count($migrations) . "\n";
    echo "        </div>\n";
    
    foreach ($migrations as $migration) {
        $filename = basename($migration);
        echo "        <div class='status info'>\n";
        echo "            <strong>📄</strong> " . htmlspecialchars($filename) . "\n";
        echo "        </div>\n";
    }
} else {
    echo "        <div class='status error'>\n";
    echo "            <strong>❌ Migrations</strong> - Diretório não encontrado\n";
    echo "        </div>\n";
}

// Verificar logs de erro
echo "        <h3>📋 Logs de Erro Recentes</h3>\n";
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $logLines = explode("\n", $logContent);
    $recentLines = array_slice($logLines, -30); // Últimas 30 linhas
    
    // Filtrar apenas linhas com ERROR
    $errorLines = array_filter($recentLines, function($line) {
        return strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false;
    });
    
    if (!empty($errorLines)) {
        echo "        <div class='status error'>\n";
        echo "            <strong>❌ Erros encontrados nos logs:</strong>\n";
        echo "        </div>\n";
        echo "        <div class='code'>\n";
        echo htmlspecialchars(implode("\n", array_slice($errorLines, -10))); // Últimos 10 erros
        echo "        </div>\n";
    } else {
        echo "        <div class='status success'>\n";
        echo "            <strong>✅ Logs</strong> - Nenhum erro recente encontrado\n";
        echo "        </div>\n";
    }
} else {
    echo "        <div class='status info'>\n";
    echo "            <strong>📄 Logs</strong> - Arquivo de log não existe ainda\n";
    echo "        </div>\n";
}

// Formulário de teste de banco
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "        <h3>🧪 Testar Conexão com Banco</h3>\n";
    echo "        <form method='POST'>\n";
    echo "            <div style='margin-bottom: 15px;'>\n";
    echo "                <label>Host: <input type='text' name='db_host' value='127.0.0.1' style='margin-left: 10px; padding: 5px;'></label>\n";
    echo "            </div>\n";
    echo "            <div style='margin-bottom: 15px;'>\n";
    echo "                <label>Porta: <input type='number' name='db_port' value='3306' style='margin-left: 10px; padding: 5px;'></label>\n";
    echo "            </div>\n";
    echo "            <div style='margin-bottom: 15px;'>\n";
    echo "                <label>Banco: <input type='text' name='db_name' placeholder='nome_do_banco' style='margin-left: 10px; padding: 5px;'></label>\n";
    echo "            </div>\n";
    echo "            <div style='margin-bottom: 15px;'>\n";
    echo "                <label>Usuário: <input type='text' name='db_user' placeholder='usuario' style='margin-left: 10px; padding: 5px;'></label>\n";
    echo "            </div>\n";
    echo "            <div style='margin-bottom: 15px;'>\n";
    echo "                <label>Senha: <input type='password' name='db_password' placeholder='senha' style='margin-left: 10px; padding: 5px;'></label>\n";
    echo "            </div>\n";
    echo "            <button type='submit' class='btn'>Testar Conexão</button>\n";
    echo "        </form>\n";
}

// Verificar permissões críticas
echo "        <h3>🔐 Permissões de Diretórios</h3>\n";
$criticalDirs = [
    'storage' => 'Armazenamento geral',
    'storage/app' => 'Arquivos da aplicação',
    'storage/framework' => 'Cache do framework',
    'storage/framework/cache' => 'Cache de dados',
    'storage/framework/sessions' => 'Sessões',
    'storage/framework/views' => 'Views compiladas',
    'storage/logs' => 'Logs do sistema',
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

// Links úteis
echo "        <h3>🔗 Links Úteis</h3>\n";
echo "        <div class='status info'>\n";
echo "            <a href='/install' class='btn'>🔧 Voltar ao Instalador</a>\n";
echo "            <a href='/fix-key-emergency.php' class='btn'>🔑 Corrigir APP_KEY</a>\n";
echo "            <a href='/test-fix.php' class='btn'>🧪 Teste Geral</a>\n";
echo "        </div>\n";

echo "        <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>\n";
echo "            <p><strong>HomeMechanic v1.0.0</strong> - Debug de Instalação</p>\n";
echo "            <p style='color: #666; font-size: 0.9rem;'>Executado em " . date('d/m/Y H:i:s') . "</p>\n";
echo "        </div>\n";
echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
?>