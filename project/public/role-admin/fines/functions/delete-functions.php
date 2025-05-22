<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
require_once __DIR__ . '/../../../../config/database.php';

// Only process POST requests - no AJAX checks
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['errors']['general'] = 'Invalid request method.';
    header('Location: ../fine-list.php');
    exit;
}

// --- Basic Validation ---
$fine_id = filter_var($_POST['fine_id'] ?? null, FILTER_VALIDATE_INT);

if (!$fine_id) {
    $_SESSION['errors']['general'] = 'Invalid fine ID provided.';
    header('Location: ../fine-list.php');
    exit;
}

// --- Process Data ---
$pdo = connect_db();
if ($pdo) {
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Get the occupancy_id first for clearance record updates
        $stmt = $pdo->prepare("SELECT occupancy_id FROM resident_fines WHERE resident_fine_id = :fine_id");
        $stmt->bindParam(':fine_id', $fine_id, PDO::PARAM_INT);
        $stmt->execute();
        $fine = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$fine) {
            throw new Exception("Fine record not found.");
        }
        
        $occupancy_id = $fine['occupancy_id'];
        
        // Delete the fine record
        $sql = "DELETE FROM resident_fines WHERE resident_fine_id = :fine_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fine_id', $fine_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Check if there are still unpaid fines for this occupancy
            $checkSql = "SELECT COUNT(*) as unpaid_count 
                        FROM resident_fines 
                        WHERE occupancy_id = :occupancy_id 
                        AND status NOT IN ('Paid', 'Waived')";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
            $checkStmt->execute();
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            $fineStatus = ($result['unpaid_count'] > 0) ? 'Pending' : 'Cleared';
            
            // Update clearance_records with the new fine status
            $updateClearanceSql = "UPDATE clearance_records 
                                  SET fine_status = :fine_status 
                                  WHERE occupancy_id = :occupancy_id";
            $updateClearanceStmt = $pdo->prepare($updateClearanceSql);
            $updateClearanceStmt->bindParam(':fine_status', $fineStatus);
            $updateClearanceStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
            $updateClearanceStmt->execute();
            
            // Check if all clearance requirements are now cleared
            if ($fineStatus === 'Cleared') {
                $checkClearanceSql = "SELECT * FROM clearance_records 
                                     WHERE occupancy_id = :occupancy_id 
                                     AND (rental_fee_status = 'Pending' 
                                         OR fine_status = 'Pending' 
                                         OR room_status = 'Pending' 
                                         OR document_status = 'Pending')";
                $checkClearanceStmt = $pdo->prepare($checkClearanceSql);
                $checkClearanceStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
                $checkClearanceStmt->execute();
                
                if ($checkClearanceStmt->rowCount() === 0) {
                    // All requirements cleared
                    $current_user_id = $_SESSION['user_id'] ?? 1; // Default to 1 if not logged in (for testing)
                    $finalUpdateSql = "UPDATE clearance_records 
                                      SET status = 'Cleared', date_cleared = NOW(), cleared_by = :cleared_by 
                                      WHERE occupancy_id = :occupancy_id";
                    $finalUpdateStmt = $pdo->prepare($finalUpdateSql);
                    $finalUpdateStmt->bindParam(':cleared_by', $current_user_id, PDO::PARAM_INT);
                    $finalUpdateStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
                    $finalUpdateStmt->execute();
                }
            }
            
            // Commit transaction
            $pdo->commit();
            
            $_SESSION['success'] = 'Fine deleted successfully!';
        } else {
            // Rollback transaction on failure
            $pdo->rollBack();
            $_SESSION['errors']['general'] = 'Failed to delete fine.';
        }
    } catch (Exception $e) {
        // Rollback transaction on exception
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Delete Fine Error: " . $e->getMessage());
        $_SESSION['errors']['general'] = 'An error occurred: ' . $e->getMessage();
    }
} else {
    $_SESSION['errors']['general'] = 'Database connection failed.';
}

// Redirect back to the fines list
header('Location: ../fine-list.php');
exit;