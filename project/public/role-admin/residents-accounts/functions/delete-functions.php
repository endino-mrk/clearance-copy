<?php
// functions/delete-functions.php
require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/../../../config/database.php';

// We expect AJAX POST request for deletes now
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$response = ['success' => false, 'message' => 'Invalid request.']; // Default JSON response

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$is_ajax) {
    if ($is_ajax) { echo json_encode($response); } 
    exit;
}

// --- Basic Validation ---
$id = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    $response['message'] = 'Invalid resident ID provided.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// --- Process Data ---
$pdo = connect_db();
if ($pdo) {
    try {
        // $sql = "DELETE FROM users WHERE user_id = :user_id";
        $sql = "UPDATE users SET active = 0 WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $response = ['success' => true, 'message' => 'Resident deleted successfully!'];
            }
        } else {
             $response['message'] = 'Failed to delete resident.';
        }

    } catch (PDOException $e) {
        error_log("Delete Resident Error: " . $e->getMessage());
        $response['message'] = 'An unexpected database error occurred.';
    }
} else {
    $response['message'] = 'Database connection failed.';
}

// --- Output JSON Response ---
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>
