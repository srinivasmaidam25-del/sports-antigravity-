<?php

// Fix storage paths for Vercel serverless read-only filesystem
if (!is_dir('/tmp/storage')) {
    @mkdir('/tmp/storage', 0777, true);
    @mkdir('/tmp/storage/framework', 0777, true);
    @mkdir('/tmp/storage/framework/views', 0777, true);
    @mkdir('/tmp/storage/framework/cache', 0777, true);
    @mkdir('/tmp/storage/framework/cache/data', 0777, true);
    @mkdir('/tmp/storage/framework/sessions', 0777, true);
    @mkdir('/tmp/storage/logs', 0777, true);
}

// Forward Vercel serverless requests to Laravel's index.php entrypoint
require __DIR__ . '/../public/index.php';
