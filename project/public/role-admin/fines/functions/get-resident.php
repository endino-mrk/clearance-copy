<?php
// Set header to return JSON
header('Content-Type: application/json');

// Include the necessary files
require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/../../../config/database.php';

// Default response
$response = [
    'success' => false,
    'message' => 'No resident ID provided',
    'data' => null
];

// Check for resident ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode($response);
    exit;
}

$resident_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if (!$resident_id) {
    $response['message'] = 'Invalid resident ID';
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
    // Get resident details
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
            WHERE r.resident_id = :resident_id
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $resident = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resident) {
        // Add initials
        $resident['initials'] = $resident['firstInitial'] . $resident['lastInitial'];
        
        // Remove unnecessary properties
        unset($resident['firstInitial']);
        unset($resident['lastInitial']);
        
        // Update response with resident details
        $response['success'] = true;
        $response['message'] = 'Resident found';
        $response['data'] = $resident;
    } else {
        $response['message'] = 'Resident not found';
    }
    
} catch (PDOException $e) {
    error_log("Get Resident Error: " . $e->getMessage());
    $response['message'] = 'An error occurred while fetching resident details: ' . $e->getMessage();
}

echo json_encode($response);
exit;