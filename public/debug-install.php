<?php
/**
 * Script de debug para verificar dados enviados pelo instalador
 * Este arquivo será removido após a instalação
 */

header('Content-Type: application/json');

// Coletar todos os dados enviados
$postData = $_POST;
$getData = $_GET;
$rawInput = file_get_contents('php://input');

// Campos obrigatórios esperados
$requiredFields = [
    'db_host',
    'db_port',
    'db_name',
    'db_user',
    'admin_name',
    'admin_email',
    'admin_password',
    'admin_password_confirmation',
    'company_name',
    'terms_accepted'
];

// Verificar quais campos estão presentes
$presentFields = [];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (isset($postData[$field]) && !empty($postData[$field])) {
        $presentFields[$field] = $field === 'admin_password' || $field === 'admin_password_confirmation' || $field === 'db_password' 
            ? '***OCULTO***' 
            : $postData[$field];
    } else {
        $missingFields[] = $field;
    }
}

// Resposta
$response = [
    'success' => empty($missingFields),
    'message' => empty($missingFields) ? 'Todos os campos obrigatórios presentes' : 'Campos obrigatórios faltando',
    'present_fields' => $presentFields,
    'missing_fields' => $missingFields,
    'total_post_fields' => count($postData),
    'total_get_fields' => count($getData),
    'raw_input_length' => strlen($rawInput),
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'UNKNOWN'
];

echo json_encode($response, JSON_PRETTY_PRINT);
