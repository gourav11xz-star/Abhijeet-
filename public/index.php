<?php
// Load Config
require_once '../config/config.php';
// Load Database
require_once '../config/database.php';

// Load Helpers
require_once '../app/helpers/session_helper.php';
require_once '../app/helpers/url_helper.php';
require_once '../app/helpers/validation_helper.php';
require_once '../app/helpers/settings_helper.php';


// Load Custom Helpers
require_once '../app/helpers/ImageHelper.php';
require_once '../app/helpers/slug_helper.php';

// Autoload Core Libraries
spl_autoload_register(function ($className) {
    // Determine the class file path
    $paths = [
        '../app/core/',
        '../app/controllers/',
        '../app/models/',
        '../app/helpers/'
    ];

    foreach ($paths as $path) {
        if (file_exists($path . $className . '.php')) {
            require_once $path . $className . '.php';
            return;
        }
    }
});


// Maintenance Mode Check
if (function_exists('get_setting') && get_setting('maintenance_mode', 'off') === 'on') {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $isAdminArea = strpos($uri, '/admin') === 0 || strpos($uri, '/login') === 0 || strpos($uri, '/logout') === 0;

    if (!$isAdminArea) {
        http_response_code(503);
        echo '<!doctype html><html><head><title>Maintenance</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100 flex items-center justify-center min-h-screen"><div class="bg-white p-8 rounded shadow text-center"><h1 class="text-3xl font-bold mb-3">Site Under Maintenance</h1><p class="text-gray-600">We are updating the website. Please check back soon.</p></div></body></html>';
        exit;
    }
}

// Init Router
$router = new Router();

// Load Routes
require_once '../routes/web.php';

// Dispatch
$router->dispatch();
