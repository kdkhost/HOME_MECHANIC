<?php
/**
 * Script para corrigir permissões e atualizar o .env
 * - Atualiza o .env para usar IP externo do banco
 * - Define permissões corretas: Superadmin (100), Admin (50), Usuário (10)
 * - Garante hierarquia rigorosa
 */

// 1. Atualizar o .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Trocar localhost por IP externo
    $envContent = preg_replace('/DB_HOST=localhost/', 'DB_HOST=15.235.57.3', $envContent);
    $envContent = preg_replace('/DB_HOST=127\.0\.0\.1/', 'DB_HOST=15.235.57.3', $envContent);
    
    file_put_contents($envFile, $envContent);
    echo "✅ .env atualizado (DB_HOST=15.235.57.3)\n";
}

// 2. Conectar ao banco
require __DIR__ . '/vendor/autoload.php';

// Recarregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'] . ";charset=utf8mb4",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao banco de dados\n";
    
    // 3. Verificar se a coluna permission_level existe
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'permission_level'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN permission_level INT DEFAULT 10");
        echo "✅ Coluna permission_level criada\n";
    }
    
    // 4. Atualizar permissões baseadas nos papéis atuais
    // Superadmin (role=superadmin ou email específico) -> level 100
    $pdo->exec("UPDATE users SET permission_level = 100 WHERE role = 'superadmin' OR permission_level = 100");
    echo "✅ Superadmins atualizados (nível 100)\n";
    
    // Admin (role=admin) -> level 50
    $pdo->exec("UPDATE users SET permission_level = 50 WHERE role = 'admin' AND (permission_level IS NULL OR permission_level < 50)");
    echo "✅ Admins atualizados (nível 50)\n";
    
    // Usuário comum (role=user ou permission_level não definido) -> level 10
    $pdo->exec("UPDATE users SET permission_level = 10 WHERE role = 'user' OR permission_level IS NULL OR permission_level = 0");
    echo "✅ Usuários atualizados (nível 10)\n";
    
    // 5. Garantir que o Marcelo seja Superadmin
    $stmt = $pdo->prepare("UPDATE users SET role = 'superadmin', permission_level = 100 WHERE email LIKE '%marcelo%' OR name LIKE '%Marcelo%'");
    $stmt->execute();
    echo "✅ Marcelo definido como Superadmin (nível 100)\n";
    
    // 6. Listar usuários e seus níveis
    echo "\n📋 Lista de usuários e permissões:\n";
    echo str_repeat("-", 60) . "\n";
    $stmt = $pdo->query("SELECT id, name, email, role, permission_level FROM users ORDER BY permission_level DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $level = $row['permission_level'] ?? 10;
        $label = $level >= 100 ? 'SUPERADMIN' : ($level >= 50 ? 'ADMIN' : 'USUÁRIO');
        echo sprintf("ID: %d | %s | %s | %s (nivel %d)\n", 
            $row['id'], 
            str_pad($row['name'], 20), 
            str_pad($row['role'], 12),
            $label,
            $level
        );
    }
    echo str_repeat("-", 60) . "\n";
    
    echo "\n✅ Permissões atualizadas com sucesso!\n";
    
} catch (PDOException $e) {
    echo "❌ Erro ao conectar ao banco: " . $e->getMessage() . "\n";
    echo "Verifique se o .env tem as credenciais corretas.\n";
}
