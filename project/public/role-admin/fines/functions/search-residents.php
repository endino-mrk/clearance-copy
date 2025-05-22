<?php
// functions/search-residents.php

// Set header to return JSON
header('Content-Type: application/json');

require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/../../../config/database.php';

// Default response
$response = [
    'success' => false,
    'message' => 'No search query provided',
    'data' => []
];

// Check for search query
if (!isset($_GET['query']) || empty($_GET['query'])) {
    echo json_encode($response);
    exit;
}

$query = trim($_GET['query']);

// Require at least 2 characters
if (strlen($query) < 2) {
    $response['message'] = 'Search query must be at least 2 characters';
    echo json_encode($response);
    exit;
}

$pdo = connect_db();
if (!$pdo) {
    $response['message'] = 'Database connection failed';
    echo json_encode($response);
    exit;
}

try {
    // Search by name or student ID
    $sql = "SELECT 
                r.resident_id as id,
                r.student_id as studentId, 
                CONCAT(u.first_name, ' ', u.last_name) as fullName,
                SUBSTRING(u.first_name, 1, 1) as firstInitial,
                SUBSTRING(u.last_name, 1, 1) as lastInitial,
                rm.number as room,
            FROM residents r
            JOIN users u ON r.user_id = u.user_id
            LEFT JOIN resident_occupancy ro ON r.resident_id = ro.resident_id AND ro.active = 1
            LEFT JOIN rooms rm ON ro.room_id = rm.room_id
            WHERE (u.first_name LIKE :query 
                OR u.last_name LIKE :query 
                OR CONCAT(u.first_name, ' ', u.last_name) LIKE :query
                OR r.student_id LIKE :query)
            AND r.active = 1 
            AND u.active = 1
            ORDER BY u.first_name, u.last_name
            LIMIT 10";
    
    $searchPattern = '%' . $query . '%';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':query', $searchPattern, PDO::PARAM_STR);
    $stmt->execute();
    
    $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($residents as &$resident) {
        // Add initials
        $resident['initials'] = $resident['firstInitial'] . $resident['lastInitial'];
        
        // Remove unnecessary properties
        unset($resident['firstInitial']);
        unset($resident['lastInitial']);
    }
    
    $response['success'] = true;
    $response['message'] = count($residents) . ' residents found';
    $response['data'] = $residents;
    
} catch (PDOException $e) {
    error_log("Search Residents Error: " . $e->getMessage());
    $response['message'] = 'An error occurred while searching residents: ' . $e->getMessage();
}

echo json_encode($response);
exit;