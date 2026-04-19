<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: application/json');

$results = [
    'php_version' => PHP_VERSION,
    'extensions' => [
        'gd' => extension_loaded('gd'),
        'exif' => extension_loaded('exif'),
        'mbstring' => extension_loaded('mbstring'),
        'imagick' => extension_loaded('imagick'),
    ],
    'db_connection' => [
        'status' => 'unknown',
        'error' => null,
        'config' => [
            'host' => config('database.connections.mysql.host'),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
        ]
    ],
    'directories' => [
        'public_uploads' => is_writable(public_path('uploads')),
        'storage_app_public' => is_writable(storage_path('app/public')),
    ]
];

try {
    DB::connection()->getPdo();
    $results['db_connection']['status'] = 'Connected!';
    $results['db_connection']['tables_count'] = count(DB::select('SHOW TABLES'));
} catch (\Exception $e) {
    $results['db_connection']['status'] = 'Failed';
    $results['db_connection']['error'] = $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT);
