<?php
// Debug file to check server connectivity

// Show PHP info in a controlled way
echo "<h1>Debug Information</h1>";

// Check if we can access session
session_start();
echo "<h2>Session Status</h2>";
echo "Session ID: " . session_id() . "<br>";

// Check if we can include files from other directories
echo "<h2>Directory Access</h2>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Parent directory: " . dirname(__DIR__) . "<br>";

// Check routing information
echo "<h2>Server Variables</h2>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";

// Check if FastRoute is available
echo "<h2>Router Availability</h2>";
if (class_exists('\FastRoute\Dispatcher')) {
    echo "FastRoute Dispatcher class is available<br>";
} else {
    echo "FastRoute Dispatcher class is NOT available<br>";
}

// Test including the routes file
echo "<h2>Routes File Test</h2>";
try {
    $routesFile = dirname(__DIR__) . '/routes/web.php';
    echo "Routes file path: $routesFile<br>";
    echo "Routes file exists: " . (file_exists($routesFile) ? 'Yes' : 'No') . "<br>";
    
    if (file_exists($routesFile)) {
        echo "First few lines of routes file:<br>";
        $lines = file($routesFile, FILE_IGNORE_NEW_LINES);
        echo "<pre>";
        for ($i = 0; $i < min(10, count($lines)); $i++) {
            echo htmlspecialchars($lines[$i]) . "\n";
        }
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "Error testing routes file: " . $e->getMessage();
}

// Link to test other routes
echo "<h2>Test Links</h2>";
echo "<ul>";
echo "<li><a href='/login'>Login Page</a></li>";
echo "<li><a href='/register'>Register Page</a></li>";
echo "<li><a href='/admin'>Dashboard</a></li>";
echo "</ul>";

// Done
echo "<h2>Debug Complete</h2>";
?> 