<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv('APP_KEY=base64:ud3fMi4RmPv3+peN8tnBdFZYdDsVnVTXDsVtKB8Hj+s=');
$_ENV['APP_KEY'] = 'base64:ud3fMi4RmPv3+peN8tnBdFZYdDsVnVTXDsVtKB8Hj+s=';
$_SERVER['APP_KEY'] = 'base64:ud3fMi4RmPv3+peN8tnBdFZYdDsVnVTXDsVtKB8Hj+s=';

putenv('DB_CONNECTION=pgsql');
putenv('DB_HOST=52.74.252.201');
putenv('DB_PORT=6543');
putenv('DB_DATABASE=postgres');
putenv('DB_USERNAME=postgres.skjaklxscvginnvpdyvj');
putenv('DB_PASSWORD=Srinu9121085544');
putenv('DB_SSLMODE=prefer');
$_ENV['DB_CONNECTION'] = 'pgsql';
$_ENV['DB_HOST'] = '52.74.252.201';
$_ENV['DB_PORT'] = '6543';
$_ENV['DB_DATABASE'] = 'postgres';
$_ENV['DB_USERNAME'] = 'postgres.skjaklxscvginnvpdyvj';
$_ENV['DB_PASSWORD'] = 'Srinu9121085544';
$_ENV['DB_SSLMODE'] = 'prefer';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_STORAGE=/tmp/storage');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('CACHE_DRIVER=array');
putenv('LOG_CHANNEL=stderr');

$storageDirs = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

require __DIR__ . '/../vendor/autoload.php';

try {
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $request = \Illuminate\Http\Request::capture();

    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    http_response_code(200);
    echo "<h1>Direct Boot Error Caught</h1>";
    echo "<p><strong>Exception:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
