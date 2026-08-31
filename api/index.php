<?php

putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

// Fallback APP_KEY
if (empty(getenv('APP_KEY')) || empty($_ENV['APP_KEY'])) {
    putenv('APP_KEY=base64:ud3fMi4RmPv3+peN8tnBdFZYdDsVnVTXDsVtKB8Hj+s=');
    $_ENV['APP_KEY'] = 'base64:ud3fMi4RmPv3+peN8tnBdFZYdDsVnVTXDsVtKB8Hj+s=';
    $_SERVER['APP_KEY'] = 'base64:ud3fMi4RmPv3+peN8tnBdFZYdDsVnVTXDsVtKB8Hj+s=';
}

// Fallback Database configurations with strict empty() checks
if (empty(getenv('DB_CONNECTION')) || empty($_ENV['DB_CONNECTION'])) {
    putenv('DB_CONNECTION=pgsql');
    $_ENV['DB_CONNECTION'] = 'pgsql';
    $_SERVER['DB_CONNECTION'] = 'pgsql';
}
if (empty(getenv('DB_HOST')) || empty($_ENV['DB_HOST'])) {
    putenv('DB_HOST=52.74.252.201');
    $_ENV['DB_HOST'] = '52.74.252.201';
    $_SERVER['DB_HOST'] = '52.74.252.201';
}
if (empty(getenv('DB_PORT')) || empty($_ENV['DB_PORT'])) {
    putenv('DB_PORT=6543');
    $_ENV['DB_PORT'] = '6543';
    $_SERVER['DB_PORT'] = '6543';
}
if (empty(getenv('DB_DATABASE')) || empty($_ENV['DB_DATABASE'])) {
    putenv('DB_DATABASE=postgres');
    $_ENV['DB_DATABASE'] = 'postgres';
    $_SERVER['DB_DATABASE'] = 'postgres';
}
if (empty(getenv('DB_USERNAME')) || empty($_ENV['DB_USERNAME'])) {
    putenv('DB_USERNAME=postgres.skjaklxscvginnvpdyvj');
    $_ENV['DB_USERNAME'] = 'postgres.skjaklxscvginnvpdyvj';
    $_SERVER['DB_USERNAME'] = 'postgres.skjaklxscvginnvpdyvj';
}
if (empty(getenv('DB_PASSWORD')) || empty($_ENV['DB_PASSWORD'])) {
    putenv('DB_PASSWORD=Srinu9121085544');
    $_ENV['DB_PASSWORD'] = 'Srinu9121085544';
    $_SERVER['DB_PASSWORD'] = 'Srinu9121085544';
}
if (empty(getenv('DB_SSLMODE')) || empty($_ENV['DB_SSLMODE'])) {
    putenv('DB_SSLMODE=prefer');
    $_ENV['DB_SSLMODE'] = 'prefer';
    $_SERVER['DB_SSLMODE'] = 'prefer';
}

putenv('APP_MAINTENANCE_DRIVER=file');
$_ENV['APP_MAINTENANCE_DRIVER'] = 'file';
$_SERVER['APP_MAINTENANCE_DRIVER'] = 'file';

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

// Serve compiled static assets (CSS, JS, Fonts, Images)
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
$publicFile = __DIR__ . '/../public' . $uri;

if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css'   => 'text/css; charset=utf-8',
        'js'    => 'application/javascript; charset=utf-8',
        'json'  => 'application/json; charset=utf-8',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
    ];

    header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($publicFile);
    exit;
}

require __DIR__ . '/../public/index.php';
