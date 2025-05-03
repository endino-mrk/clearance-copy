<?php

declare(strict_types=1);

// Define the base directory of the application
define('BASE_PATH', dirname(__DIR__));

// Register Composer's autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load .env configuration
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Split the line into key and value
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Set environment variable
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Bootstrap the database connection
require BASE_PATH . '/config/database_bootstrap.php';

// --- Session Start ---
session_start();

// --- Routing Setup ---
$routeDefinitionCallback = require BASE_PATH . '/routes/web.php';
$dispatcher = FastRoute\simpleDispatcher($routeDefinitionCallback);

// Fetch method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Handle method overriding for forms (PUT/PATCH/DELETE)
if ($httpMethod === 'POST' && isset($_POST['_method'])) {
    $overrideMethod = strtoupper($_POST['_method']);
    if (in_array($overrideMethod, ['PUT', 'PATCH', 'DELETE'])) {
        $httpMethod = $overrideMethod;
    }
}

// --- Authentication Check ---
// Define routes that DON'T require login (using the route path)
$publicRoutes = [
    '/login',
    '/register',
    '/', // Assuming '/' is your landing page route
    // Add any other specific public route paths here
];

// Check if user is logged in AND accessing a protected route
if (!isset($_SESSION['user_id']) && !in_array($uri, $publicRoutes)) {
    // User is not logged in and trying to access a protected page
    // Redirect to the login page route
    header('Location: /login'); // Redirect to the LOGIN ROUTE
    exit;
}
// --- End Authentication Check ---

// Dispatch the route
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        http_response_code(404);
        echo '404 Not Found';
        // TODO: Render a 404 view
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        http_response_code(405);
        echo '405 Method Not Allowed. Allowed methods: ' . implode(', ', $allowedMethods);
        // TODO: Render a 405 view
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2]; // Route parameters (e.g., {'id' => '123'})

        // Handle different types of route handlers
        if (is_callable($handler)) {
            // Handler is a closure/callable function
            call_user_func($handler, $vars);
        } elseif (is_array($handler) && count($handler) === 2) {
            // Handler is a controller-method pair [ControllerClass, 'methodName']
            $controllerClass = $handler[0];
            $controllerMethod = $handler[1];

            if (class_exists($controllerClass)) {
                // TODO: Implement Dependency Injection container later if needed
                $controller = new $controllerClass();

                if (method_exists($controller, $controllerMethod)) {
                    // Call the handler method, passing route parameters
                    // Use array_values to ensure numeric indexes for call_user_func_array
                    call_user_func_array([$controller, $controllerMethod], array_values($vars));
                } else {
                    // Method not found in controller
                    http_response_code(500);
                    echo "Error: Controller method '{$controllerMethod}' not found in class '{$controllerClass}'.";
                }
            } else {
                 // Controller class not found
                 http_response_code(500);
                 echo "Error: Controller class '{$controllerClass}' not found.";
            }
        } else {
            // Invalid handler format in routes
            http_response_code(500);
            echo "Error: Invalid route handler format defined.";
        }
        break;
} 