<?php
/**
 * HomeMechanic - Teste Completo de Instalação
 * Script para testar todo o processo de instalação passo a passo
 */

// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para exibir status
function showStatus($title, $status, $message = '', $details = null) {
    $icon = $status ? '✅' : '❌';
    $class = $status ? 'success' : 'error';
    
    echo "<div class='status {$class}'>\n";
    echo "    <strong>{$icon} {$title}</strong>";
    if ($message) {
        echo "<br>{$message}";
    }
    if ($details) {
        echo "<div class='details'>{$details}</div>";
    }
    echo "</div>\n";
}

// Função para testar instalação completa
function testCompleteInstallation() {
    $testData = [
        'database' => [
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'homemechanic_2026',
            'username' => 'homemechanic',
            'password' => 'Hm2026@Secure!'
        ],
        'admin' => [
            'name' => 'Administrador Teste',
            'email' => 'admin@teste.com',
            'password' => 'admin123456'
        ],
        'company' => [
            'name' => 'HomeMechanic Teste',
            'description' => 'Sistema de teste para oficinas mecânicas'
        ],
        'system' => [
            'url' => 'https://homemechanic.com.br'
        ]
    ];
    
    try {
        // Carregar Laravel
        require_once __DIR__ . '/../vendor/autoload.php';
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        
        // Obter serviço do instalador
        $installerService = $app->make('App\Modules\Installer\Services\InstallerService');
        
        echo "<h3>🧪 Teste de Instalação Completa</h3>\n";
        
        // 1. Verificar requisitos
        echo "<h4>Etapa 1: Verificação de Requisitos</h4>\n";
        $requirements = $installerService->checkRequirements();
        
        $allOk = true;
        foreach ($requirements as $category => $items) {
            if (is_array($items) && isset($items['status'])) {
                showStatus($items['name'] ?? $category, $items['status'], $items['current'] ?? '');
                if ($items['required'] && !$items['status']) {
                    $allOk = false;
                }
            } elseif (is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['status'])) {
                        showStatus($item['name'], $item['status']);
                        if ($item['required'] && !$item['status']) {
                            $allOk = false;
                        }
                    }
                }
            }
        }
        
        if (!$allOk) {
            showStatus('Requisitos', false, 'Alguns requisitos não foram atendidos');
            return false;
        }
        
        // 2. Testar conexão com banco
        echo "<h4>Etapa 2: Teste de Conexão com Banco</h4>\n";
        $dbTest = $installerService->testDatabaseConnection($testData['database']);
        showStatus('Conexão DB', $dbTest['success'], $dbTest['message']);
        
        if (!$dbTest['success']) {
            return false;
        }
        
        // 3. Executar instalação (apenas se não estiver instalado)
        if (!$installerService->isInstalled()) {
            echo "<h4>Etapa 3: Executando Instalação</h4>\n";
            $installResult = $installerService->install($testData);
            showStatus('Instalação', $installResult['success'], $installResult['message']);
            
            if ($installResult['success']) {
                echo "<div class='status success'>\n";
                echo "    <strong>🎉 Instalação Concluída!</strong><br>\n";
                echo "    URL Admin: <a href='{$installResult['admin_url']}'>{$installResult['admin_url']}</a><br>\n";
                echo "    Email Admin: {$installResult['admin_email']}\n";
                echo "</div>\n";
            }
            
            return $installResult['success'];
        } else {
            showStatus('Sistema', true, 'Sistema já está instalado');
            return true;
        }
        
    } catch (Exception $e) {
        showStatus('Erro Fatal', false, $e->getMessage(), 
            "Arquivo: {$e->getFile()}<br>Linha: {$e->getLine()}");
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeMechanic - Teste de Instalação</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 40px; 
            background: #f4f6f9; 
        }
        .container { 
            max-width: 1000px; 
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
        .status { 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 8px; 
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            border-left: 4px solid #28a745; 
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            border-left: 4px solid #dc3545; 
        }
        .warning { 
            background: #fff3cd; 
            color: #856404; 
            border-left: 4px solid #ffc107; 
        }
        .info { 
            background: #cce7ff; 
            color: #004085; 
            border-left: 4px solid #007bff; 
        }
        .details {
            margin-top: 10px;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        .btn { 
            display: inline-block; 
            padding: 12px 24px; 
            background: #FF6B00; 
            color: white; 
            text-decoration: none; 
            border-radius: 6px; 
            margin: 10px 5px; 
        }
        .btn:hover { 
            background: #E55A00; 
        }
        h3, h4 {
            color: #333;
            border-bottom: 2px solid #FF6B00;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔧 HomeMechanic</div>
            <h2>Teste Completo de Instalação</h2>
            <p>Teste automatizado de todo o processo de instalação</p>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_test'])) {
            $success = testCompleteInstallation();
            
            if ($success) {
                echo "<div class='status success'>\n";
                echo "    <strong>🎉 Teste Concluído com Sucesso!</strong><br>\n";
                echo "    O sistema foi instalado e está funcionando corretamente.\n";
                echo "</div>\n";
            } else {
                echo "<div class='status error'>\n";
                echo "    <strong>❌ Teste Falhou</strong><br>\n";
                echo "    Verifique os erros acima e corrija antes de tentar novamente.\n";
                echo "</div>\n";
            }
        } else {
            echo "<div class='status info'>\n";
            echo "    <strong>ℹ️ Pronto para Testar</strong><br>\n";
            echo "    Este teste irá verificar todos os requisitos e executar uma instalação completa do sistema.\n";
            echo "    <br><br>\n";
            echo "    <strong>Dados de Teste:</strong><br>\n";
            echo "    • Banco: homemechanic_2026<br>\n";
            echo "    • Usuário DB: homemechanic<br>\n";
            echo "    • Admin: admin@teste.com / admin123456<br>\n";
            echo "    • Empresa: HomeMechanic Teste\n";
            echo "</div>\n";
            
            echo "<form method='POST'>\n";
            echo "    <button type='submit' name='run_test' value='1' class='btn'>🚀 Executar Teste Completo</button>\n";
            echo "</form>\n";
        }
        ?>

        <div class="status info">
            <strong>🔗 Links Úteis</strong><br>
            <a href="/install" class="btn">🔧 Instalador Normal</a>
            <a href="/debug-installation.php" class="btn">🐛 Debug Detalhado</a>
            <a href="/fix-key-emergency.php" class="btn">🔑 Corrigir APP_KEY</a>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>HomeMechanic v1.0.0</strong> - Teste de Instalação</p>
            <p style="color: #666; font-size: 0.9rem;">Executado em <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>