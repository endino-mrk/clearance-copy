<?php
// project/public/debug.php - Create this file temporarily for debugging
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

echo "<h2>Session Debug Information</h2>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Current File:</strong> " . $_SERVER['PHP_SELF'] . "</p>";

echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Database Test:</h3>";
$pdo = connect_db();
if ($pdo) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test user query
    if (isset($_SESSION['user_id'])) {
        try {
            $sql = "SELECT * FROM users WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "<p style='color: green;'>✓ User found in database</p>";
                echo "<p><strong>User Type:</strong> " . ($user['type'] ?? 'NULL') . "</p>";
                echo "<p><strong>User Active:</strong> " . ($user['active'] ?? 'NULL') . "</p>";
                
                // Check resident record if needed
                if (in_array($user['type'], ['Resident', 'Treasurer'])) {
                    $residentSql = "SELECT * FROM residents WHERE user_id = :user_id AND active = 1";
                    $residentStmt = $pdo->prepare($residentSql);
                    $residentStmt->bindParam(':user_id', $_SESSION['user_id']);
                    $residentStmt->execute();
                    $resident = $residentStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($resident) {
                        echo "<p style='color: green;'>✓ Resident record found</p>";
                        echo "<p><strong>Resident ID:</strong> " . $resident['resident_id'] . "</p>";
                    } else {
                        echo "<p style='color: red;'>✗ No resident record found</p>";
                    }
                }
            } else {
                echo "<p style='color: red;'>✗ User not found or inactive</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>No user_id in session</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
}

echo "<h3>Actions:</h3>";
echo "<p><a href='login.php'>Go to Login</a></p>";
echo "<p><a href='logout.php'>Logout & Clear Session</a></p>";
echo "<p><a href='debug.php'>Refresh Debug</a></p>";

// Clear session button
if (isset($_GET['clear'])) {
    session_destroy();
    echo "<p style='color: green;'>Session cleared. <a href='debug.php'>Refresh</a></p>";
} else {
    echo "<p><a href='debug.php?clear=1' style='color: red;'>Clear Session</a></p>";
}

echo "<h3>Test Login Data:</h3>";
echo "<p>Try logging in with test accounts. Make sure you have users in your database with proper types.</p>";
echo "<p>You can create test data with:</p>";
echo "<pre>";
echo "UPDATE users SET type = 'Manager' WHERE email = 'your-manager-email@example.com';\n";
echo "UPDATE users SET type = 'Treasurer' WHERE email = 'your-treasurer-email@example.com';\n";
echo "UPDATE users SET type = 'Resident' WHERE email = 'your-resident-email@example.com';";
echo "</pre>";
?>