<?php
// Verificação específica para PHP 8.4.x - HomeMechanic System
echo "<h1>🔍 Verificação PHP 8.4.x - HomeMechanic</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";

// Verificar versão PHP
echo "<h2>📋 Versão PHP</h2>";
$php_version = PHP_VERSION;
$major = PHP_MAJOR_VERSION;
$minor = PHP_MINOR_VERSION;

echo "Versão atual: <strong>$php_version</strong><br>";

if ($major == 8 && $minor == 4) {
    echo '<span class="ok">✅ PHP 8.4.x - PERFEITO!</span><br>';
    $php_ok = true;
} elseif ($major == 8 && $minor < 4) {
    echo '<span class="error">❌ PHP 8.' . $minor . '.x - MUITO ANTIGO</span><br>';
    echo '<span class="error">➤ Necessário atualizar para PHP 8.4.x</span><br>';
    $php_ok = false;
} elseif ($major == 8 && $minor > 4) {
    echo '<span class="warning">⚠️ PHP 8.' . $minor . '.x - MUITO NOVO</span><br>';
    echo '<span class="warning">➤ Recomendado usar PHP 8.4.x para compatibilidade</span><br>';
    $php_ok = false;
} else {
    echo '<span class="error">❌ PHP ' . $major . '.x - INCOMPATÍVEL</span><br>';
    echo '<span class="error">➤ HomeMechanic requer PHP 8.4.x</span><br>';
    $php_ok = false;
}

// Verificar SAPI
echo "<br>SAPI: " . php_sapi_name() . "<br>";
echo "Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido') . "<br>";

// Verificar CloudLinux
echo "<h2>☁️ CloudLinux</h2>";
$cloudlinux_detected = false;

if (file_exists('/proc/lve/list')) {
    echo '<span class="ok">✅ CloudLinux detectado via /proc/lve/list</span><br>';
    $cloudlinux_detected = true;
}

if (file_exists('/usr/bin/cloudlinux-selector')) {
    echo '<span class="ok">✅ CloudLinux Selector disponível</span><br>';
    $cloudlinux_detected = true;
}

if (getenv('CLOUDLINUX_LVE_VERSION')) {
    echo '<span class="ok">✅ CloudLinux LVE ativo</span><br>';
    $cloudlinux_detected = true;
}

if (!$cloudlinux_detected) {
    echo '<span class="info">ℹ️ CloudLinux não detectado (pode ser normal)</span><br>';
}

// Verificar versões PHP disponíveis (CloudLinux)
echo "<h2>🔧 Versões PHP Disponíveis</h2>";
$php_binaries = [
    '/usr/bin/php84' => 'PHP 8.4 (Recomendado)',
    '/usr/bin/php83' => 'PHP 8.3',
    '/usr/bin/php82' => 'PHP 8.2',
    '/usr/bin/php81' => 'PHP 8.1',
    '/usr/bin/php80' => 'PHP 8.0',
    '/opt/alt/php84/usr/bin/php' => 'Alt-PHP 8.4 (CloudLinux)',
    '/opt/alt/php83/usr/bin/php' => 'Alt-PHP 8.3 (CloudLinux)',
];

$php84_available = false;
foreach ($php_binaries as $binary => $description) {
    if (file_exists($binary)) {
        if (strpos($binary, 'php84') !== false) {
            echo '<span class="ok">✅ ' . $description . ' - DISPONÍVEL</span><br>';
            $php84_available = true;
        } else {
            echo '<span class="info">ℹ️ ' . $description . ' - Disponível</span><br>';
        }
    }
}

if (!$php84_available) {
    echo '<span class="warning">⚠️ PHP 8.4 não encontrado nos caminhos padrão</span><br>';
}

// Verificar extensões obrigatórias
echo "<h2>🧩 Extensões PHP Obrigatórias</h2>";
$required_extensions = [
    'pdo' => 'PDO',
    'pdo_mysql' => 'PDO MySQL',
    'mbstring' => 'Multibyte String',
    'openssl' => 'OpenSSL',
    'tokenizer' => 'Tokenizer',
    'xml' => 'XML',
    'ctype' => 'Character Type',
    'json' => 'JSON',
    'bcmath' => 'BC Math',
    'fileinfo' => 'File Info',
    'gd' => 'GD (Imagens)'
];

