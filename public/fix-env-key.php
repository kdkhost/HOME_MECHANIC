<?php
/**
 * Script de Emergência - Corrigir APP_KEY no .env
 * 
 * Este script força a atualização do .env com uma APP_KEY válida
 * Use quando o erro "No application encryption key has been specified" aparecer
 */

$basePath = dirname(__DIR__);
$envPath = $basePath . '/.env';
$envInstallerPath = $basePath . '/.env.installer';

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Corrigir APP_KEY - HomeMechanic</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #FF6B00, #E55A00);
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 {
            color: #FF6B00;
            border-bottom: 3px solid #FF6B00;
            padding-bottom: 10px;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 5px solid;
        }
        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .alert-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .alert-info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #FF6B00;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #E55A00;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        code {
            background: #f8f9fa;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
        .step {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #FF6B00;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Corrigir APP_KEY do .env</h1>
";

// Verificar se foi solicitada a correção
if (isset($_GET['action']) && $_GET['action'] === 'fix') {
    echo "<h2>Executando Correção...</h2>";
    
    try {
        // Passo 1: Verificar se .env.installer existe
        if (!file_exists($envInstallerPath)) {
            throw new Exception("Arquivo .env.installer não encontrado!");
        }
        
        echo "<div class='step'>
            <strong>✅ Passo 1:</strong> Arquivo .env.installer encontrado
        </div>";
        
        // Passo 2: Fazer backup do .env atual (se existir)
        if (file_exists($envPath)) {
            $backupPath = $envPath . '.backup.' . date('YmdHis');
            copy($envPath, $backupPath);
            echo "<div class='step'>
                <strong>✅ Passo 2:</strong> Backup criado: <code>" . basename($backupPath) . "</code>
            </div>";
        } else {
            echo "<div class='step'>
                <strong>ℹ️ Passo 2:</strong> Arquivo .env não existe (será criado)
            </div>";
        }
        
        // Passo 3: Copiar .env.installer para .env
        if (copy($envInstallerPath, $envPath)) {
            echo "<div class='step'>
                <strong>✅ Passo 3:</strong> Arquivo .env criado/atualizado com sucesso
            </div>";
        } else {
            throw new Exception("Falha ao copiar .env.installer para .env");
        }
        
        // Passo 4: Verificar se APP_KEY está presente
        $envContent = file_get_contents($envPath);
        if (strpos($envContent, 'APP_KEY=base64:') !== false) {
            echo "<div class='step'>
                <strong>✅ Passo 4:</strong> APP_KEY válida encontrada no .env
            </div>";
        } else {
            throw new Exception("APP_KEY não encontrada no .env após cópia");
        }
        
        // Passo 5: Limpar caches do Laravel (se possível)
        $cachePaths = [
            $basePath . '/bootstrap/cache/config.php',
            $basePath . '/bootstrap/cache/routes-v7.php',
            $basePath . '/bootstrap/cache/services.php',
        ];
        
        $cacheCleared = false;
        foreach ($cachePaths as $cachePath) {
            if (file_exists($cachePath)) {
                unlink($cachePath);
                $cacheCleared = true;
            }
        }
        
        if ($cacheCleared) {
            echo "<div class='step'>
                <strong>✅ Passo 5:</strong> Caches do Laravel limpos
            </div>";
        } else {
            echo "<div class='step'>
                <strong>ℹ️ Passo 5:</strong> Nenhum cache para limpar
            </div>";
        }
        
        // Sucesso!
        echo "<div class='alert alert-success'>
            <h3>🎉 Correção Concluída com Sucesso!</h3>
            <p>O arquivo <code>.env</code> foi atualizado com uma <code>APP_KEY</code> válida.</p>
            <p><strong>Próximos passos:</strong></p>
            <ol>
                <li>Acesse a <a href='/' style='color: #FF6B00; font-weight: bold;'>página inicial</a></li>
                <li>Você será redirecionado para o instalador</li>
                <li>Siga os 5 steps para completar a instalação</li>
            </ol>
        </div>";
        
        echo "<div style='text-align: center; margin-top: 30px;'>
            <a href='/' class='btn'>Ir para Página Inicial</a>
            <a href='/install' class='btn'>Ir para Instalador</a>
        </div>";
        
    } catch (Exception $e) {
        echo "<div class='alert alert-error'>
            <h3>❌ Erro na Correção</h3>
            <p><strong>Mensagem:</strong> {$e->getMessage()}</p>
        </div>";
        
        echo "<div style='text-align: center; margin-top: 20px;'>
            <a href='?' class='btn btn-secondary'>Voltar</a>
        </div>";
    }
    
} else {
    // Mostrar informações e botão para executar correção
    echo "<div class='alert alert-warning'>
        <h3>⚠️ Erro Detectado</h3>
        <p>O sistema está apresentando o erro:</p>
        <pre>No application encryption key has been specified.</pre>
        <p>Este script irá corrigir o problema automaticamente.</p>
    </div>";
    
    echo "<h2>O que será feito?</h2>";
    
    echo "<div class='step'>
        <strong>1.</strong> Verificar se o arquivo <code>.env.installer</code> existe
    </div>";
    
    echo "<div class='step'>
        <strong>2.</strong> Fazer backup do arquivo <code>.env</code> atual (se existir)
    </div>";
    
    echo "<div class='step'>
        <strong>3.</strong> Copiar <code>.env.installer</code> para <code>.env</code>
    </div>";
    
    echo "<div class='step'>
        <strong>4.</strong> Verificar se <code>APP_KEY</code> está presente
    </div>";
    
    echo "<div class='step'>
        <strong>5.</strong> Limpar caches do Laravel
    </div>";
    
    // Verificar status atual
    echo "<h2>Status Atual</h2>";
    
    if (file_exists($envInstallerPath)) {
        echo "<div class='alert alert-success'>
            ✅ Arquivo <code>.env.installer</code> existe
        </div>";
    } else {
        echo "<div class='alert alert-error'>
            ❌ Arquivo <code>.env.installer</code> NÃO existe<br>
            <small>Você precisa fazer pull do repositório Git para obter este arquivo.</small>
        </div>";
    }
    
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        if (strpos($envContent, 'APP_KEY=base64:') !== false && strlen(trim(str_replace('APP_KEY=', '', strstr($envContent, 'APP_KEY=', true) ?: ''))) > 10) {
            echo "<div class='alert alert-success'>
                ✅ Arquivo <code>.env</code> existe e tem APP_KEY
            </div>";
        } else {
            echo "<div class='alert alert-warning'>
                ⚠️ Arquivo <code>.env</code> existe mas APP_KEY está vazia ou inválida
            </div>";
        }
    } else {
        echo "<div class='alert alert-info'>
            ℹ️ Arquivo <code>.env</code> não existe (será criado)
        </div>";
    }
    
    // Botão para executar correção
    echo "<div style='text-align: center; margin-top: 30px;'>";
    
    if (file_exists($envInstallerPath)) {
        echo "<a href='?action=fix' class='btn'>🔧 Executar Correção Agora</a>";
    } else {
        echo "<div class='alert alert-error'>
            <strong>Não é possível executar a correção!</strong><br>
            O arquivo <code>.env.installer</code> não foi encontrado.<br><br>
            <strong>Solução:</strong><br>
            Execute no terminal: <code>git pull origin master</code>
        </div>";
    }
    
    echo "<a href='/test-env-auto.php' class='btn btn-secondary'>Ver Diagnóstico Completo</a>";
    echo "</div>";
}

echo "
        <div style='margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center;'>
            <strong>🔧 HomeMechanic System</strong><br>
            <small>Script de Correção de APP_KEY</small>
        </div>
    </div>
</body>
</html>";
