<?php

// List of controllers to create
$controllers = [
    'ClearanceController',
    'RentalFeeController',
    'FineController', 
    'PaymentController',
    'RoomController',
    'DocumentController',
    'SettingController',
    'UserController'
];

// Base directory for controllers
$baseDir = __DIR__ . '/app/Controllers/';

// Create each controller
foreach ($controllers as $controller) {
    $content = <<<PHP
<?php

namespace App\Controllers;

class {$controller} extends BaseController
{
    public function index(): void 
    { 
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset(\$_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        echo '<h1>{$controller} Index</h1>'; 
        echo '<p>This is a placeholder for the {$controller} index view.</p>';
        echo '<p><a href="/admin">Back to Dashboard</a></p>';
    }
    
    public function create(): void { echo '<h1>{$controller} Create</h1>'; }
    public function store(): void { echo '<h1>{$controller} Store</h1>'; }
    public function show(): void { echo '<h1>{$controller} Show</h1>'; }
    public function edit(): void { echo '<h1>{$controller} Edit</h1>'; }
    public function update(): void { echo '<h1>{$controller} Update</h1>'; }
    public function destroy(): void { echo '<h1>{$controller} Delete</h1>'; }
}
PHP;

    // Write to file
    $filePath = $baseDir . $controller . '.php';
    file_put_contents($filePath, $content);
    echo "Created $filePath\n";
}

echo "All controllers created successfully!\n"; 