$missing_extensions = [];
foreach ($required_extensions as $ext => $name) {
    if (extension_loaded($ext)) {
        echo '<span class="ok">✅ ' . $name . '</span><br>';
    } else {
        echo '<span class="error">❌ ' . $name . ' - FALTANDO</span><br>';
        $missing_extensions[] = $ext;
    }
}

// Configurações PHP importantes
echo "<h2>⚙️ Configurações PHP</h2>";
$php_settings = [
    'memory_limit' => ['Memória', '256M', 'Mínimo 256M'],
    'max_execution_time' => ['Tempo Execução', '300', 'Mínimo 300s'],
    'upload_max_filesize' => ['Upload Máximo', '100M', 'Mínimo 100M'],
    'post_max_size' => ['POST Máximo', '100M', 'Mínimo 100M'],
    'max_input_vars' => ['Input Vars', '3000', 'Mínimo 3000']
];

foreach ($php_settings as $setting => $info) {
    $current = ini_get($setting);
    echo "<strong>{$info[0]}:</strong> $current";
    
    if ($setting === 'memory_limit') {
        $current_bytes = convertToBytes($current);
        $required_bytes = convertToBytes($info[1]);
        if ($current_bytes >= $required_bytes) {
            echo ' <span class="ok">✅</span>';
        } else {
            echo ' <span class="warning">⚠️ ' . $info[2] . '</span>';
        }
    }
    echo "<br>";
}

// Instruções para correção
echo "<h2>🛠️ Instruções para Correção</h2>";

if (!$php_ok) {
    echo "<h3>1. Configurar PHP 8.4.x</h3>";
    echo "<p><strong>Via cPanel (Recomendado):</strong></p>";
    echo "<ol>";
    echo "<li>Acesse o cPanel da sua hospedagem</li>";
    echo "<li>Procure por 'CloudLinux PHP Selector' ou 'Select PHP Version'</li>";
    echo "<li>Selecione <strong>PHP 8.4.x</strong></li>";
    echo "<li>Ative todas as extensões necessárias (listadas acima)</li>";
    echo "<li>Clique em 'Save' ou 'Apply'</li>";
    echo "</ol>";
    
    echo "<p><strong>Via SSH (se disponível):</strong></p>";
    echo "<pre style='background:#f5f5f5;padding:10px;'>";
    echo "# Verificar versões disponíveis\n";
    echo "ls -la /usr/bin/php*\n";
    echo "ls -la /opt/alt/php*/usr/bin/php\n\n";
    echo "# Testar PHP 8.4\n";
    echo "/usr/bin/php84 -v\n";
    echo "# ou\n";
    echo "/opt/alt/php84/usr/bin/php -v\n";
    echo "</pre>";
}

if (count($missing_extensions) > 0) {
    echo "<h3>2. Instalar Extensões Faltando</h3>";
    echo "<p>Extensões faltando: <strong>" . implode(', ', $missing_extensions) . "</strong></p>";
    echo "<p>Via cPanel PHP Selector, ative essas extensões.</p>";
}

echo "<h3>3. Verificar Novamente</h3>";
echo "<p>Após fazer as alterações:</p>";
echo "<ol>";
echo "<li>Aguarde alguns minutos para as mudanças serem aplicadas</li>";
echo "<li>Recarregue esta página</li>";
echo "<li>Verifique se todos os itens estão com ✅</li>";
echo "</ol>";

// Status final
echo "<hr>";
echo "<h2>📊 Status Final</h2>";
if ($php_ok && count($missing_extensions) == 0) {
    echo '<div style="background:#d4edda;padding:15px;border-radius:5px;">';
    echo '<span class="ok">🎉 <strong>TUDO OK!</strong> PHP 8.4.x configurado corretamente.</span><br>';
    echo 'Você pode prosseguir com a instalação do HomeMechanic.';
    echo '</div>';
} else {
    echo '<div style="background:#f8d7da;padding:15px;border-radius:5px;">';
    echo '<span class="error">⚠️ <strong>CORREÇÕES NECESSÁRIAS</strong></span><br>';
    echo 'Siga as instruções acima antes de prosseguir.';
    echo '</div>';
}

echo "<p><em>Verificação executada em: " . date('Y-m-d H:i:s') . "</em></p>";

// Função auxiliar para converter tamanhos
function convertToBytes($size) {
    $size = trim($size);
    $last = strtolower($size[strlen($size)-1]);
    $size = (int) $size;
    
    switch($last) {
        case 'g': $size *= 1024;
        case 'm': $size *= 1024;
        case 'k': $size *= 1024;
    }
    
    return $size;
}
?>