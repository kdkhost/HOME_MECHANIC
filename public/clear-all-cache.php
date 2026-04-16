<?php
/**
 * Limpar Todos os Caches
 * Script para limpar caches do Laravel sem usar artisan
 */

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Limpar Caches - HomeMechanic</title>
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
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #FF6B00;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #E55A00;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧹 Limpar Todos os Caches</h1>
";

$basePath = dirname(__DIR__);
$cleared = [];
$errors = [];

// Lista de arquivos de cache para deletar
$cacheFiles = [
    'bootstrap/cache/config.php' => 'Cache de Configuração',
    'bootstrap/cache/routes-v7.php' => 'Cache de Rotas',
    'bootstrap/cache/services.php' => 'Cache de Serviços',
    'bootstrap/cache/packages.php' => 'Cache de Pacotes',
    'bootstrap/cache/compiled.php' => 'Cache Compilado',
];

echo "<h2>Limpando Caches...</h2>";

foreach ($cacheFiles as $file => $description) {
    $fullPath = $basePath . '/' . $file;
    
    if (file_exists($fullPath)) {
        if (@unlink($fullPath)) {
            $cleared[] = $description;
            echo "<div class='alert alert-success'>
                ✅ {$description} removido
            </div>";
        } else {
            $errors[] = $description;
            echo "<div class='alert alert-error'>
                ❌ Falha ao remover {$description}
            </div>";
        }
    } else {
        echo "<div class='alert alert-success'>
            ℹ️ {$description} não existe (já limpo)
        </div>";
    }
}

// Limpar diretórios de cache
$cacheDirs = [
    'storage/framework/cache/data' => 'Cache de Dados',
    'storage/framework/views' => 'Cache de Views',
];

foreach ($cacheDirs as $dir => $description) {
    $fullPath = $basePath . '/' . $dir;
    
    if (is_dir($fullPath)) {
        $files = glob($fullPath . '/*');
        $count = 0;
        
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                if (@unlink($file)) {
                    $count++;
                }
            }
        }
        
        if ($count > 0) {
            echo "<div class='alert alert-success'>
                ✅ {$description}: {$count} arquivos removidos
            </div>";
        } else {
            echo "<div class='alert alert-success'>
                ℹ️ {$description}: nenhum arquivo para remover
            </div>";
        }
    }
}

// Resumo
echo "<h2>📊 Resumo</h2>";

if (count($cleared) > 0) {
    echo "<div class='alert alert-success'>
        <strong>✅ Caches Limpos:</strong><br>
        " . implode('<br>', $cleared) . "
    </div>";
}

if (count($errors) > 0) {
    echo "<div class='alert alert-error'>
        <strong>❌ Erros:</strong><br>
        " . implode('<br>', $errors) . "
    </div>";
}

if (count($errors) === 0) {
    echo "<div class='alert alert-success'>
        <h3>🎉 Todos os Caches Foram Limpos!</h3>
        <p>Agora você pode:</p>
        <ol>
            <li>Acessar o instalador novamente</li>
            <li>Testar a conexão com o banco de dados</li>
            <li>Prosseguir com a instalação</li>
        </ol>
    </div>";
}

echo "
        <div style='text-align: center; margin-top: 30px;'>
            <a href='/install/steps' class='btn'>Ir para Instalador</a>
            <a href='/check-routes.php' class='btn'>Verificar Rotas</a>
        </div>
        
        <div style='margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center;'>
            <strong>🔧 HomeMechanic System</strong><br>
            <small>Limpeza de Caches</small>
        </div>
    </div>
</body>
</html>";
