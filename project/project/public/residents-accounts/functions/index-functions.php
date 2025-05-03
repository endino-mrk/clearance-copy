<?php

require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/config/database.php';

$resident_accounts = [];

try {
        $pdo = connect_db();
        if (!$pdo) {
            $_SESSION['errors']['db_connection'] = "Database connection failed."; 
        } else {
            $stmt = $pdo->query("SELECT id, first_name, last_name, middle_name, email, phone_number, created_at FROM users ORDER BY last_name, first_name");
            $resident_accounts = $stmt->fetchAll(PDO::FETCH_OBJ);
        }

    } catch (\PDOException $e) {
        error_log("Database error in functions/index-functions.php: " . $e->getMessage());
        $_SESSION['errors']['db_query'] = "Could not retrieve user data.";
        $resident_accounts = [];
    }
?> 