<?php
/**
 * HomeMechanic - Verificação Específica do Erro de Instalação
 * Script para reproduzir exatamente o erro que está ocorrendo
 */

// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeMechanic - Verificação do Erro</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
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
        form { margin: 20px 0; }
        input, select { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; }
        input[type="text"], input[type="email"], input[type="password"] { width: 200px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔧 HomeMechanic</div>
            <h2>Verificação do Erro de Instalação</h2>
            <p>Reproduzindo exatamente o erro que está ocorrendo</p>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<h3>🔍 Executando Instalação com Dados Fornecidos</h3>";
            
            try {
                // Capturar dados do formulário
                $installData = [
                    'database' => [
                        'host' => $_POST['db_host'] ?? '127.0.0.1',
                        'port' => $_POST['db_port'] ?? 3306,
                        'name' => $_POST['db_name'] ?? '',
                        'username' => $_POST['db_user'] ?? '',
                        'password' => $_POST['db_password'] ?? ''
                    ],
                    'admin' => [
                        'name' => $_POST['admin_name'] ?? '',
                        'email' => $_POST['admin_email'] ?? '',
                        'password' => $_POST['admin_password'] ?? ''
                    ],
                    'company' => [
                        'name' => $_POST['company_name'] ?? 'HomeMechanic',
                        'description' => $_POST['company_description'] ?? 'Sistema de gestão para oficinas mecânicas'
                    ],
                    'system' => [
                        'url' => $_POST['system_url'] ?? ''
                    ]
                ];
                
                echo "<div class='status info'>";
                echo "<strong>📋 Dados Recebidos:</strong><br>";
                echo "Banco: " . htmlspecialchars($installData['database']['host']) . ":" . $installData['database']['port'] . "/" . htmlspecialchars($installData['database']['name']) . "<br>";
                echo "Usuário DB: " . htmlspecialchars($installData['database']['username']) . "<br>";
                echo "Admin: " . htmlspecialchars($installData['admin']['name']) . " (" . htmlspecialchars($installData['admin']['email']) . ")<br>";
                echo "Empresa: " . htmlspecialchars($installData['company']['name']);
                echo "</div>";
                
                // Carregar Laravel
                require_once __DIR__ . '/../vendor/autoload.php';
                $app = require_once __DIR__ . '/../bootstrap/app.php';
                
                echo "<div class='status success'>✅ Laravel carregado</div>";
                
                // Simular exatamente o que o InstallerController faz
                $installerService = $app->make('App\Modules\Installer\Services\InstallerService');
                
                echo "<div class='status success'>✅ InstallerService carregado</div>";
                
                // Verificar se já está instalado
                if ($installerService->isInstalled()) {
                    echo "<div class='status warning'>⚠️ Sistema já está instalado</div>";
                    
                    // Mostrar opção para remover instalação
                    echo "<div class='status info'>";
                    echo "<strong>Para testar novamente:</strong><br>";
                    echo "1. Remova o arquivo: storage/installed<br>";
                    echo "2. Limpe o banco de dados<br>";
                    echo "3. Tente novamente";
                    echo "</div>";
                    
                } else {
                    echo "<div class='status info'>ℹ️ Sistema não instalado - prosseguindo</div>";
                    
                    // Testar conexão com banco
                    echo "<h4>Testando Conexão com Banco</h4>";
                    $dbTest = $installerService->testDatabaseConnection($installData['database']);
                    
                    if ($dbTest['success']) {
                        echo "<div class='status success'>✅ Conexão com banco OK</div>";
                        
                        // Executar instalação
                        echo "<h4>Executando Instalação</h4>";
                        echo "<div class='status warning'>⚠️ Iniciando processo de instalação...</div>";
                        
                        $result = $installerService->install($installData);
                        
                        if ($result['success']) {
                            echo "<div class='status success'>";
                            echo "<strong>🎉 Instalação Concluída com Sucesso!</strong><br>";
                            echo "Mensagem: " . htmlspecialchars($result['message']) . "<br>";
                            if (isset($result['admin_url'])) {
                                echo "URL Admin: <a href='" . htmlspecialchars($result['admin_url']) . "'>" . htmlspecialchars($result['admin_url']) . "</a><br>";
                            }
                            if (isset($result['admin_email'])) {
                                echo "Email Admin: " . htmlspecialchars($result['admin_email']);
                            }
                            echo "</div>";
                        } else {
                            echo "<div class='status error'>";
                            echo "<strong>❌ ERRO NA INSTALAÇÃO</strong><br>";
                            echo "Mensagem: " . htmlspecialchars($result['message']) . "<br>";
                            
                            if (isset($result['details'])) {
                                echo "<br><strong>Detalhes do Erro:</strong><br>";
                                if (is_array($result['details'])) {
                                    foreach ($result['details'] as $key => $value) {
                                        echo htmlspecialchars($key) . ": " . htmlspecialchars($value) . "<br>";
                                    }
                                } else {
                                    echo htmlspecialchars($result['details']);
                                }
                            }
                            echo "</div>";
                        }
                        
                    } else {
                        echo "<div class='status error'>❌ Erro na conexão com banco: " . htmlspecialchars($dbTest['message']) . "</div>";
                    }
                }
                
            } catch (Exception $e) {
                echo "<div class='status error'>";
                echo "<strong>❌ EXCEÇÃO CAPTURADA:</strong><br>";
                echo "Tipo: " . get_class($e) . "<br>";
                echo "Mensagem: " . htmlspecialchars($e->getMessage()) . "<br>";
                echo "Arquivo: " . htmlspecialchars($e->getFile()) . "<br>";
                echo "Linha: " . $e->getLine() . "<br>";
                echo "</div>";
                
                echo "<div class='code'>";
                echo "Stack Trace:\n";
                echo htmlspecialchars($e->getTraceAsString());
                echo "</div>";
            }
            
            // Mostrar logs recentes
            echo "<h3>📋 Logs Recentes do Laravel</h3>";
            $logFile = __DIR__ . '/../storage/logs/laravel.log';
            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                $logLines = explode("\n", $logContent);
                $recentLines = array_slice($logLines, -30); // Últimas 30 linhas
                
                // Filtrar apenas linhas relevantes
                $relevantLines = array_filter($recentLines, function($line) {
                    return !empty(trim($line)) && (
                        strpos($line, 'ERROR') !== false ||
                        strpos($line, 'Exception') !== false ||
                        strpos($line, 'instalação') !== false ||
                        strpos($line, 'Installer') !== false
                    );
                });
                
                if (!empty($relevantLines)) {
                    echo "<div class='code'>";
                    echo htmlspecialchars(implode("\n", $relevantLines));
                    echo "</div>";
                } else {
                    echo "<div class='status info'>ℹ️ Nenhum erro relevante encontrado nos logs recentes</div>";
                }
            } else {
                echo "<div class='status warning'>⚠️ Arquivo de log não existe</div>";
            }
            
        } else {
            // Mostrar formulário
            echo "<h3>📝 Dados para Teste de Instalação</h3>";
            echo "<p>Preencha os dados exatamente como você está tentando instalar:</p>";
            
            echo "<form method='POST'>";
            
            echo "<h4>🗄️ Banco de Dados</h4>";
            echo "<label>Host: <input type='text' name='db_host' value='127.0.0.1' required></label><br>";
            echo "<label>Porta: <input type='number' name='db_port' value='3306' required></label><br>";
            echo "<label>Nome do Banco: <input type='text' name='db_name' value='homemechanic_2026' required></label><br>";
            echo "<label>Usuário: <input type='text' name='db_user' value='homemechanic' required></label><br>";
            echo "<label>Senha: <input type='password' name='db_password' value='Hm2026@Secure!'></label><br>";
            
            echo "<h4>👤 Administrador</h4>";
            echo "<label>Nome: <input type='text' name='admin_name' value='Administrador' required></label><br>";
            echo "<label>Email: <input type='email' name='admin_email' value='admin@homemechanic.com.br' required></label><br>";
            echo "<label>Senha: <input type='password' name='admin_password' value='admin123456' required></label><br>";
            
            echo "<h4>🏢 Empresa</h4>";
            echo "<label>Nome: <input type='text' name='company_name' value='HomeMechanic'></label><br>";
            echo "<label>Descrição: <input type='text' name='company_description' value='Sistema de gestão para oficinas mecânicas'></label><br>";
            
            echo "<h4>🌐 Sistema</h4>";
            echo "<label>URL: <input type='text' name='system_url' value='https://homemechanic.com.br'></label><br>";
            
            echo "<br><button type='submit' class='btn'>🚀 Testar Instalação</button>";
            echo "</form>";
        }
        ?>

        <div class="status info">
            <strong>🔗 Links Úteis</strong><br>
            <a href="/install" class="btn">🔧 Instalador Normal</a>
            <a href="/debug-error.php" class="btn">🐛 Debug Completo</a>
            <a href="/fix-key-emergency.php" class="btn">🔑 Correção de Emergência</a>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>HomeMechanic v1.0.0</strong> - Verificação do Erro</p>
            <p style="color: #666; font-size: 0.9rem;">Executado em <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>