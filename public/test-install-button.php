<?php
/**
 * HomeMechanic - Teste Específico do Botão de Instalação
 * Script para testar exatamente o que acontece quando clica no botão
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
    <title>HomeMechanic - Teste do Botão de Instalação</title>
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
        .btn { display: inline-block; padding: 12px 24px; background: #FF6B00; color: white; text-decoration: none; border: none; border-radius: 6px; margin: 10px 5px; cursor: pointer; }
        .btn:hover { background: #E55A00; }
        .test-form { border: 2px solid #FF6B00; padding: 20px; margin: 20px 0; border-radius: 8px; }
        input, select { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px; }
        label { display: block; margin: 10px 0 5px 0; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔧 HomeMechanic</div>
            <h2>Teste do Botão de Instalação</h2>
            <p>Diagnosticando por que o botão "Iniciar Instalação" não funciona</p>
        </div>

        <?php
        echo "<h3>🔍 Verificações Preliminares</h3>";
        
        // 1. Verificar se Laravel carrega
        echo "<h4>1. Carregamento do Laravel</h4>";
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            echo "<div class='status success'>✅ Laravel carregado</div>";
            
            // Verificar rotas
            echo "<h4>2. Verificação de Rotas</h4>";
            try {
                $installRoute = url('/install');
                echo "<div class='status success'>✅ Rota /install: {$installRoute}</div>";
            } catch (Exception $e) {
                echo "<div class='status error'>❌ Erro na rota /install: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='status error'>❌ Erro ao carregar Laravel: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        
        // 2. Verificar se o instalador está acessível
        echo "<h4>3. Teste de Acesso ao Instalador</h4>";
        
        $installUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/install';
        echo "<div class='status info'>📍 URL do Instalador: <a href='{$installUrl}' target='_blank'>{$installUrl}</a></div>";
        
        // 3. Verificar CSRF
        echo "<h4>4. Teste de CSRF Token</h4>";
        try {
            if (function_exists('csrf_token')) {
                $token = csrf_token();
                echo "<div class='status success'>✅ CSRF Token disponível: " . substr($token, 0, 20) . "...</div>";
            } else {
                echo "<div class='status warning'>⚠️ CSRF Token não disponível (normal em script standalone)</div>";
            }
        } catch (Exception $e) {
            echo "<div class='status error'>❌ Erro CSRF: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        
        // 4. Verificar JavaScript
        echo "<h4>5. Teste de JavaScript</h4>";
        ?>
        
        <div class="status info">
            <strong>🧪 Teste JavaScript:</strong><br>
            <button onclick="testJavaScript()" class="btn">Testar JavaScript</button>
            <div id="jsResult"></div>
        </div>

        <script>
        function testJavaScript() {
            const resultDiv = document.getElementById('jsResult');
            resultDiv.innerHTML = '<div style="color: green; margin-top: 10px;">✅ JavaScript está funcionando!</div>';
            
            // Testar fetch
            try {
                fetch('/install', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    resultDiv.innerHTML += `<div style="color: blue; margin-top: 5px;">📡 Fetch para /install: Status ${response.status}</div>`;
                    return response.text();
                })
                .then(data => {
                    if (data.includes('HomeMechanic') || data.includes('instalação')) {
                        resultDiv.innerHTML += '<div style="color: green; margin-top: 5px;">✅ Página do instalador acessível</div>';
                    } else {
                        resultDiv.innerHTML += '<div style="color: orange; margin-top: 5px;">⚠️ Resposta inesperada da página</div>';
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML += `<div style="color: red; margin-top: 5px;">❌ Erro no fetch: ${error.message}</div>`;
                });
            } catch (error) {
                resultDiv.innerHTML += `<div style="color: red; margin-top: 5px;">❌ Erro JavaScript: ${error.message}</div>`;
            }
        }
        </script>

        <?php
        // 5. Formulário de teste direto
        echo "<h3>🧪 Teste Direto do Formulário</h3>";
        echo "<p>Este formulário simula exatamente o que o botão de instalação deveria fazer:</p>";
        ?>

        <div class="test-form">
            <h4>📝 Formulário de Teste (Método POST)</h4>
            <form method="POST" action="/install" id="testForm">
                <?php if (function_exists('csrf_token')): ?>
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <?php endif; ?>
                
                <label>Host do Banco:</label>
                <input type="text" name="db_host" value="127.0.0.1" required>
                
                <label>Porta:</label>
                <input type="number" name="db_port" value="3306" required>
                
                <label>Nome do Banco:</label>
                <input type="text" name="db_name" placeholder="seu_banco_aqui" required>
                
                <label>Usuário do Banco:</label>
                <input type="text" name="db_user" placeholder="seu_usuario_aqui" required>
                
                <label>Senha do Banco:</label>
                <input type="password" name="db_password" placeholder="sua_senha_aqui">
                
                <label>Nome do Admin:</label>
                <input type="text" name="admin_name" value="Administrador" required>
                
                <label>Email do Admin:</label>
                <input type="email" name="admin_email" value="admin@homemechanic.com.br" required>
                
                <label>Senha do Admin:</label>
                <input type="password" name="admin_password" value="admin123456" required>
                
                <label>Confirmar Senha:</label>
                <input type="password" name="admin_password_confirmation" value="admin123456" required>
                
                <label>Nome da Empresa:</label>
                <input type="text" name="company_name" value="HomeMechanic">
                
                <label>URL do Sistema:</label>
                <input type="text" name="system_url" value="https://<?= $_SERVER['HTTP_HOST'] ?>">
                
                <br><br>
                <button type="submit" class="btn">🚀 Testar Instalação (POST Direto)</button>
            </form>
        </div>

        <?php
        // 6. Teste AJAX
        ?>
        
        <div class="test-form">
            <h4>📡 Teste AJAX (Como o JavaScript do instalador)</h4>
            <button onclick="testAjaxInstall()" class="btn">🧪 Testar via AJAX</button>
            <div id="ajaxResult" style="margin-top: 15px;"></div>
        </div>

        <script>
        async function testAjaxInstall() {
            const resultDiv = document.getElementById('ajaxResult');
            resultDiv.innerHTML = '<div class="status info">🔄 Testando instalação via AJAX...</div>';
            
            const formData = new FormData();
            
            // Adicionar CSRF token se disponível
            <?php if (function_exists('csrf_token')): ?>
                formData.append('_token', '<?= csrf_token() ?>');
            <?php endif; ?>
            
            // Dados de teste (SUBSTITUA PELOS SEUS DADOS REAIS)
            formData.append('db_host', '127.0.0.1');
            formData.append('db_port', '3306');
            formData.append('db_name', 'SEU_BANCO_AQUI');  // ⚠️ SUBSTITUA
            formData.append('db_user', 'SEU_USUARIO_AQUI'); // ⚠️ SUBSTITUA
            formData.append('db_password', 'SUA_SENHA_AQUI'); // ⚠️ SUBSTITUA
            formData.append('admin_name', 'Administrador');
            formData.append('admin_email', 'admin@homemechanic.com.br');
            formData.append('admin_password', 'admin123456');
            formData.append('admin_password_confirmation', 'admin123456');
            formData.append('company_name', 'HomeMechanic');
            formData.append('system_url', 'https://<?= $_SERVER['HTTP_HOST'] ?>');
            
            try {
                const response = await fetch('/install', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const contentType = response.headers.get('content-type');
                const responseText = await response.text();
                
                resultDiv.innerHTML = `
                    <div class="status info">
                        <strong>📊 Resposta do Servidor:</strong><br>
                        Status: ${response.status}<br>
                        Content-Type: ${contentType}<br>
                        Tamanho: ${responseText.length} caracteres
                    </div>
                `;
                
                // Tentar parsear como JSON
                try {
                    const data = JSON.parse(responseText);
                    resultDiv.innerHTML += `
                        <div class="status ${data.success ? 'success' : 'error'}">
                            <strong>${data.success ? '✅' : '❌'} Resultado JSON:</strong><br>
                            ${data.message || 'Sem mensagem'}<br>
                            ${data.details ? 'Detalhes: ' + JSON.stringify(data.details) : ''}
                        </div>
                    `;
                } catch (e) {
                    // Se não for JSON, mostrar o HTML/texto
                    resultDiv.innerHTML += `
                        <div class="status warning">
                            <strong>⚠️ Resposta não é JSON:</strong>
                        </div>
                        <div class="code">${responseText.substring(0, 1000)}${responseText.length > 1000 ? '...' : ''}</div>
                    `;
                }
                
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="status error">
                        <strong>❌ Erro na Requisição AJAX:</strong><br>
                        ${error.message}
                    </div>
                `;
            }
        }
        </script>

        <div class="status warning">
            <strong>⚠️ IMPORTANTE:</strong><br>
            Para o teste AJAX funcionar, você precisa editar este arquivo e colocar seus dados reais do banco nas linhas marcadas com "⚠️ SUBSTITUA".
        </div>

        <div class="status info">
            <strong>🔗 Links Úteis:</strong><br>
            <a href="/install" class="btn">🔧 Instalador Original</a>
            <a href="/simple-test.php" class="btn">🧪 Teste Simples</a>
            <a href="/debug-error.php" class="btn">🐛 Debug Completo</a>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>HomeMechanic v1.0.1</strong> - Teste do Botão de Instalação</p>
            <p style="color: #666; font-size: 0.9rem;">Executado em <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>