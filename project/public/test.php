<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

echo "<h1>Database Connection Test</h1>";

$pdo = connect_db();
if ($pdo) {
    echo "<p style='color:green'>✓ Database connection successful!</p>";
    
    try {
        // Check users table structure
        $stmt = $pdo->query("DESCRIBE users");
        echo "<h3>Users Table Structure:</h3>";
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        while ($row = $stmt->fetch()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Count users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<p>Number of users in database: " . $result['count'] . "</p>";
        
        // Show first user
        $stmt = $pdo->query("SELECT * FROM users LIMIT 5");
        if ($user = $stmt->fetch()) {
            echo "<h3>Sample User:</h3>";
            echo "<ul>";
            foreach ($user as $key => $value) {
                if ($key !== 'password') { // Don't display the password hash
                    echo "<li>" . htmlspecialchars($key) . ": " . htmlspecialchars($value ?? 'NULL') . "</li>";
                } else {
                    echo "<li>password: [HIDDEN]</li>";
                }
            }
            echo "</ul>";
        }
        
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error testing database: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color:red'>✗ Database connection failed!</p>";
}