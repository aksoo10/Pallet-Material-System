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

// Delegate execution to Laravel's public/index.php
require __DIR__.'/../public/index.php';
