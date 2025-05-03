<?php

// This file defines the routes for the application.
// It's typically included by a router.

// We'll map URLs (like '/users') to controller actions 
// (like [App\Controllers\UserController::class, 'index']).

// Example route definitions (syntax depends on the router you choose/build):

use FastRoute\RouteCollector;
use App\Middleware\AuthMiddleware;
use App\Middleware\MiddlewareHandler;

// Define middleware groups
$authMiddleware = [AuthMiddleware::class];

// Define route groups with controller prefixes
$routeGroups = [
    'auth' => [
        'prefix' => '',
        'controller' => 'App\Controllers\AuthController',
        'middleware' => []
    ],
    'dashboard' => [
        'prefix' => '/admin',
        'controller' => 'App\Controllers\DashboardController',
        'middleware' => $authMiddleware
    ],
    'residents' => [
        'prefix' => '/residents',
        'controller' => 'App\Controllers\ResidentController',
        'middleware' => $authMiddleware
    ],
    'residents-account' => [
        'prefix' => '/residents-account',
        'controller' => 'App\Controllers\ResidentsAccountController',
        'middleware' => $authMiddleware
    ],
    'clearance' => [
        'prefix' => '/clearance',
        'controller' => 'App\Controllers\ClearanceController',
        'middleware' => $authMiddleware
    ],
    'rooms' => [
        'prefix' => '/rooms',
        'controller' => 'App\Controllers\RoomController',
        'middleware' => $authMiddleware
    ],
    'rental-fees' => [
        'prefix' => '/rental-fees',
        'controller' => 'App\Controllers\RentalFeeController',
        'middleware' => $authMiddleware
    ],
    'fines' => [
        'prefix' => '/fines',
        'controller' => 'App\Controllers\FineController',
        'middleware' => $authMiddleware
    ],
    'payments' => [
        'prefix' => '/payments',
        'controller' => 'App\Controllers\PaymentController',
        'middleware' => $authMiddleware
    ],
    'documents' => [
        'prefix' => '/documents',
        'controller' => 'App\Controllers\DocumentController',
        'middleware' => $authMiddleware
    ],
    'settings' => [
        'prefix' => '/settings',
        'controller' => 'App\Controllers\SettingController',
        'middleware' => $authMiddleware
    ]
];

