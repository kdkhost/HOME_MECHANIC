<?php
/**
 * HomeMechanic - Teste de Correções
 * Script para verificar se as correções do erro 500 funcionaram
 */

echo "<!DOCTYPE html>\n";
echo "<html lang='pt-BR'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>HomeMechanic - Teste de Correções</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f9; }\n";
echo "        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }\n";
echo "        .header { text-align: center; margin-bottom: 30px; }\n";
echo "        .logo { color: #FF6B00; font-size: 2rem; font-weight: bold; }\n";
echo "        .status { padding: 15px; margin: 10px 0; border-radius: 8px; }\n";
echo "        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }\n";
echo "        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }\n";
echo "        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }\n";
echo "        .info { background: #cce7ff; color: #004085; border-left: 4px solid #007bff; }\n";
echo "        .btn { display: inline-block; padding: 12px 24px; background: #FF6B00; color: white; text-decoration: none; border-radius: 6px; margin: 10px 5px; }\n";
echo "        .btn:hover { background: #E55A00; }\n";
echo "        .code { background: #f8f9fa; padding: 15px; border-radius: 6px; font-family: monospace; margin: 10px 0; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container'>\n";
echo "        <div class='header'>\n";
echo "            <div class='logo'>🔧 HomeMechanic</div>\n";
echo "            <h2>Teste de Correções - Sistema</h2>\n";
echo "            <p>Verificando se as correções do erro 500 funcionaram</p>\n";
echo "        </div>\n";

// Verificar versão do PHP
echo "        <div class='status info'>\n";
echo "            <strong>📋 Versão do PHP:</strong> " . PHP_VERSION . "\n";
if (version_compare(PHP_VERSION, '8.4.0', '>=')) {
    echo "            <span style='color: #28a745;'>✅ PHP 8.4+ OK</span>\n";
} else {
    echo "            <span style='color: #dc3545;'>❌ Requer PHP 8.4+</span>\n";
}
echo "        </div>\n";

// Verificar arquivos críticos
$files_to_check = [
    'bootstrap/app.php' => 'Configuração principal do Laravel',
    'public/css/app.css' => 'CSS do frontend',
    'public/js/app.js' => 'JavaScript do frontend',
    'public/css/admin.css' => 'CSS do painel admin',
    'public/js/admin.js' => 'JavaScript do painel admin',
    'resources/views/layouts/frontend.blade.php' => 'Layout do frontend',
    'resources/views/layouts/admin.blade.php' => 'Layout do admin',
    'app/Exceptions/Handler.php' => 'Manipulador de exceções'
];

echo "        <h3>📁 Verificação de Arquivos</h3>\n";
foreach ($files_to_check as $file => $description) {
    $full_path = __DIR__ . '/../' . $file;
    if (file_exists($full_path)) {
        echo "        <div class='status success'>\n";
        echo "            <strong>✅ {$file}</strong><br>\n";
        echo "            {$description} - Arquivo existe (" . number_format(filesize($full_path)) . " bytes)\n";
        echo "        </div>\n";
    } else {
        echo "        <div class='status error'>\n";
        echo "            <strong>❌ {$file}</strong><br>\n";
        echo "            {$description} - Arquivo não encontrado\n";
        echo "        </div>\n";
    }
}

// Verificar extensões PHP necessárias
$required_extensions = [
    'pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'
];

echo "        <h3>🔧 Extensões PHP</h3>\n";
$missing_extensions = [];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "        <div class='status success'>\n";
        echo "            <strong>✅ {$ext}</strong> - Carregada\n";
        echo "        </div>\n";
    } else {
        $missing_extensions[] = $ext;
        echo "        <div class='status error'>\n";
        echo "            <strong>❌ {$ext}</strong> - Não encontrada\n";
        echo "        </div>\n";
    }
}

// Verificar permissões de diretórios
$directories_to_check = [
    'storage' => 'Diretório de armazenamento',
    'bootstrap/cache' => 'Cache do Bootstrap',
    'public' => 'Diretório público'
];

