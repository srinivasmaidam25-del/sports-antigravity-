<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// Fallback APP_KEY
if (!getenv('APP_KEY') && !isset($_ENV['APP_KEY'])) {
    putenv('APP_KEY=base64:ud3fMi4RmPv3+peN8tnBdFZYdDsVnVTXDsVtKB8Hj+s=');
    $_ENV['APP_KEY'] = 'base64:ud3fMi4RmPv3+peN8tnBdFZYdDsVnVTXDsVtKB8Hj+s=';
    $_SERVER['APP_KEY'] = 'base64:ud3fMi4RmPv3+peN8tnBdFZYdDsVnVTXDsVtKB8Hj+s=';
}

// Fallback Database configurations
if (!getenv('DB_CONNECTION') && !isset($_ENV['DB_CONNECTION'])) {
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
}

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

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Error Caught in api/index.php</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
