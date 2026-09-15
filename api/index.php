<?php

// Ensure all required /tmp directories exist for Laravel in Vercel serverless environment
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/app/public',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set environment variables for serverless runtime
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');

// Ensure timezone is valid
$timezone = getenv('APP_TIMEZONE') ?: ($_ENV['APP_TIMEZONE'] ?? null);
if (empty($timezone) || ! @timezone_open((string) $timezone)) {
    putenv('APP_TIMEZONE=Asia/Jakarta');
    $_ENV['APP_TIMEZONE'] = 'Asia/Jakarta';
    $_SERVER['APP_TIMEZONE'] = 'Asia/Jakarta';
}

// Delegate execution to Laravel's public/index.php with fallback error capture
try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><title>500 Application Error</title><style>body{font-family:sans-serif;padding:30px;background:#f8f9fa;color:#333;}pre{background:#fff;padding:15px;border-radius:6px;border:1px solid #ddd;overflow:auto;}</style></head><body>';
    echo '<h2>Application Error (500)</h2>';
    echo '<p><strong>Message:</strong> '.htmlspecialchars($e->getMessage()).'</p>';
    echo '<p><strong>File:</strong> '.htmlspecialchars($e->getFile()).' on line '.$e->getLine().'</p>';
    echo '<pre>'.htmlspecialchars($e->getTraceAsString()).'</pre>';
    echo '</body></html>';
}
