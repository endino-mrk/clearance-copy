<?php
// Enhanced fetch-function.php with column detection

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
    $response = ['error' => 'Invalid fine ID'];
    echo json_encode($response);
    exit;
}

// Function to check if a column exists in a table
function columnExists($pdo, $table, $column) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM $table LIKE '$column'");
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error checking column: " . $e->getMessage());
        return false;
    }
}

$response = ['error' => 'Failed to fetch fine data']; 

$pdo = connect_db();
if ($pdo) {
    try {
        // Check if description column exists
        $hasDescriptionColumn = columnExists($pdo, 'resident_fines', 'description');
        
        // Build SQL based on available columns
        $sql = "SELECT rf.resident_fine_id, rf.occupancy_id, rf.fine_id, f.name as reason, 
                       f.amount, rf.status, rf.violation_date, 
                       rf.date_issued, rf.date_paid, rf.updated_at,
                       r.resident_id, r.student_id, CONCAT(u.first_name, ' ', u.last_name) as resident_name,
                       rm.number as room";
        
        // Only include description if it exists
        if ($hasDescriptionColumn) {
            $sql .= ", rf.description";
        }
        
        $sql .= " FROM resident_fines rf
                JOIN fines f ON rf.fine_id = f.fine_id
                JOIN resident_occupancy ro ON rf.occupancy_id = ro.occupancy_id
                JOIN residents r ON ro.resident_id = r.resident_id
                JOIN users u ON r.user_id = u.user_id
                JOIN rooms rm ON ro.room_id = rm.room_id
                WHERE rf.resident_fine_id = :fine_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fine_id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $fine = $stmt->fetch(PDO::FETCH_ASSOC); 
            if ($fine) {
                $response = $fine;
                
                // Format dates for display
                if (!empty($fine['violation_date'])) {
                    $response['violation_date'] = date('Y-m-d', strtotime($fine['violation_date']));
                }
                
                if (!empty($fine['date_issued'])) {
                    $response['date_issued'] = date('Y-m-d', strtotime($fine['date_issued']));
                }
                
                if (!empty($fine['date_paid'])) {
                    $response['date_paid'] = date('Y-m-d', strtotime($fine['date_paid']));
                }
                
                // Calculate due date (15 days after issue date by default)
                if (!empty($fine['date_issued'])) {
                    $dueDate = date('Y-m-d', strtotime($fine['date_issued'] . ' + 15 days'));
                    $response['due_date'] = $dueDate;
                }
                
                // Add flag to indicate if description column exists
                $response['has_description_column'] = $hasDescriptionColumn;
            } else {
                $response = ['error' => 'Fine not found'];
            }
        }
    } catch (PDOException $e) {
        error_log("Fetch Fine Error: " . $e->getMessage());
        $response = ['error' => 'Database error occurred: ' . $e->getMessage()];
    }
} else {
    $response = ['error' => 'Database connection failed'];
}

echo json_encode($response);
exit;