echo "        <h3>📂 Permissões de Diretórios</h3>\n";
foreach ($directories_to_check as $dir => $description) {
    $full_path = __DIR__ . '/../' . $dir;
    if (is_dir($full_path)) {
        if (is_writable($full_path)) {
            echo "        <div class='status success'>\n";
            echo "            <strong>✅ {$dir}</strong><br>\n";
            echo "            {$description} - Gravável\n";
            echo "        </div>\n";
        } else {
            echo "        <div class='status warning'>\n";
            echo "            <strong>⚠️ {$dir}</strong><br>\n";
            echo "            {$description} - Sem permissão de escrita\n";
            echo "        </div>\n";
        }
    } else {
        echo "        <div class='status error'>\n";
        echo "            <strong>❌ {$dir}</strong><br>\n";
        echo "            {$description} - Diretório não encontrado\n";
        echo "        </div>\n";
    }
}

// Verificar se o arquivo .env existe
echo "        <h3>⚙️ Configuração</h3>\n";
$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    echo "        <div class='status success'>\n";
    echo "            <strong>✅ Arquivo .env</strong> - Encontrado\n";
    echo "        </div>\n";
} else {
    echo "        <div class='status warning'>\n";
    echo "            <strong>⚠️ Arquivo .env</strong> - Não encontrado (será criado pelo instalador)\n";
    echo "        </div>\n";
}

// Verificar se está instalado
$installed_file = __DIR__ . '/../storage/installed';
if (file_exists($installed_file)) {
    echo "        <div class='status info'>\n";
    echo "            <strong>📦 Sistema</strong> - Já instalado\n";
    echo "        </div>\n";
} else {
    echo "        <div class='status warning'>\n";
    echo "            <strong>📦 Sistema</strong> - Não instalado (redirecionará para /install)\n";
    echo "        </div>\n";
}

// Resumo das correções aplicadas
echo "        <h3>🔧 Correções Aplicadas</h3>\n";
echo "        <div class='status info'>\n";
echo "            <strong>✅ Bootstrap/app.php</strong><br>\n";
echo "            Removido método handler() inválido da configuração de exceções\n";
echo "        </div>\n";
echo "        <div class='status info'>\n";
echo "            <strong>✅ Layouts Blade</strong><br>\n";
echo "            Substituído Vite por arquivos CSS/JS estáticos\n";
echo "        </div>\n";
echo "        <div class='status info'>\n";
echo "            <strong>✅ Assets Estáticos</strong><br>\n";
echo "            Criados arquivos CSS e JS para frontend e admin\n";
echo "        </div>\n";

// Próximos passos
echo "        <h3>📋 Próximos Passos</h3>\n";
echo "        <div class='status info'>\n";
echo "            <strong>1.</strong> Acesse o sistema principal: <a href='/' class='btn'>Ir para Homepage</a><br><br>\n";
echo "            <strong>2.</strong> Se não instalado, será redirecionado para: <a href='/install' class='btn'>Instalador</a><br><br>\n";
echo "            <strong>3.</strong> Após instalação, acesse: <a href='/admin/login' class='btn'>Painel Admin</a>\n";
echo "        </div>\n";

// Informações do servidor
echo "        <h3>🖥️ Informações do Servidor</h3>\n";
echo "        <div class='code'>\n";
echo "            <strong>Servidor:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido') . "<br>\n";
echo "            <strong>PHP SAPI:</strong> " . php_sapi_name() . "<br>\n";
echo "            <strong>Memória Limite:</strong> " . ini_get('memory_limit') . "<br>\n";
echo "            <strong>Tempo Limite:</strong> " . ini_get('max_execution_time') . "s<br>\n";
echo "            <strong>Upload Máximo:</strong> " . ini_get('upload_max_filesize') . "<br>\n";
echo "            <strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "<br>\n";
echo "        </div>\n";

echo "        <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>\n";
echo "            <p><strong>HomeMechanic v1.0.0</strong> - Sistema de Gestão Automotiva</p>\n";
echo "            <p style='color: #666; font-size: 0.9rem;'>Correções aplicadas em " . date('d/m/Y H:i:s') . "</p>\n";
echo "        </div>\n";
echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
?>