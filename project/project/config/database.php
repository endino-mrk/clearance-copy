<?php
// config/database.php

    /**
     * Database connection function
     * @return PDO|null PDO connection object or null on failure
     */
    function connect_db() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "dormitory_clearance";
        
        try {
            $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $pdo = new PDO($dsn, $username, $password, $options);
            
            // Test connection with a simple query
            $pdo->query("SELECT 1");
            return $pdo;
        } catch (PDOException $e) {
            // More detailed error logging
            error_log("Database Connection Error: " . $e->getMessage() . 
                    " in " . $e->getFile() . " on line " . $e->getLine());
            return null;
        }
    }

    /**
     * Function to display database table contents (for debugging)
     * @param PDO $pdo PDO connection object
     * @param string $tableName Name of the table to display
     * @return void
     */
    function displayTable($pdo, $tableName) {
        echo "<h2>" . ucfirst($tableName) . " Table</h2>";
        
        try {
            $stmt = $pdo->query("SELECT * FROM $tableName");
            
            if ($stmt->rowCount() > 0) {
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                echo "<tr>";
                
                for ($i = 0; $i < $stmt->columnCount(); $i++) {
                    $col = $stmt->getColumnMeta($i);
                    echo "<th>" . ucfirst($col['name']) . "</th>";
                }
                echo "</tr>";
                
                while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table><br>";
            } else {
                echo "<p>No records found in $tableName table.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Error displaying table $tableName: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
?>