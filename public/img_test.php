<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

header('Content-Type: application/json');

$results = [
    'php_version' => PHP_VERSION,
    'drivers_test' => []
];

$drivers = [
    'Imagick' => ImagickDriver::class,
    'GD' => GdDriver::class
];

foreach ($drivers as $name => $class) {
    try {
        $manager = new ImageManager(new $class());
        $image = $manager->create(100, 100)->fill('ff0000');
        
        $testPath = public_path('uploads/test_' . strtolower($name) . '.jpg');
        $dir = dirname($testPath);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        $image->save($testPath);
        
        $results['drivers_test'][$name] = [
            'status' => 'Success',
            'path' => $testPath,
            'exists' => file_exists($testPath),
            'writable' => is_writable($dir)
        ];
    } catch (\Exception $e) {
        $results['drivers_test'][$name] = [
            'status' => 'Failed',
            'error' => $e->getMessage()
        ];
    }
}

echo json_encode($results, JSON_PRETTY_PRINT);
