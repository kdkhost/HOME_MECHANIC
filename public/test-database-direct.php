<?php
/**
 * Teste Direto de Conexão com Banco de Dados
 * Sem depender de rotas do Laravel
 */

header('Content-Type: application/json');

// Receber dados via POST
$host = $_POST['db_host'] ?? '127.0.0.1';
$port = $_POST['db_port'] ?? 3306;
$database = $_POST['db_name'] ?? '';
$username = $_POST['db_user'] ?? '';
$password = $_POST['db_password'] ?? '';

// Validar dados obrigatórios
if (empty($database) || empty($username)) {
    echo json_encode([
        'success' => false,
        'message' => 'Preencha o nome do banco e usuário'
    ]);
    exit;
}

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$database}";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 10
    ]);
    
    // Testar query simples
    $pdo->query('SELECT 1');
    
    // Obter versão do MySQL
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexão estabelecida com sucesso!',
        'version' => $version,
        'host' => $host,
        'database' => $database
    ]);
    
} catch (PDOException $e) {
    $errorMessage = $e->getMessage();
    
    // Mensagens de erro mais amigáveis
    if (strpos($errorMessage, 'Access denied') !== false) {
        $message = 'Acesso negado. Verifique usuário e senha.';
    } elseif (strpos($errorMessage, 'Unknown database') !== false) {
        $message = 'Banco de dados não existe. Crie o banco primeiro.';
    } elseif (strpos($errorMessage, "Can't connect") !== false) {
        $message = 'Não foi possível conectar ao servidor MySQL. Verifique host e porta.';
    } else {
        $message = 'Erro na conexão: ' . $errorMessage;
    }
    
    echo json_encode([
        'success' => false,
        'message' => $message,
        'error_code' => $e->getCode()
    ]);
}
