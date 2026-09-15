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

// Ensure critical environment variables are not empty strings
$defaultEnvs = [
    'SESSION_DRIVER' => 'cookie',
    'SESSION_SECURE_COOKIE' => 'true',
    'CACHE_STORE' => 'array',
    'APP_TIMEZONE' => 'Asia/Jakarta',
    'APP_ENV' => 'production',
    'APP_MAINTENANCE_DRIVER' => 'file',
    'APP_KEY' => 'base64:lvX3kwemEw1kb+Sa2RHzlxOEgyo5LEP/wod7WKwgZR0=',
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '38.45.72.91',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'Material_Dressing',
    'DB_USERNAME' => 'u168_AgufvtiYua',
    'DB_PASSWORD' => 'f.azi@5P84cVo3erlGfRXCXA',
];

foreach ($defaultEnvs as $key => $defaultVal) {
    $val = getenv($key) ?: ($_ENV[$key] ?? ($_SERVER[$key] ?? null));
    if ($val === false || trim((string) $val) === '') {
        putenv("{$key}={$defaultVal}");
        $_ENV[$key] = $defaultVal;
        $_SERVER[$key] = $defaultVal;
    }
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
