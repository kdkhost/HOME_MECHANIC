<?php
/**
 * Teste de Criação Automática do .env
 * 
 * Este script testa se o sistema cria automaticamente o .env
 * quando ele não existe, permitindo que o instalador funcione
 */

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Teste de .env Automático</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #FF6B00;
            border-bottom: 3px solid #FF6B00;
            padding-bottom: 10px;
        }
        .test-section {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #dee2e6;
            border-radius: 5px;
        }
        .success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .info {
            border-left-color: #17a2b8;
            background: #d1ecf1;
        }
        code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .icon {
            font-size: 1.5em;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Teste de Criação Automática do .env</h1>
        <p>Este teste verifica se o sistema cria automaticamente o arquivo <code>.env</code> quando ele não existe.</p>
";

$basePath = dirname(__DIR__);
$envPath = $basePath . '/.env';
$envInstallerPath = $basePath . '/.env.installer';
$installedPath = $basePath . '/storage/installed';

echo "<div class='test-section info'>
    <strong><span class='icon'>📁</span>Caminhos dos Arquivos:</strong><br>
    <code>.env</code>: {$envPath}<br>
    <code>.env.installer</code>: {$envInstallerPath}<br>
    <code>storage/installed</code>: {$installedPath}
</div>";

// Teste 1: Verificar se .env.installer existe
echo "<h2>Teste 1: Arquivo .env.installer</h2>";
if (file_exists($envInstallerPath)) {
    echo "<div class='test-section success'>
        <strong><span class='icon'>✅</span>SUCESSO:</strong> Arquivo <code>.env.installer</code> existe!<br>
        <small>Tamanho: " . filesize($envInstallerPath) . " bytes</small>
    </div>";
} else {
    echo "<div class='test-section error'>
        <strong><span class='icon'>❌</span>ERRO:</strong> Arquivo <code>.env.installer</code> NÃO existe!<br>
        <small>O sistema não poderá criar o .env automaticamente.</small>
    </div>";
}

// Teste 2: Verificar se .env existe
echo "<h2>Teste 2: Arquivo .env</h2>";
if (file_exists($envPath)) {
    echo "<div class='test-section success'>
        <strong><span class='icon'>✅</span>EXISTE:</strong> Arquivo <code>.env</code> já existe!<br>
        <small>Tamanho: " . filesize($envPath) . " bytes</small><br>
        <small>Última modificação: " . date('d/m/Y H:i:s', filemtime($envPath)) . "</small>
    </div>";
    
    // Verificar se foi criado pelo .env.installer
    if (file_exists($envInstallerPath)) {
        $envContent = file_get_contents($envPath);
        $installerContent = file_get_contents($envInstallerPath);
        
        // Remover comentários do installer para comparação
        $installerContentClean = preg_replace('/^#.*$/m', '', $installerContent);
        $installerContentClean = trim(preg_replace('/\n\n+/', "\n", $installerContentClean));
        
        if (strpos($envContent, 'ARQUIVO TEMPORÁRIO PARA INSTALAÇÃO') !== false) {
            echo "<div class='test-section warning'>
                <strong><span class='icon'>⚠️</span>ATENÇÃO:</strong> O arquivo <code>.env</code> parece ser uma cópia do <code>.env.installer</code>.<br>
                <small>Isso é normal se o sistema ainda não foi instalado. O instalador criará o .env definitivo.</small>
            </div>";
        }
    }
} else {
    echo "<div class='test-section warning'>
        <strong><span class='icon'>⚠️</span>NÃO EXISTE:</strong> Arquivo <code>.env</code> não existe!<br>
        <small>O <code>public/index.php</code> deve criar automaticamente ao acessar o sistema.</small>
    </div>";
}

// Teste 3: Verificar se sistema está instalado
echo "<h2>Teste 3: Status da Instalação</h2>";
if (file_exists($installedPath)) {
    $installedContent = file_get_contents($installedPath);
    $installedData = json_decode($installedContent, true);
    
    echo "<div class='test-section success'>
        <strong><span class='icon'>✅</span>INSTALADO:</strong> Sistema já está instalado!<br>";
    
    if ($installedData && is_array($installedData)) {
        echo "<strong>Informações da Instalação:</strong><br>";
        echo "<pre>" . json_encode($installedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    }
    
    echo "</div>";
} else {
    echo "<div class='test-section info'>
        <strong><span class='icon'>ℹ️</span>NÃO INSTALADO:</strong> Sistema ainda não foi instalado.<br>
        <small>Acesse <a href='/install'>/install</a> para iniciar a instalação.</small>
    </div>";
}

// Teste 4: Simular criação automática do .env
echo "<h2>Teste 4: Simulação de Criação Automática</h2>";
echo "<div class='test-section info'>
    <strong><span class='icon'>🔧</span>Lógica do public/index.php:</strong><br>
    <pre style='background: #f8f9fa; color: #333; padding: 10px;'>
// Verificar se .env existe, se não, copiar do .env.installer
\$envPath = __DIR__.'/../.env';
\$envInstallerPath = __DIR__.'/../.env.installer';

if (!file_exists(\$envPath) && file_exists(\$envInstallerPath)) {
    // Copiar .env.installer para .env
    copy(\$envInstallerPath, \$envPath);
}</pre>
</div>";

if (!file_exists($envPath) && file_exists($envInstallerPath)) {
    echo "<div class='test-section success'>
        <strong><span class='icon'>✅</span>PRONTO PARA CRIAR:</strong> Quando você acessar o sistema pela primeira vez:<br>
        <ol>
            <li>O <code>public/index.php</code> detectará que o <code>.env</code> não existe</li>
            <li>Copiará automaticamente o <code>.env.installer</code> para <code>.env</code></li>
            <li>O Laravel carregará normalmente</li>
            <li>O middleware <code>CheckInstalled</code> redirecionará para <code>/install</code></li>
            <li>O instalador criará o <code>.env</code> definitivo com suas configurações</li>
        </ol>
    </div>";
} elseif (file_exists($envPath)) {
    echo "<div class='test-section success'>
        <strong><span class='icon'>✅</span>JÁ CRIADO:</strong> O arquivo <code>.env</code> já existe!<br>
        <small>O sistema está pronto para funcionar.</small>
    </div>";
} else {
    echo "<div class='test-section error'>
        <strong><span class='icon'>❌</span>ERRO:</strong> Não é possível criar o <code>.env</code> automaticamente!<br>
        <small>O arquivo <code>.env.installer</code> não existe.</small>
    </div>";
}

// Teste 5: Verificar permissões
echo "<h2>Teste 5: Permissões de Escrita</h2>";
$basePathWritable = is_writable($basePath);

if ($basePathWritable) {
    echo "<div class='test-section success'>
        <strong><span class='icon'>✅</span>PERMISSÕES OK:</strong> O diretório raiz tem permissão de escrita!<br>
        <small>O sistema poderá criar o arquivo <code>.env</code> automaticamente.</small>
    </div>";
} else {
    echo "<div class='test-section error'>
        <strong><span class='icon'>❌</span>ERRO DE PERMISSÃO:</strong> O diretório raiz NÃO tem permissão de escrita!<br>
        <small>Execute: <code>chmod 755 " . $basePath . "</code></small>
    </div>";
}

// Resumo Final
echo "<h2>📊 Resumo Final</h2>";
$allOk = file_exists($envInstallerPath) && $basePathWritable;

if ($allOk) {
    echo "<div class='test-section success'>
        <strong><span class='icon'>🎉</span>TUDO PRONTO!</strong><br>
        O sistema está configurado corretamente para criar o <code>.env</code> automaticamente.<br><br>
        <strong>Próximos Passos:</strong><br>
        <ol>
            <li>Acesse <a href='/' style='color: #FF6B00; font-weight: bold;'>a página inicial</a></li>
            <li>O sistema criará o <code>.env</code> automaticamente</li>
            <li>Você será redirecionado para <a href='/install' style='color: #FF6B00; font-weight: bold;'>/install</a></li>
            <li>Siga os 5 steps do instalador</li>
            <li>Pronto! Sistema instalado e funcionando! 🚀</li>
        </ol>
    </div>";
} else {
    echo "<div class='test-section error'>
        <strong><span class='icon'>⚠️</span>ATENÇÃO!</strong><br>
        Alguns problemas foram encontrados. Corrija-os antes de continuar.
    </div>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 5px; text-align: center;'>
            <strong>🔧 HomeMechanic System - Teste de Instalação</strong><br>
            <small>Desenvolvido para funcionar sem .env inicial</small>
        </div>
    </div>
</body>
</html>";
