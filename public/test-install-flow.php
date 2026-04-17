<?php
/**
 * Script de teste para verificar o fluxo de dados do instalador
 * Simula o que acontece quando os dados são enviados
 */

header('Content-Type: application/json');

// Simular dados enviados pelo JavaScript
$simulatedPost = [
    'db_host' => 'localhost',
    'db_port' => '3306',
    'db_name' => 'homemechanic_test',
    'db_user' => 'root',
    'db_password' => 'senha123',
    'admin_name' => 'Administrador',
    'admin_email' => 'admin@homemechanic.com.br',
    'admin_password' => 'senha12345',
    'admin_password_confirmation' => 'senha12345',
    'company_name' => 'HomeMechanic Teste',
    'company_description' => 'Sistema de teste',
    'system_url' => 'https://homemechanic.com.br',
    'terms_accepted' => '1'
];

// Simular o que o Controller faz
$dbConfig = [
    'host' => $simulatedPost['db_host'],
    'port' => $simulatedPost['db_port'],
    'database' => $simulatedPost['db_name'], // Para testDatabaseConnection
    'username' => $simulatedPost['db_user'],
    'password' => $simulatedPost['db_password']
];

$installData = [
    'database' => [
        'host' => $simulatedPost['db_host'],
        'port' => $simulatedPost['db_port'],
        'name' => $simulatedPost['db_name'], // Para o Service
        'username' => $simulatedPost['db_user'],
        'password' => $simulatedPost['db_password']
    ],
    'admin' => [
        'name' => $simulatedPost['admin_name'],
        'email' => $simulatedPost['admin_email'],
        'password' => $simulatedPost['admin_password']
    ],
    'company' => [
        'name' => $simulatedPost['company_name'],
        'description' => $simulatedPost['company_description']
    ],
    'system' => [
        'url' => $simulatedPost['system_url']
    ]
];

// Verificar se os dados estão corretos
$checks = [
    'db_config_has_database_key' => isset($dbConfig['database']),
    'db_config_database_value' => $dbConfig['database'] ?? 'VAZIO',
    'install_data_has_name_key' => isset($installData['database']['name']),
    'install_data_name_value' => $installData['database']['name'] ?? 'VAZIO',
    'values_match' => ($dbConfig['database'] ?? '') === ($installData['database']['name'] ?? '')
];

// Simular o que o Service faz
$serviceData = $installData;

// Simular prepareInstallationData
$serviceData['database'] = array_merge([
    'host' => '127.0.0.1',
    'port' => 3306,
    'name' => '',
    'username' => '',
    'password' => ''
], $serviceData['database'] ?? []);

// Verificar se o nome do banco ainda está presente
$finalChecks = [
    'after_prepare_has_name' => isset($serviceData['database']['name']),
    'after_prepare_name_value' => $serviceData['database']['name'] ?? 'VAZIO',
    'after_prepare_name_not_empty' => !empty($serviceData['database']['name'])
];

// Simular o que seria usado no USE statement
$dbNameForUse = $serviceData['database']['name'];
$useStatement = "USE `{$dbNameForUse}`";

$response = [
    'success' => !empty($dbNameForUse),
    'message' => !empty($dbNameForUse) ? 'Fluxo de dados correto!' : 'ERRO: Nome do banco vazio!',
    'simulated_post' => array_merge($simulatedPost, ['db_password' => '***OCULTO***', 'admin_password' => '***OCULTO***']),
    'db_config_for_test' => array_merge($dbConfig, ['password' => '***OCULTO***']),
    'install_data_for_service' => [
        'database' => array_merge($installData['database'], ['password' => '***OCULTO***']),
        'admin' => array_merge($installData['admin'], ['password' => '***OCULTO***']),
        'company' => $installData['company'],
        'system' => $installData['system']
    ],
    'checks' => $checks,
    'after_prepare' => [
        'database' => array_merge($serviceData['database'], ['password' => '***OCULTO***'])
    ],
    'final_checks' => $finalChecks,
    'use_statement' => $useStatement,
    'use_statement_valid' => !empty($dbNameForUse) && $useStatement !== 'USE ``'
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
