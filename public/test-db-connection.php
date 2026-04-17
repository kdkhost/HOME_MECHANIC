<?php
/**
 * Script para testar conexão com banco de dados
 * Acesse: /test-db-connection.php
 */

// Carregar configurações do Laravel
require_once __DIR__ . '/../vendor/autoload.php';

try {
    // Tentar carregar .env
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    } else {
        echo "❌ Arquivo .env não encontrado!\n";
        exit(1);
    }

    // Configurações do banco
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $database = $_ENV['DB_DATABASE'] ?? '';
    $username = $_ENV['DB_USERNAME'] ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? '';

    echo "🔍 Testando conexão com banco de dados...\n\n";
    echo "Host: {$host}:{$port}\n";
    echo "Database: {$database}\n";
    echo "Username: {$username}\n";
    echo "Password: " . (empty($password) ? '(vazio)' : '***') . "\n\n";

    // Testar conexão
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    
    if (empty($password)) {
        echo "⚠️  ATENÇÃO: Senha do banco está vazia!\n";
        echo "Tentando conectar sem senha...\n\n";
    }

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);

    echo "✅ Conexão com MySQL estabelecida!\n\n";

    // Verificar se o banco existe
    if (!empty($database)) {
        $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
        $stmt->execute([$database]);
        
        if ($stmt->fetch()) {
            echo "✅ Banco de dados '{$database}' existe!\n";
            
            // Conectar ao banco específico
            $pdo->exec("USE `{$database}`");
            
            // Verificar tabelas
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "📋 Tabelas encontradas: " . count($tables) . "\n";
            foreach ($tables as $table) {
                echo "  - {$table}\n";
            }
            
            // Verificar usuário admin
            if (in_array('users', $tables)) {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
                $adminCount = $stmt->fetch()['count'];
                echo "\n👤 Usuários admin: {$adminCount}\n";
                
                if ($adminCount > 0) {
                    $stmt = $pdo->query("SELECT name, email FROM users WHERE role = 'admin' LIMIT 1");
                    $admin = $stmt->fetch();
                    echo "   Admin: {$admin['name']} ({$admin['email']})\n";
                }
            }
            
        } else {
            echo "❌ Banco de dados '{$database}' NÃO existe!\n";
            echo "💡 Execute o instalador para criar o banco.\n";
        }
    }

} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n\n";
    
    if (str_contains($e->getMessage(), 'Access denied')) {
        echo "💡 Soluções possíveis:\n";
        echo "1. Verificar usuário e senha no .env\n";
        echo "2. Criar usuário no MySQL:\n";
        echo "   CREATE USER '{$username}'@'localhost' IDENTIFIED BY 'sua_senha';\n";
        echo "   GRANT ALL PRIVILEGES ON *.* TO '{$username}'@'localhost';\n";
        echo "   FLUSH PRIVILEGES;\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Teste concluído em " . date('d/m/Y H:i:s') . "\n";
?>