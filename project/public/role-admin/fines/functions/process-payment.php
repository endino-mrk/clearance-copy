<?php
// Fixed version of process-payment.php with proper debugging and error handling

require_once __DIR__ . '/../../../../config/bootstrap.php';
require_once __DIR__ . '/../../../../config/database.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['errors']['general'] = 'Invalid request method.';
    header('Location: ../fine-list.php');
    exit;
}

$errors = [];
$input = $_POST;

// Log incoming data
error_log("Payment processing - POST data: " . print_r($input, true));

// Validate fine_id
$fine_id = filter_var($input['fine_id'] ?? null, FILTER_VALIDATE_INT);
if (!$fine_id) {
    $errors['fine_id'] = 'Invalid fine ID.';
    error_log("Invalid fine ID: " . ($input['fine_id'] ?? 'empty'));
}

// Validate amount
if (empty($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
    $errors['amount'] = 'A valid payment amount greater than 0 is required.';
    error_log("Invalid amount: " . ($input['amount'] ?? 'empty'));
}

// Validate receipt number
if (empty($input['receipt_no'])) {
    $errors['receipt_no'] = 'Receipt number is required.';
    error_log("Receipt number is empty");
}

// Validate payment date
if (empty($input['date_paid'])) {
    $errors['date_paid'] = 'Payment date is required.';
    error_log("Payment date is empty");
} else {
    // Validate date format
    $date = date_create($input['date_paid']);
    if (!$date) {
        $errors['date_paid'] = 'Invalid date format.';
        error_log("Invalid date format: " . $input['date_paid']);
    }
}

// Process payment if validation passes
if (empty($errors)) {
    error_log("Validation passed, processing payment");
    
    $pdo = connect_db();
    if (!$pdo) {
        error_log("Database connection failed");
        $_SESSION['errors']['general'] = 'Database connection failed.';
        header('Location: ../fine-list.php');
        exit;
    }
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Get the current fine details
        $stmt = $pdo->prepare("
            SELECT rf.resident_fine_id, rf.occupancy_id, rf.status, 
                   f.amount, COALESCE(rf.amount_paid, 0) as amount_paid
            FROM resident_fines rf
            JOIN fines f ON rf.fine_id = f.fine_id
            WHERE rf.resident_fine_id = :fine_id
        ");
        $stmt->bindParam(':fine_id', $fine_id, PDO::PARAM_INT);
        $stmt->execute();
        $fine = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$fine) {
            throw new Exception("Fine record not found.");
        }
        
        error_log("Fine details: " . print_r($fine, true));
        
        // Calculate new amount paid and determine status
        $amount_paid = floatval($input['amount']);
        $new_total_paid = floatval($fine['amount_paid']) + $amount_paid;
        $fine_amount = floatval($fine['amount']);
        
        // Validate that payment doesn't exceed the fine amount
        if ($new_total_paid > $fine_amount) {
            throw new Exception("Payment amount would exceed the fine amount.");
        }
        
        // Determine new status
        $new_status = 'Partially Paid';
        if ($new_total_paid >= $fine_amount) {
            $new_status = 'Paid';
            $new_total_paid = $fine_amount; // Cap at the fine amount
        }
        
        error_log("New payment info: amount_paid=$amount_paid, new_total_paid=$new_total_paid, new_status=$new_status");
        
        // Update the fine record
        $updateSql = "
            UPDATE resident_fines 
            SET status = :status,
                amount_paid = :amount_paid,
                date_paid = :date_paid,
                updated_by = :updated_by,
                updated_at = NOW()
            WHERE resident_fine_id = :fine_id
        ";
        
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':status', $new_status);
        $updateStmt->bindParam(':amount_paid', $new_total_paid);
        $updateStmt->bindParam(':date_paid', $input['date_paid']);
        $updateStmt->bindParam(':fine_id', $fine_id, PDO::PARAM_INT);
        
        // Current user ID
        $updated_by = $_SESSION['user_id'] ?? 1; // Default to 1 if not logged in (for testing)
        $updateStmt->bindParam(':updated_by', $updated_by, PDO::PARAM_INT);
        
        $updateStmt->execute();
        
        // Record payment in payment history
        $paymentSql = "
            INSERT INTO rental_fee_payments 
            (occupancy_id, amount, receipt_no, date_paid, recorded_by, date_recorded)
            VALUES (:occupancy_id, :amount, :receipt_no, :date_paid, :recorded_by, NOW())
        ";
        
        $paymentStmt = $pdo->prepare($paymentSql);
        $paymentStmt->bindParam(':occupancy_id', $fine['occupancy_id'], PDO::PARAM_INT);
        $paymentStmt->bindParam(':amount', $amount_paid);
        $paymentStmt->bindParam(':receipt_no', $input['receipt_no']);
        $paymentStmt->bindParam(':date_paid', $input['date_paid']);
        $paymentStmt->bindParam(':recorded_by', $updated_by, PDO::PARAM_INT);
        $paymentStmt->execute();
        
        // Update clearance status if needed
        $occupancy_id = $fine['occupancy_id'];
        
        // Check if all fines are cleared
        $checkSql = "
            SELECT COUNT(*) as unpaid_count 
            FROM resident_fines 
            WHERE occupancy_id = :occupancy_id 
            AND status NOT IN ('Paid', 'Waived')
        ";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        $fineStatus = ($result['unpaid_count'] > 0) ? 'Pending' : 'Cleared';
        
        // Update clearance_records
        $updateClearanceSql = "
            UPDATE clearance_records 
            SET fine_status = :fine_status,
                updated_at = NOW() 
            WHERE occupancy_id = :occupancy_id
        ";
        $updateClearanceStmt = $pdo->prepare($updateClearanceSql);
        $updateClearanceStmt->bindParam(':fine_status', $fineStatus);
        $updateClearanceStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
        $updateClearanceStmt->execute();
        
        // Check if all clearance requirements are cleared
        if ($fineStatus === 'Cleared') {
            $checkClearanceSql = "
                SELECT * FROM clearance_records 
                WHERE occupancy_id = :occupancy_id 
                AND (rental_fee_status = 'Pending' 
                    OR fine_status = 'Pending' 
                    OR room_status = 'Pending' 
                    OR document_status = 'Pending')
            ";
            $checkClearanceStmt = $pdo->prepare($checkClearanceSql);
            $checkClearanceStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
            $checkClearanceStmt->execute();
            
            if ($checkClearanceStmt->rowCount() === 0) {
                // All requirements cleared
                $finalUpdateSql = "
                    UPDATE clearance_records 
                    SET status = 'Cleared', 
                        date_cleared = NOW(), 
                        cleared_by = :cleared_by 
                    WHERE occupancy_id = :occupancy_id
                ";
                $finalUpdateStmt = $pdo->prepare($finalUpdateSql);
                $finalUpdateStmt->bindParam(':cleared_by', $updated_by, PDO::PARAM_INT);
                $finalUpdateStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
                $finalUpdateStmt->execute();
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        error_log("Payment processed successfully");
        $_SESSION['success'] = 'Payment recorded successfully!';
        
    } catch (Exception $e) {
        // Rollback transaction on exception
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Payment Error: " . $e->getMessage());
        $_SESSION['errors']['general'] = 'An error occurred: ' . $e->getMessage();
    }
}

// If we got here with errors, store them in session
if (!empty($errors)) {
    error_log("Payment processing failed with errors: " . print_r($errors, true));
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $input; // Store input for repopulating the form
}

// Redirect back to the fines list
header('Location: ../fine-list.php');
exit;