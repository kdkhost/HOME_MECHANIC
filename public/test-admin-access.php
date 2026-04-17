<?php
/**
 * Script de teste para verificar acesso ao admin
 */

header('Content-Type: application/json');

// Verificar se o sistema está instalado
$installedFile = __DIR__ . '/../storage/installed';
$isInstalled = file_exists($installedFile);

// Verificar se .env existe
$envFile = __DIR__ . '/../.env';
$hasEnv = file_exists($envFile);

// Verificar se há usuários no banco
$hasUsers = false;
$userCount = 0;
$adminUsers = [];

if ($hasEnv && $isInstalled) {
    try {
        // Carregar .env
        $envContent = file_get_contents($envFile);
        preg_match('/DB_HOST=(.*)/', $envContent, $hostMatch);
        preg_match('/DB_PORT=(.*)/', $envContent, $portMatch);
        preg_match('/DB_DATABASE=(.*)/', $envContent, $dbMatch);
        preg_match('/DB_USERNAME=(.*)/', $envContent, $userMatch);
        preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
        
        $dbHost = trim($hostMatch[1] ?? 'localhost');
        $dbPort = trim($portMatch[1] ?? '3306');
        $dbName = trim($dbMatch[1] ?? '');
        $dbUser = trim($userMatch[1] ?? '');
        $dbPass = trim($passMatch[1] ?? '');
        
        if (!empty($dbName)) {
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Verificar se tabela users existe
            $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
            
            if (count($tables) > 0) {
                // Contar usuários
                $result = $pdo->query("SELECT COUNT(*) as total FROM users")->fetch();
                $userCount = $result['total'];
                $hasUsers = $userCount > 0;
                
                // Listar usuários admin (sem senhas)
                $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users WHERE role = 'admin' ORDER BY id ASC");
                $adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    } catch (Exception $e) {
        // Erro ao conectar
    }
}

// URLs importantes
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = "{$protocol}://{$host}";

$urls = [
    'home' => $baseUrl . '/',
    'install' => $baseUrl . '/install/steps',
    'admin_login' => $baseUrl . '/admin/login',
    'admin_dashboard' => $baseUrl . '/admin/dashboard'
];

// Verificar se consegue acessar admin/login
$adminLoginAccessible = false;
$adminLoginError = null;

try {
    $ch = curl_init($urls['admin_login']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode === 200) {
        $adminLoginAccessible = true;
    } else {
        $adminLoginError = "HTTP {$httpCode}";
    }
    
    curl_close($ch);
} catch (Exception $e) {
    $adminLoginError = $e->getMessage();
}

// Resposta
$response = [
    'success' => $isInstalled && $hasEnv && $hasUsers,
    'system_status' => [
        'installed' => $isInstalled,
        'has_env' => $hasEnv,
        'has_users' => $hasUsers,
        'user_count' => $userCount,
        'admin_login_accessible' => $adminLoginAccessible
    ],
    'admin_users' => $adminUsers,
    'urls' => $urls,
    'instructions' => []
];

// Instruções baseadas no status
if (!$isInstalled) {
    $response['instructions'][] = "❌ Sistema não instalado. Acesse: {$urls['install']}";
} else {
    $response['instructions'][] = "✅ Sistema instalado";
}

if (!$hasEnv) {
    $response['instructions'][] = "❌ Arquivo .env não encontrado";
} else {
    $response['instructions'][] = "✅ Arquivo .env existe";
}

if (!$hasUsers) {
    $response['instructions'][] = "❌ Nenhum usuário encontrado no banco de dados";
    $response['instructions'][] = "   Reinstale o sistema ou crie um usuário manualmente";
} else {
    $response['instructions'][] = "✅ {$userCount} usuário(s) encontrado(s)";
    
    if (count($adminUsers) > 0) {
        $response['instructions'][] = "✅ " . count($adminUsers) . " administrador(es) encontrado(s)";
        foreach ($adminUsers as $admin) {
            $response['instructions'][] = "   - {$admin['name']} ({$admin['email']})";
        }
    }
}

if (!$adminLoginAccessible) {
    $response['instructions'][] = "⚠️  Página de login não acessível: {$adminLoginError}";
    $response['instructions'][] = "   Verifique se o servidor web está configurado corretamente";
} else {
    $response['instructions'][] = "✅ Página de login acessível: {$urls['admin_login']}";
}

// Próximos passos
if ($response['success']) {
    $response['next_steps'] = [
        "1. Acesse: {$urls['admin_login']}",
        "2. Use as credenciais que você criou durante a instalação",
        "3. Se esqueceu a senha, use o script de reset (criar se necessário)"
    ];
} else {
    $response['next_steps'] = [
        "1. Verifique os problemas listados acima",
        "2. Se necessário, reinstale o sistema: {$urls['install']}",
        "3. Após instalação, acesse: {$urls['admin_login']}"
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
