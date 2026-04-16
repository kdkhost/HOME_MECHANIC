<?php
/**
 * HomeMechanic - Teste Simples (Sem Container Laravel)
 * Script básico para testar problemas fundamentais
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
    <title>HomeMechanic - Teste Simples</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔧 HomeMechanic</div>
            <h2>Teste Simples do Sistema</h2>
            <p>Verificação básica sem dependências do Laravel</p>
        </div>

        <?php
        echo "<h3>🔍 Verificações Básicas</h3>";
        
        // 1. Verificar PHP
        echo "<h4>1. Versão do PHP</h4>";
        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '8.4.0', '>=') && version_compare($phpVersion, '8.5.0', '<');
        
        if ($phpOk) {
            echo "<div class='status success'>✅ PHP {$phpVersion} - Compatível</div>";
        } else {
            echo "<div class='status error'>❌ PHP {$phpVersion} - Requer PHP 8.4.x</div>";
        }
        
        // 2. Verificar extensões
        echo "<h4>2. Extensões PHP</h4>";
        $requiredExtensions = [
            'pdo' => 'PDO',
            'pdo_mysql' => 'PDO MySQL',
            'mbstring' => 'Mbstring',
            'openssl' => 'OpenSSL',
            'tokenizer' => 'Tokenizer',
            'xml' => 'XML',
            'ctype' => 'Ctype',
            'json' => 'JSON',
            'bcmath' => 'BCMath',
            'fileinfo' => 'Fileinfo',
            'gd' => 'GD'
        ];
        
        $allExtensionsOk = true;
        foreach ($requiredExtensions as $ext => $name) {
            if (extension_loaded($ext)) {
                echo "<div class='status success'>✅ {$name}</div>";
            } else {
                echo "<div class='status error'>❌ {$name} - Não instalada</div>";
                $allExtensionsOk = false;
            }
        }
        
        // 3. Verificar arquivos
        echo "<h4>3. Arquivos do Sistema</h4>";
        
        $files = [
            'vendor/autoload.php' => 'Autoloader do Composer',
            'bootstrap/app.php' => 'Bootstrap do Laravel',
            '.env' => 'Arquivo de configuração',
            'app/Modules/Installer/Services/InstallerService.php' => 'Serviço do Instalador'
        ];
        
        foreach ($files as $file => $desc) {
            $fullPath = __DIR__ . '/../' . $file;
            if (file_exists($fullPath)) {
                echo "<div class='status success'>✅ {$desc}</div>";
            } else {
                echo "<div class='status error'>❌ {$desc} - Não encontrado: {$file}</div>";
            }
        }
        
        // 4. Verificar .env
        echo "<h4>4. Configuração .env</h4>";
        $envPath = __DIR__ . '/../.env';
        
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            
            // Verificar APP_KEY
            if (preg_match('/APP_KEY=(.+)/', $envContent, $matches)) {
                $appKey = trim($matches[1]);
                if (!empty($appKey) && $appKey !== '') {
                    echo "<div class='status success'>✅ APP_KEY configurada</div>";
                } else {
                    echo "<div class='status error'>❌ APP_KEY vazia</div>";
                }
            } else {
                echo "<div class='status error'>❌ APP_KEY não encontrada</div>";
            }
            
            // Verificar configurações do banco
            $dbConfigs = ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME'];
            foreach ($dbConfigs as $config) {
                if (preg_match("/{$config}=(.+)/", $envContent, $matches)) {
                    $value = trim($matches[1], '"\'');
                    if (!empty($value)) {
                        echo "<div class='status success'>✅ {$config} configurado</div>";
                    } else {
                        echo "<div class='status warning'>⚠️ {$config} vazio</div>";
                    }
                } else {
                    echo "<div class='status error'>❌ {$config} não encontrado</div>";
                }
            }
        } else {
            echo "<div class='status error'>❌ Arquivo .env não existe</div>";
        }
        
        // 5. Verificar permissões
        echo "<h4>5. Permissões de Diretórios</h4>";
        
        $directories = [
            'storage' => 'Armazenamento geral',
            'storage/app' => 'Arquivos da aplicação',
            'storage/framework' => 'Cache do framework',
            'storage/logs' => 'Logs do sistema',
            'bootstrap/cache' => 'Cache do bootstrap'
        ];
        
        foreach ($directories as $dir => $desc) {
            $fullPath = __DIR__ . '/../' . $dir;
            if (is_dir($fullPath)) {
                if (is_writable($fullPath)) {
                    echo "<div class='status success'>✅ {$desc} - Gravável</div>";
                } else {
                    echo "<div class='status error'>❌ {$desc} - Sem permissão de escrita</div>";
                }
            } else {
                echo "<div class='status warning'>⚠️ {$desc} - Diretório não existe</div>";
            }
        }
        
        // 6. Teste de conexão com banco (se configurado)
        echo "<h4>6. Teste de Conexão com Banco</h4>";
        
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            
            // Extrair configurações do banco
            $dbHost = '';
            $dbPort = '3306';
            $dbName = '';
            $dbUser = '';
            $dbPass = '';
            
            if (preg_match('/DB_HOST=(.+)/', $envContent, $matches)) {
                $dbHost = trim($matches[1], '"\'');
            }
            if (preg_match('/DB_PORT=(.+)/', $envContent, $matches)) {
                $dbPort = trim($matches[1], '"\'');
            }
            if (preg_match('/DB_DATABASE=(.+)/', $envContent, $matches)) {
                $dbName = trim($matches[1], '"\'');
            }
            if (preg_match('/DB_USERNAME=(.+)/', $envContent, $matches)) {
                $dbUser = trim($matches[1], '"\'');
            }
            if (preg_match('/DB_PASSWORD=(.+)/', $envContent, $matches)) {
                $dbPass = trim($matches[1], '"\'');
            }
            
            if (!empty($dbHost) && !empty($dbName) && !empty($dbUser)) {
                try {
                    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
                    $pdo = new PDO($dsn, $dbUser, $dbPass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_TIMEOUT => 5
                    ]);
                    
                    $pdo->query('SELECT 1');
                    echo "<div class='status success'>✅ Conexão com banco OK</div>";
                    
                } catch (PDOException $e) {
                    echo "<div class='status error'>❌ Erro na conexão: " . htmlspecialchars($e->getMessage()) . "</div>";
                }
            } else {
                echo "<div class='status warning'>⚠️ Configurações do banco incompletas</div>";
            }
        }
        
        // 7. Verificar se já está instalado
        echo "<h4>7. Status da Instalação</h4>";
        
        $installedFile = __DIR__ . '/../storage/installed';
        if (file_exists($installedFile)) {
            $installInfo = file_get_contents($installedFile);
            echo "<div class='status info'>ℹ️ Sistema já está instalado</div>";
            
            // Tentar decodificar informações
            $info = json_decode($installInfo, true);
            if ($info && isset($info['installed_at'])) {
                echo "<div class='status info'>📅 Instalado em: " . $info['installed_at'] . "</div>";
                if (isset($info['admin_email'])) {
                    echo "<div class='status info'>👤 Admin: " . htmlspecialchars($info['admin_email']) . "</div>";
                }
            }
        } else {
            echo "<div class='status warning'>⚠️ Sistema não está instalado</div>";
        }
        
        // Resumo final
        echo "<h3>📊 Resumo</h3>";
        
        if ($phpOk && $allExtensionsOk) {
            echo "<div class='status success'>";
            echo "<strong>✅ Sistema Pronto</strong><br>";
            echo "Todos os requisitos básicos foram atendidos. ";
            if (!file_exists($installedFile)) {
                echo "Você pode prosseguir com a instalação.";
            } else {
                echo "Sistema já está instalado e funcionando.";
            }
            echo "</div>";
        } else {
            echo "<div class='status error'>";
            echo "<strong>❌ Problemas Encontrados</strong><br>";
            echo "Corrija os problemas acima antes de prosseguir com a instalação.";
            echo "</div>";
        }
        ?>

        <div class="status info">
            <strong>🔗 Próximos Passos</strong><br>
            <?php if (!file_exists($installedFile)): ?>
                <a href="/fix-key-emergency.php" class="btn">🔑 Corrigir Configuração</a>
                <a href="/install" class="btn">🔧 Instalar Sistema</a>
            <?php else: ?>
                <a href="/" class="btn">🏠 Página Inicial</a>
                <a href="/admin" class="btn">⚙️ Painel Admin</a>
            <?php endif; ?>
            <a href="/debug-error.php" class="btn">🐛 Debug Avançado</a>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>HomeMechanic v1.0.1</strong> - Teste Simples</p>
            <p style="color: #666; font-size: 0.9rem;">Executado em <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>