// Define routes using FastRoute syntax
return function(RouteCollector $r) use ($routeGroups, $authMiddleware) {
    
    // A wrapper function to handle controller actions with middleware
    $handleRoute = function (string $controller, string $method, array $params = []) use ($routeGroups) {
        // Find the group this controller belongs to
        $group = null;
        foreach ($routeGroups as $key => $routeGroup) {
            if ($routeGroup['controller'] === $controller) {
                $group = $routeGroup;
                break;
            }
        }
        
        // Get middleware for this route
        $middleware = $group ? $group['middleware'] : [];
        
        // Create a handler for the controller method
        $handler = function () use ($controller, $method, $params) {
            $controllerInstance = new $controller();
            $controllerInstance->$method($params);
        };
        
        // Execute the middleware chain with the handler as the target
        if (!empty($middleware)) {
            MiddlewareHandler::executeMiddlewareChain($middleware, $handler);
        } else {
            $handler();
        }
    };

    // AUTH ROUTES (No prefix, no auth middleware)
    // Authentication Routes
    $r->addRoute('GET', '/', function() use ($handleRoute) {
        $handleRoute('App\Controllers\AuthController', 'showLoginForm');
    });
    
    $r->addRoute('GET', '/login', function() use ($handleRoute) {
        $handleRoute('App\Controllers\AuthController', 'showLoginForm');
    });
    
    $r->addRoute('POST', '/login', function() use ($handleRoute) {
        $handleRoute('App\Controllers\AuthController', 'login');
    });
    
    $r->addRoute('GET', '/register', function() use ($handleRoute) {
        $handleRoute('App\Controllers\AuthController', 'showRegistrationForm');
    });
    
    $r->addRoute('POST', '/register', function() use ($handleRoute) {
        $handleRoute('App\Controllers\AuthController', 'register');
    });
    
    $r->addRoute('GET', '/logout', function() use ($handleRoute) {
        $handleRoute('App\Controllers\AuthController', 'logout');
    });
    
    $r->addRoute('POST', '/logout', function() use ($handleRoute) {
        $handleRoute('App\Controllers\AuthController', 'logout');
    });
    
    // DASHBOARD ROUTES (with auth middleware)
    $r->addRoute('GET', '/admin', function() use ($handleRoute) {
        $handleRoute('App\Controllers\DashboardController', 'index');
    });
    

    // RESIDENTS ROUTES (with auth middleware)
    $r->addRoute('GET', '/residents', function() use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentController', 'index');
    });
    
    $r->addRoute('GET', '/residents/create', function() use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentController', 'create');
    });
    
    $r->addRoute('POST', '/residents', function() use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentController', 'store');
    });
    
    $r->addRoute('GET', '/residents/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentController', 'show', $params);
    });
    
    $r->addRoute('GET', '/residents/{id:\d+}/edit', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentController', 'edit', $params);
    });
    
    $r->addRoute('PUT', '/residents/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentController', 'update', $params);
    });
    
    $r->addRoute('DELETE', '/residents/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentController', 'destroy', $params);
    });
    

    // CLEARANCE ROUTES (with auth middleware)
    $r->addRoute('GET', '/clearance', function() use ($handleRoute) {
        $handleRoute('App\Controllers\ClearanceController', 'index');
    });
    
    $r->addRoute('GET', '/clearance/create', function() use ($handleRoute) {
        $handleRoute('App\Controllers\ClearanceController', 'create');
    });
    
    $r->addRoute('POST', '/clearance', function() use ($handleRoute) {
        $handleRoute('App\Controllers\ClearanceController', 'store');
    });
    
    $r->addRoute('GET', '/clearance/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ClearanceController', 'show', $params);
    });
    
    $r->addRoute('GET', '/clearance/{id:\d+}/edit', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ClearanceController', 'edit', $params);
    });
    
    $r->addRoute('PUT', '/clearance/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ClearanceController', 'update', $params);
    });
    
    $r->addRoute('DELETE', '/clearance/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ClearanceController', 'destroy', $params);
    });


    

    // FINANCE ROUTES (with auth middleware)
    $r->addRoute('GET', '/rental-fees', function() use ($handleRoute) {
        $handleRoute('App\Controllers\RentalFeeController', 'index');
    });
    
    $r->addRoute('GET', '/fines', function() use ($handleRoute) {
        $handleRoute('App\Controllers\FineController', 'index');
    });
    
    $r->addRoute('GET', '/payments', function() use ($handleRoute) {
        $handleRoute('App\Controllers\PaymentController', 'index');
    });



    
    // ROOM ROUTES (with auth middleware)
    $r->addRoute('GET', '/rooms', function() use ($handleRoute) {
        $handleRoute('App\Controllers\RoomController', 'index');
    });
    
    $r->addRoute('GET', '/rooms/create', function() use ($handleRoute) {
        $handleRoute('App\Controllers\RoomController', 'create');
    });
    
    $r->addRoute('POST', '/rooms', function() use ($handleRoute) {
        $handleRoute('App\Controllers\RoomController', 'store');
    });
    
    $r->addRoute('GET', '/rooms/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\RoomController', 'show', $params);
    });
    
    $r->addRoute('GET', '/rooms/{id:\d+}/edit', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\RoomController', 'edit', $params);
    });
    
    $r->addRoute('PUT', '/rooms/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\RoomController', 'update', $params);
    });
    
    $r->addRoute('DELETE', '/rooms/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\RoomController', 'destroy', $params);
    });
    




    // DOCUMENT ROUTES (with auth middleware)
    $r->addRoute('GET', '/documents', function() use ($handleRoute) {
        $handleRoute('App\Controllers\DocumentController', 'index');
    });
    
    $r->addRoute('GET', '/documents/create', function() use ($handleRoute) {
        $handleRoute('App\Controllers\DocumentController', 'create');
    });
    
    $r->addRoute('POST', '/documents', function() use ($handleRoute) {
        $handleRoute('App\Controllers\DocumentController', 'store');
    });
    
    $r->addRoute('GET', '/documents/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\DocumentController', 'show', $params);
    });
    
    $r->addRoute('GET', '/documents/{id:\d+}/edit', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\DocumentController', 'edit', $params);
    });
    
    $r->addRoute('PUT', '/documents/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\DocumentController', 'update', $params);
    });
    
    $r->addRoute('DELETE', '/documents/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\DocumentController', 'destroy', $params);
    });


    // RESIDENT ACCOUNTS ROUTES (with auth middleware)
    $r->addRoute('GET', '/residents-account', function() use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentsAccountController', 'index');
    });
    
    $r->addRoute('GET', '/residents-account/create', function() use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentsAccountController', 'create');
    });
    
    $r->addRoute('POST', '/residents-account', function() use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentsAccountController', 'store');
    });
    
    $r->addRoute('GET', '/residents-account/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentsAccountController', 'show', $params);
    });
    
    $r->addRoute('GET', '/residents-account/{id:\d+}/edit', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentsAccountController', 'edit', $params);
    });
    
    $r->addRoute('PUT', '/residents-account/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentsAccountController', 'update', $params);
    });
    
    $r->addRoute('DELETE', '/residents-account/{id:\d+}', function($params) use ($handleRoute) {
        $handleRoute('App\Controllers\ResidentsAccountController', 'destroy', $params);
    });
};