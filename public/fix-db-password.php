<?php
/**
 * Script para corrigir senha do banco de dados no .env
 * Use este script se você não consegue fazer login devido a erro de conexão com banco
 */

// Verificar se é requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $dbHost = $_POST['db_host'] ?? '';
    $dbPort = $_POST['db_port'] ?? '3306';
    $dbName = $_POST['db_name'] ?? '';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_password'] ?? '';
    
    // Validar campos
    if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
        echo json_encode([
            'success' => false,
            'message' => 'Preencha todos os campos obrigatórios'
        ]);
        exit;
    }
    
    // Testar conexão
    try {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Conexão OK - atualizar .env
        $envPath = __DIR__ . '/../.env';
        
        if (!file_exists($envPath)) {
            echo json_encode([
                'success' => false,
                'message' => 'Arquivo .env não encontrado'
            ]);
            exit;
        }
        
        $envContent = file_get_contents($envPath);
        
        // Atualizar valores
        $envContent = preg_replace('/^DB_HOST=.*/m', "DB_HOST={$dbHost}", $envContent);
        $envContent = preg_replace('/^DB_PORT=.*/m', "DB_PORT={$dbPort}", $envContent);
        $envContent = preg_replace('/^DB_DATABASE=.*/m', "DB_DATABASE={$dbName}", $envContent);
        $envContent = preg_replace('/^DB_USERNAME=.*/m', "DB_USERNAME={$dbUser}", $envContent);
        $envContent = preg_replace('/^DB_PASSWORD=.*/m', "DB_PASSWORD={$dbPass}", $envContent);
        
        // Salvar
        if (file_put_contents($envPath, $envContent)) {
            // Limpar caches
            $cachePaths = [
                __DIR__ . '/../bootstrap/cache/config.php',
                __DIR__ . '/../storage/framework/cache/data'
            ];
            
            foreach ($cachePaths as $path) {
                if (file_exists($path)) {
                    if (is_file($path)) {
                        @unlink($path);
                    } elseif (is_dir($path)) {
                        array_map('unlink', glob("{$path}/*"));
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Configurações atualizadas com sucesso! Você pode fazer login agora.',
                'redirect' => '/admin/login'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao salvar arquivo .env. Verifique as permissões.'
            ]);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao conectar ao banco: ' . $e->getMessage()
        ]);
    }
    
    exit;
}

// Ler configurações atuais do .env
$envPath = __DIR__ . '/../.env';
$currentConfig = [
    'host' => '',
    'port' => '3306',
    'database' => '',
    'username' => '',
    'password' => ''
];

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    preg_match('/DB_HOST=(.*)/', $envContent, $hostMatch);
    preg_match('/DB_PORT=(.*)/', $envContent, $portMatch);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $dbMatch);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $userMatch);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
    
    $currentConfig['host'] = trim($hostMatch[1] ?? '');
    $currentConfig['port'] = trim($portMatch[1] ?? '3306');
    $currentConfig['database'] = trim($dbMatch[1] ?? '');
    $currentConfig['username'] = trim($userMatch[1] ?? '');
    $currentConfig['password'] = trim($passMatch[1] ?? '');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corrigir Senha do Banco - HomeMechanic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .fix-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #FF6B00;
            font-weight: bold;
            margin: 0;
        }
        .alert-info {
            background: #e3f2fd;
            border-color: #2196f3;
            color: #1565c0;
        }
    </style>
</head>
<body>
    <div class="fix-container">
        <div class="logo">
            <h1><i class="bi bi-tools"></i> HomeMechanic</h1>
            <p class="text-muted">Corrigir Configuração do Banco de Dados</p>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Erro de Conexão Detectado</strong><br>
            O sistema não conseguiu conectar ao banco de dados. Verifique e corrija as credenciais abaixo.
        </div>

        <form id="fixForm">
            <div class="mb-3">
                <label class="form-label">Host do Banco *</label>
                <input type="text" class="form-control" name="db_host" value="<?= htmlspecialchars($currentConfig['host']) ?>" required>
                <small class="text-muted">Geralmente: localhost ou 127.0.0.1</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Porta</label>
                <input type="number" class="form-control" name="db_port" value="<?= htmlspecialchars($currentConfig['port']) ?>">
                <small class="text-muted">Padrão: 3306</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Nome do Banco *</label>
                <input type="text" class="form-control" name="db_name" value="<?= htmlspecialchars($currentConfig['database']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Usuário *</label>
                <input type="text" class="form-control" name="db_user" value="<?= htmlspecialchars($currentConfig['username']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" class="form-control" name="db_password" value="<?= htmlspecialchars($currentConfig['password']) ?>">
                <small class="text-muted">Deixe em branco se não houver senha</small>
            </div>

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary" onclick="testConnection()">
                    <i class="bi bi-plug me-2"></i>Testar Conexão
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Salvar e Aplicar
                </button>
                <a href="/admin/login" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Voltar para Login
                </a>
            </div>
        </form>

        <div id="result" class="mt-3"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function testConnection() {
            const form = document.getElementById('fixForm');
            const formData = new FormData(form);
            const btn = event.target;
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Testando...';
            
            try {
                const response = await fetch('/fix-db-password.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                const resultDiv = document.getElementById('result');
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>${data.message}
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle me-2"></i>${data.message}
                        </div>
                    `;
                }
            } catch (error) {
                document.getElementById('result').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle me-2"></i>Erro: ${error.message}
                    </div>
                `;
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        document.getElementById('fixForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
            
            try {
                const response = await fetch('/fix-db-password.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                const resultDiv = document.getElementById('result');
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>${data.message}
                        </div>
                    `;
                    
                    // Redirecionar após 2 segundos
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle me-2"></i>${data.message}
                        </div>
                    `;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                document.getElementById('result').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle me-2"></i>Erro: ${error.message}
                    </div>
                `;
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    </script>
</body>
</html>
