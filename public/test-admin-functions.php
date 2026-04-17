<?php
/**
 * Script de teste para verificar funcionalidades do painel admin
 */

header('Content-Type: application/json');

// Verificar se Laravel está carregado
$laravelPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($laravelPath)) {
    echo json_encode([
        'success' => false,
        'message' => 'Laravel não encontrado'
    ]);
    exit;
}

require $laravelPath;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Testar rotas admin
$routes = [
    'admin.dashboard' => '/admin/dashboard',
    'admin.services.index' => '/admin/services',
    'admin.gallery.index' => '/admin/gallery',
    'admin.upload.index' => '/admin/upload',
    'admin.seo.index' => '/admin/seo',
    'admin.documentation.index' => '/admin/documentacao',
];

$results = [];

foreach ($routes as $name => $uri) {
    try {
        $route = app('router')->getRoutes()->getByName($name);
        
        $results[$name] = [
            'exists' => $route !== null,
            'uri' => $route ? $route->uri() : 'N/A',
            'methods' => $route ? $route->methods() : [],
            'action' => $route ? $route->getActionName() : 'N/A'
        ];
    } catch (Exception $e) {
        $results[$name] = [
            'exists' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Verificar controllers
$controllers = [
    'Dashboard' => 'App\\Modules\\Dashboard\\Controllers\\DashboardController',
    'Services' => 'App\\Modules\\Services\\Controllers\\ServiceController',
    'Gallery' => 'App\\Modules\\Gallery\\Controllers\\GalleryController',
    'Upload' => 'App\\Modules\\Upload\\Controllers\\UploadController',
    'SEO' => 'App\\Modules\\Seo\\Controllers\\SeoController',
    'Documentation' => 'App\\Modules\\Documentation\\Controllers\\DocumentationController',
];

$controllerStatus = [];

foreach ($controllers as $name => $class) {
    $controllerStatus[$name] = [
        'exists' => class_exists($class),
        'class' => $class,
        'methods' => class_exists($class) ? get_class_methods($class) : []
    ];
}

// Verificar views
$views = [
    'layouts.admin' => resource_path('views/layouts/admin.blade.php'),
    'dashboard.index' => resource_path('views/modules/dashboard/index.blade.php'),
    'services.index' => resource_path('views/modules/services/index.blade.php'),
    'gallery.index' => resource_path('views/modules/gallery/index.blade.php'),
];

$viewStatus = [];

foreach ($views as $name => $path) {
    $viewStatus[$name] = [
        'exists' => file_exists($path),
        'path' => $path,
        'size' => file_exists($path) ? filesize($path) : 0
    ];
}

// Verificar assets
$assets = [
    'admin.css' => public_path('css/admin.css'),
    'admin.js' => public_path('js/admin.js'),
];

$assetStatus = [];

foreach ($assets as $name => $path) {
    $assetStatus[$name] = [
        'exists' => file_exists($path),
        'path' => $path,
        'size' => file_exists($path) ? filesize($path) : 0
    ];
}

// Verificar banco de dados
$dbStatus = [
    'connected' => false,
    'tables' => []
];

try {
    DB::connection()->getPdo();
    $dbStatus['connected'] = true;
    
    $tables = DB::select('SHOW TABLES');
    $dbStatus['tables'] = array_map(function($table) {
        return array_values((array)$table)[0];
    }, $tables);
    
    $dbStatus['table_count'] = count($dbStatus['tables']);
} catch (Exception $e) {
    $dbStatus['error'] = $e->getMessage();
}

// Resposta final
$response = [
    'success' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'routes' => $results,
    'controllers' => $controllerStatus,
    'views' => $viewStatus,
    'assets' => $assetStatus,
    'database' => $dbStatus,
    'summary' => [
        'routes_working' => count(array_filter($results, fn($r) => $r['exists'] ?? false)),
        'routes_total' => count($results),
        'controllers_working' => count(array_filter($controllerStatus, fn($c) => $c['exists'])),
        'controllers_total' => count($controllerStatus),
        'views_working' => count(array_filter($viewStatus, fn($v) => $v['exists'])),
        'views_total' => count($viewStatus),
        'assets_working' => count(array_filter($assetStatus, fn($a) => $a['exists'])),
        'assets_total' => count($assetStatus),
        'database_connected' => $dbStatus['connected']
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
