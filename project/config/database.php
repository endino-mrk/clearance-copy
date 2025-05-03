<?php

// config/database.php

// Replace with your actual database credentials
define('DB_HOST', 'localhost');      // Usually 'localhost' or '127.0.0.1'
define('DB_PORT', '3306');         // Default MySQL/MariaDB port
define('DB_NAME', 'clearance'); // Your database name
define('DB_USER', 'root');    // Your database username
define('DB_PASS', '');    // Your database password
define('DB_CHARSET', 'utf8mb4');

// --- PDO Connection Function ---

/**
 * Establishes a PDO database connection.
 *
 * @return PDO|null Returns a PDO connection object on success, null on failure.
 */
function connect_db(): ?PDO
{
    static $pdo = null; // Static variable to hold the connection

    if ($pdo === null) { // Connect only if not already connected
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    // Fetch results as associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false,             // Use native prepared statements
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            // Log the error securely (don't echo detailed errors in production)
            error_log('Database Connection Error: ' . $e->getMessage());
            // You might want to display a generic error message to the user
            // or handle this more gracefully depending on your application needs.
            // For now, we return null to indicate failure.
            return null; 
        }
    }

    return $pdo;
}

return [
    'host' => 'localhost',      // Or your database host (e.g., 127.0.0.1)
    'port' => 3306,           // Default MySQL/MariaDB port
    'dbname' => 'clearance', // Replace with your database name
    'username' => 'root', // Replace with your database username
    'password' => '', // Replace with your database password
    'charset' => 'utf8mb4'
]; 