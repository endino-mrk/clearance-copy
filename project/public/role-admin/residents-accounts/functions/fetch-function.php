<?php
// functions/fetch-function.php

// Set header to return JSON
header('Content-Type: application/json');

require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/../../../config/database.php';

$response = [];

// Basic check for GET request and ID
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    $response = ['error' => 'Invalid request'];
    echo json_encode($response);
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    $response = ['error' => 'Invalid resident ID'];
    echo json_encode($response);
    exit;
}

$response = ['error' => 'Failed to fetch resident data']; 

$pdo = connect_db();
if ($pdo) {
    try {
        $sql = "SELECT user_id, first_name, last_name, email, created_at 
                FROM users 
                WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $resident = $stmt->fetch(PDO::FETCH_ASSOC); 
            if ($resident) {
                $response = $resident;
            } else {
                $response = ['error' => 'Resident not found'];
            }
        }
    } catch (PDOException $e) {
        error_log("Fetch Resident Error (functions/fetch-function.php): " . $e->getMessage());
        $response = ['error' => 'Database error occurred'];
    }
} else {
    $response = ['error' => 'Database connection failed'];
}

echo json_encode($response);
exit;
?> 