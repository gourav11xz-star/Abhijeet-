<?php
// App Config
define('APP_NAME', 'MarketSphere');
define('APP_VERSION', '1.0.0');
define('APP_ROOT', dirname(dirname(__FILE__)) . '/app');
define('URL_ROOT', 'http://35.154.222.65'); // Updated to root url as htaccess handles public

// Database Config
define('DB_HOST', 'localhost');
define('DB_USER', 'marketuser');
define('DB_PASS', '');
define('DB_NAME', 'marketsphere_db');
define('DB_CHARSET', 'utf8mb4');

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');
