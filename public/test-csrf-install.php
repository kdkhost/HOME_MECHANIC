<?php
/**
 * HomeMechanic - Teste de CSRF e Instalação
 * Script para testar se o problema está relacionado ao CSRF token ou rotas
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
    <title>HomeMechanic - Teste CSRF e Instalação</title>
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
        form { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        input, textarea { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px; }
        button { padding: 10px 20px; background: #FF6B00; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #E55A00; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔧 HomeMechanic</div>
            <h2>Teste de CSRF e Instalação</h2>
            <p>Testando problemas relacionados ao CSRF token e rotas</p>
        </div>

        <?php
        // Testar carregamento do Laravel
        echo "<h3>🔍 Teste de Carregamento e Rotas</h3>";
        
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            
            echo "<div class='status success'>✅ Laravel carregado</div>";
            
            // Testar se as rotas existem
            $router = $app->make('router');
            
            echo "<div class='status info'>";
            echo "<strong>📋 Testando Rotas:</strong><br>";
            
            // Verificar rota do instalador
            try {
                $installRoute = route('installer.store');
                echo "✅ Rota installer.store: " . htmlspecialchars($installRoute) . "<br>";
            } catch (Exception $e) {
                echo "❌ Rota installer.store: " . htmlspecialchars($e->getMessage()) . "<br>";
            }
            
            try {
                $testDbRoute = route('installer.test-database');
                echo "✅ Rota installer.test-database: " . htmlspecialchars($testDbRoute) . "<br>";
            } catch (Exception $e) {
                echo "❌ Rota installer.test-database: " . htmlspecialchars($e->getMessage()) . "<br>";
            }
            
            echo "</div>";
            
            // Testar CSRF token
            echo "<h4>🔐 Teste de CSRF Token</h4>";
            
            try {
                $csrfToken = csrf_token();
                echo "<div class='status success'>✅ CSRF Token gerado: " . substr($csrfToken, 0, 20) . "...</div>";
            } catch (Exception $e) {
                echo "<div class='status error'>❌ Erro ao gerar CSRF Token: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='status error'>";
            echo "<strong>❌ Erro ao carregar Laravel:</strong><br>";
            echo htmlspecialchars($e->getMessage());
            echo "</div>";
        }
        ?>

        <h3>🧪 Teste Manual de Instalação</h3>
        <p>Use este formulário para testar a instalação diretamente (sem JavaScript):</p>

        <form method="POST" action="/install">
            <?php if (function_exists('csrf_token')): ?>
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <?php endif; ?>
            
            <h4>🗄️ Banco de Dados</h4>
            <label>Host:</label>
            <input type="text" name="db_host" value="127.0.0.1" required><br>
            
            <label>Porta:</label>
            <input type="number" name="db_port" value="3306" required><br>
            
            <label>Nome do Banco:</label>
            <input type="text" name="db_name" value="homemechanic_2026" required><br>
            
            <label>Usuário:</label>
            <input type="text" name="db_user" value="homemechanic" required><br>
            
            <label>Senha:</label>
            <input type="password" name="db_password" value="Hm2026@Secure!"><br>
            
            <h4>👤 Administrador</h4>
            <label>Nome:</label>
            <input type="text" name="admin_name" value="Administrador" required><br>
            
            <label>Email:</label>
            <input type="email" name="admin_email" value="admin@homemechanic.com.br" required><br>
            
            <label>Senha:</label>
            <input type="password" name="admin_password" value="admin123456" required><br>
            
            <label>Confirmar Senha:</label>
            <input type="password" name="admin_password_confirmation" value="admin123456" required><br>
            
            <h4>🏢 Empresa</h4>
            <label>Nome da Empresa:</label>
            <input type="text" name="company_name" value="HomeMechanic"><br>
            
            <label>Descrição:</label>
            <textarea name="company_description">Sistema de gestão para oficinas mecânicas especializadas em carros esportivos de luxo e tuning</textarea><br>
            
            <h4>🌐 Sistema</h4>
            <label>URL do Sistema:</label>
            <input type="text" name="system_url" value="https://homemechanic.com.br"><br>
            
            <br>
            <button type="submit">🚀 Instalar Sistema</button>
        </form>

        <h3>📋 Teste via JavaScript (AJAX)</h3>
        <button onclick="testAjaxInstall()" class="btn">🧪 Testar Instalação via AJAX</button>
        
        <div id="ajaxResult" style="margin-top: 20px;"></div>

        <script>
        async function testAjaxInstall() {
            const resultDiv = document.getElementById('ajaxResult');
            resultDiv.innerHTML = '<div class="status info">🔄 Testando instalação via AJAX...</div>';
            
            const formData = new FormData();
            formData.append('_token', '<?= function_exists("csrf_token") ? csrf_token() : "no-token" ?>');
            formData.append('db_host', '127.0.0.1');
            formData.append('db_port', '3306');
            formData.append('db_name', 'homemechanic_2026');
            formData.append('db_user', 'homemechanic');
            formData.append('db_password', 'Hm2026@Secure!');
            formData.append('admin_name', 'Administrador');
            formData.append('admin_email', 'admin@homemechanic.com.br');
            formData.append('admin_password', 'admin123456');
            formData.append('admin_password_confirmation', 'admin123456');
            formData.append('company_name', 'HomeMechanic');
            formData.append('company_description', 'Sistema de gestão para oficinas mecânicas');
            formData.append('system_url', 'https://homemechanic.com.br');
            
            try {
                const response = await fetch('/install', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const responseText = await response.text();
                
                resultDiv.innerHTML = `
                    <div class="status info">
                        <strong>📊 Resposta do Servidor:</strong><br>
                        Status: ${response.status}<br>
                        Content-Type: ${response.headers.get('content-type')}<br>
                    </div>
                    <div class="code">${responseText}</div>
                `;
                
                // Tentar parsear como JSON
                try {
                    const data = JSON.parse(responseText);
                    resultDiv.innerHTML += `
                        <div class="status ${data.success ? 'success' : 'error'}">
                            <strong>${data.success ? '✅' : '❌'} Resultado:</strong><br>
                            ${data.message || 'Sem mensagem'}
                        </div>
                    `;
                } catch (e) {
                    resultDiv.innerHTML += `
                        <div class="status warning">
                            <strong>⚠️ Resposta não é JSON válido</strong><br>
                            Possível erro de servidor ou redirecionamento
                        </div>
                    `;
                }
                
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="status error">
                        <strong>❌ Erro na Requisição:</strong><br>
                        ${error.message}
                    </div>
                `;
            }
        }
        </script>

        <div class="status info">
            <strong>🔗 Links Úteis</strong><br>
            <a href="/install" class="btn">🔧 Instalador Normal</a>
            <a href="/debug-error.php" class="btn">🐛 Debug Completo</a>
            <a href="/check-install-error.php" class="btn">🔍 Verificar Erro</a>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>HomeMechanic v1.0.0</strong> - Teste CSRF e Instalação</p>
            <p style="color: #666; font-size: 0.9rem;">Executado em <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>