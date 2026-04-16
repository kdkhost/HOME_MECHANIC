<?php
/**
 * HomeMechanic - Debug Específico do Erro de Instalação
 * Script para capturar o erro exato que está ocorrendo
 */

// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Função para capturar todos os erros
function captureError($errno, $errstr, $errfile, $errline) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #dc3545;'>";
    echo "<strong>❌ Erro PHP:</strong><br>";
    echo "Tipo: " . $errno . "<br>";
    echo "Mensagem: " . htmlspecialchars($errstr) . "<br>";
    echo "Arquivo: " . htmlspecialchars($errfile) . "<br>";
    echo "Linha: " . $errline . "<br>";
    echo "</div>";
}

set_error_handler('captureError');

// Função para capturar exceções
function captureException($exception) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #dc3545;'>";
    echo "<strong>❌ Exceção:</strong><br>";
    echo "Tipo: " . get_class($exception) . "<br>";
    echo "Mensagem: " . htmlspecialchars($exception->getMessage()) . "<br>";
    echo "Arquivo: " . htmlspecialchars($exception->getFile()) . "<br>";
    echo "Linha: " . $exception->getLine() . "<br>";
    echo "<details><summary>Stack Trace</summary><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre></details>";
    echo "</div>";
}

set_exception_handler('captureException');

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeMechanic - Debug do Erro</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { color: #FF6B00; font-size: 2rem; font-weight: bold; }
        .status { padding: 15px; margin: 10px 0; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .info { background: #cce7ff; color: #004085; border-left: 4px solid #007bff; }
        .code { background: #f8f9fa; padding: 15px; border-radius: 6px; font-family: monospace; margin: 10px 0; white-space: pre-wrap; }
        .btn { display: inline-block; padding: 12px 24px; background: #FF6B00; color: white; text-decoration: none; border-radius: 6px; margin: 10px 5px; }
        .btn:hover { background: #E55A00; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔧 HomeMechanic</div>
            <h2>Debug Específico do Erro de Instalação</h2>
            <p>Capturando o erro exato que está ocorrendo</p>
        </div>

        <?php
        echo "<h3>🔍 Simulando Processo de Instalação</h3>";
        
        try {
            // 1. Testar carregamento básico
            echo "<h4>Etapa 1: Carregamento do Laravel</h4>";
            
            if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
                throw new Exception('Autoloader não encontrado');
            }
            
            require_once __DIR__ . '/../vendor/autoload.php';
            echo "<div class='status success'>✅ Autoloader carregado</div>";
            
            if (!file_exists(__DIR__ . '/../bootstrap/app.php')) {
                throw new Exception('bootstrap/app.php não encontrado');
            }
            
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            echo "<div class='status success'>✅ Laravel app carregado</div>";
            
            // 2. Testar serviço do instalador
            echo "<h4>Etapa 2: Carregamento do Serviço de Instalação</h4>";
            
            $installerService = $app->make('App\Modules\Installer\Services\InstallerService');
            echo "<div class='status success'>✅ InstallerService carregado</div>";
            
            // 3. Testar verificação de requisitos
            echo "<h4>Etapa 3: Verificação de Requisitos</h4>";
            
            $requirements = $installerService->checkRequirements();
            echo "<div class='status success'>✅ Requisitos verificados</div>";
            
            // 4. Testar dados de instalação simulados
            echo "<h4>Etapa 4: Teste com Dados Simulados</h4>";
            
            $testData = [
                'database' => [
                    'host' => '127.0.0.1',
                    'port' => 3306,
                    'name' => 'homemechanic_2026',
                    'username' => 'homemechanic',
                    'password' => 'Hm2026@Secure!'
                ],
                'admin' => [
                    'name' => 'Admin Teste',
                    'email' => 'admin@teste.com',
                    'password' => 'admin123456'
                ],
                'company' => [
                    'name' => 'HomeMechanic Teste',
                    'description' => 'Sistema de teste'
                ],
                'system' => [
                    'url' => 'https://homemechanic.com.br'
                ]
            ];
            
            // 5. Testar conexão com banco
            echo "<h4>Etapa 5: Teste de Conexão com Banco</h4>";
            
            $dbTest = $installerService->testDatabaseConnection($testData['database']);
            if ($dbTest['success']) {
                echo "<div class='status success'>✅ Conexão com banco OK</div>";
            } else {
                echo "<div class='status error'>❌ Erro na conexão: " . htmlspecialchars($dbTest['message']) . "</div>";
            }
            
            // 6. Verificar se já está instalado
            echo "<h4>Etapa 6: Verificação de Instalação Existente</h4>";
            
            if ($installerService->isInstalled()) {
                echo "<div class='status warning'>⚠️ Sistema já está instalado</div>";
                
                // Mostrar informações da instalação
                $installInfo = $installerService->getInstallationInfo();
                echo "<div class='status info'>";
                echo "<strong>Informações da Instalação:</strong><br>";
                echo "Data: " . ($installInfo['installed_at'] ?? 'N/A') . "<br>";
                echo "Versão: " . ($installInfo['version'] ?? 'N/A') . "<br>";
                echo "Admin: " . ($installInfo['admin_email'] ?? 'N/A');
                echo "</div>";
            } else {
                echo "<div class='status info'>ℹ️ Sistema não está instalado - OK para prosseguir</div>";
                
                // 7. Tentar executar instalação (apenas se não estiver instalado)
                echo "<h4>Etapa 7: Tentativa de Instalação</h4>";
                
                echo "<div class='status warning'>⚠️ Executando instalação de teste...</div>";
                
                $result = $installerService->install($testData);
                
                if ($result['success']) {
                    echo "<div class='status success'>✅ Instalação concluída com sucesso!</div>";
                    echo "<div class='status info'>";
                    echo "<strong>Detalhes:</strong><br>";
                    echo "Mensagem: " . htmlspecialchars($result['message']) . "<br>";
                    if (isset($result['admin_url'])) {
                        echo "URL Admin: " . htmlspecialchars($result['admin_url']) . "<br>";
                    }
                    if (isset($result['admin_email'])) {
                        echo "Email Admin: " . htmlspecialchars($result['admin_email']);
                    }
                    echo "</div>";
                } else {
                    echo "<div class='status error'>❌ Falha na instalação</div>";
                    echo "<div class='status error'>";
                    echo "<strong>Erro:</strong> " . htmlspecialchars($result['message']) . "<br>";
                    if (isset($result['details'])) {
                        echo "<strong>Detalhes:</strong><br>";
                        if (is_array($result['details'])) {
                            foreach ($result['details'] as $key => $value) {
                                echo $key . ": " . htmlspecialchars($value) . "<br>";
                            }
                        } else {
                            echo htmlspecialchars($result['details']);
                        }
                    }
                    echo "</div>";
                }
            }
            
        } catch (Exception $e) {
            echo "<div class='status error'>";
            echo "<strong>❌ Exceção Capturada:</strong><br>";
            echo "Tipo: " . get_class($e) . "<br>";
            echo "Mensagem: " . htmlspecialchars($e->getMessage()) . "<br>";
            echo "Arquivo: " . htmlspecialchars($e->getFile()) . "<br>";
            echo "Linha: " . $e->getLine() . "<br>";
            echo "</div>";
            
            echo "<div class='code'>";
            echo htmlspecialchars($e->getTraceAsString());
            echo "</div>";
        }
        
        // Verificar logs recentes
        echo "<h3>📋 Logs Recentes</h3>";
        $logFile = __DIR__ . '/../storage/logs/laravel.log';
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $logLines = explode("\n", $logContent);
            $recentLines = array_slice($logLines, -20); // Últimas 20 linhas
            
            echo "<div class='code'>";
            echo htmlspecialchars(implode("\n", $recentLines));
            echo "</div>";
        } else {
            echo "<div class='status info'>ℹ️ Arquivo de log não existe ainda</div>";
        }
        ?>

        <div class="status info">
            <strong>🔗 Links Úteis</strong><br>
            <a href="/install" class="btn">🔧 Voltar ao Instalador</a>
            <a href="/fix-key-emergency.php" class="btn">🔑 Correção de Emergência</a>
            <a href="/debug-installation.php" class="btn">🐛 Debug Geral</a>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>HomeMechanic v1.0.0</strong> - Debug Específico do Erro</p>
            <p style="color: #666; font-size: 0.9rem;">Executado em <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>