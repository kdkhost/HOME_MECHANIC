<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host   = '15.235.57.3';
$user   = 'homemechanic_2026';
$pass   = 'homemechanic_2026';
$dbname = 'homemechanic_2026';

try {
    $pdo = new PDO(
        "mysql:host={$host};port=3306;dbname={$dbname};charset=utf8mb4",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 15]
    );

    echo "=== TABELAS ===\n";
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) echo "  - $t\n";

    echo "\n=== COLUNAS: users ===\n";
    $cols = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) echo "  {$c['Field']} | {$c['Type']} | null:{$c['Null']} | default:{$c['Default']}\n";

    echo "\n=== DADOS: users ===\n";
    $rows = $pdo->query("SELECT id, name, email, role, avatar, phone, bio, created_at FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "  id:{$r['id']} | {$r['name']} | avatar:" . ($r['avatar'] ?: 'NULL') . "\n";
    }

    echo "\n=== MIGRATIONS PENDENTES ===\n";
    $ran = $pdo->query("SELECT migration FROM migrations ORDER BY batch, migration")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($ran as $m) echo "  [OK] $m\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
