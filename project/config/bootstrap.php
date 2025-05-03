<?php

// Define the absolute path to the project root
define('BASE_PATH', dirname(__DIR__));

// You can add other global setup here later if needed, like error reporting settings
// error_reporting(E_ALL);
// ini_set('display_errors', 1); 

// Start session if not already started (useful for auth)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration (optional here, can be included where needed)
// require_once BASE_PATH . '/config/database.php';

// Include helper functions globally
require_once BASE_PATH . '/includes/functions.php';

?> 