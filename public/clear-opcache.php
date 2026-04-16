<?php
/**
 * Limpar OPcache
 * Script para limpar cache do PHP OPcache
 */

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <title>Limpar OPcache</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
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
        h1 { color: #FF6B00; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #FF6B00;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔄 Limpar OPcache</h1>
";

if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<div class='success'>
            ✅ <strong>OPcache limpo com sucesso!</strong><br>
            Todos os scripts PHP em cache foram removidos.
        </div>";
    } else {
        echo "<div class='error'>
            ❌ <strong>Falha ao limpar OPcache</strong><br>
            Pode ser necessário reiniciar o PHP-FPM ou LiteSpeed.
        </div>";
    }
    
    // Mostrar estatísticas
    $status = opcache_get_status();
    if ($status) {
        echo "<div class='info'>
            <strong>Estatísticas do OPcache:</strong><br>
            Memória usada: " . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB<br>
            Memória livre: " . round($status['memory_usage']['free_memory'] / 1024 / 1024, 2) . " MB<br>
            Scripts em cache: " . $status['opcache_statistics']['num_cached_scripts'] . "<br>
            Hits: " . $status['opcache_statistics']['hits'] . "<br>
            Misses: " . $status['opcache_statistics']['misses'] . "
        </div>";
    }
} else {
    echo "<div class='info'>
        ℹ️ <strong>OPcache não está ativo</strong><br>
        O OPcache não está habilitado neste servidor.
    </div>";
}

echo "
        <div style='text-align: center; margin-top: 30px;'>
            <a href='/clear-all-cache.php' class='btn'>Limpar Caches Laravel</a>
            <a href='/install/steps' class='btn'>Ir para Instalador</a>
        </div>
    </div>
</body>
</html>";
