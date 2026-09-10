<?php

// Standalone cPanel Cache Cleaner Script
$baseDir = dirname(__DIR__);

// Load Laravel Bootstrap
require $baseDir . '/vendor/autoload.php';
$app = require_once $baseDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    
    echo "<h1>✅ cPanel Laravel Cache Cleared Successfully!</h1>";
    echo "<p>View, route, config, and framework caches have been wiped.</p>";
    echo "<p><a href='/'>Click here to return to Home Page</a></p>";
} catch (\Throwable $e) {
    echo "<h1>Cache Clearing Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
