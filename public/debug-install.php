<?php
/**
 * Debug de Instalação
 * Mostra exatamente o que está sendo enviado
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mostrar todos os dados recebidos
    $data = [
        'method' => $_SERVER['REQUEST_METHOD'],
        'post_data' => $_POST,
        'files' => $_FILES,
        'headers' => getallheaders(),
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'N/A',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A'
    ];
    
    // Verificar campos obrigatórios
    $required = [
        'db_host',
        'db_name', 
        'db_user',
        'admin_name',
        'admin_email',
        'admin_password',
        'admin_password_confirmation',
        'terms_accepted'
    ];
    
    $missing = [];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missing[] = $field;
        }
    }
    
    echo json_encode([
        'success' => count($missing) === 0,
        'message' => count($missing) === 0 ? 'Todos os campos presentes' : 'Campos faltando',
        'missing_fields' => $missing,
        'received_data' => $data,
        'post_count' => count($_POST),
        'fields_received' => array_keys($_POST)
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'error' => 'Use POST method',
        'method_used' => $_SERVER['REQUEST_METHOD']
    ]);
}
