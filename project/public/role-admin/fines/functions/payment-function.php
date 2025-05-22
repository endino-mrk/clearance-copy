<?php
// File: functions/payment-functions.php

require_once __DIR__ . '/../../../../config/bootstrap.php';
require_once __DIR__ . '/../../../../config/database.php';

function recordPayment($fine_id, $amount_paid, $receipt_no, $date_paid, $recorded_by) {
    $pdo = connect_db();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }

    try {
        $pdo->beginTransaction();

        // 1. Get fine details
        $stmt = $pdo->prepare("
            SELECT rf.resident_fine_id, rf.occupancy_id, rf.fine_id, f.amount, 
                   COALESCE(rf.amount_paid, 0) as amount_paid, rf.status 
            FROM resident_fines rf
            JOIN fines f ON rf.fine_id = f.fine_id
            WHERE rf.resident_fine_id = :fine_id
        ");
        $stmt->bindParam(':fine_id', $fine_id, PDO::PARAM_INT);
        $stmt->execute();
        $fine = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fine) {
            throw new Exception("Fine record not found");
        }

        // Calculate new amount and status
        $new_amount_paid = floatval($fine['amount_paid']) + floatval($amount_paid);
        $new_status = 'Partially Paid';

        if ($new_amount_paid >= $fine['amount']) {
            $new_status = 'Paid';
            $new_amount_paid = $fine['amount']; // Ensure we don't overpay
        }

        // 2. Update fine record
        $updateStmt = $pdo->prepare("
            UPDATE resident_fines 
            SET amount_paid = :amount_paid, 
                status = :status,
                date_paid = :date_paid,
                updated_by = :updated_by,
                updated_at = NOW()
            WHERE resident_fine_id = :fine_id
        ");
        $updateStmt->bindParam(':amount_paid', $new_amount_paid);
        $updateStmt->bindParam(':status', $new_status);
        $updateStmt->bindParam(':date_paid', $date_paid);
        $updateStmt->bindParam(':updated_by', $recorded_by, PDO::PARAM_INT);
        $updateStmt->bindParam(':fine_id', $fine_id, PDO::PARAM_INT);
        $updateStmt->execute();

        // 3. Record payment in payment history table (if it exists)
        if (tableExists($pdo, 'fine_payments')) {
            $paymentStmt = $pdo->prepare("
                INSERT INTO fine_payments 
                (resident_fine_id, amount, receipt_no, date_paid, recorded_by, date_recorded)
                VALUES (:resident_fine_id, :amount, :receipt_no, :date_paid, :recorded_by, NOW())
            ");
            $paymentStmt->bindParam(':resident_fine_id', $fine_id, PDO::PARAM_INT);
            $paymentStmt->bindParam(':amount', $amount_paid);
            $paymentStmt->bindParam(':receipt_no', $receipt_no);
            $paymentStmt->bindParam(':date_paid', $date_paid);
            $paymentStmt->bindParam(':recorded_by', $recorded_by, PDO::PARAM_INT);
            $paymentStmt->execute();
        }

        // 4. Update clearance status if needed
        updateClearanceStatus($fine['occupancy_id'], $pdo, $recorded_by);

        $pdo->commit();

        return ['success' => true, 'message' => 'Payment recorded successfully'];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Payment Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error recording payment: ' . $e->getMessage()];
    }
}

function updateClearanceStatus($occupancy_id, $pdo, $user_id) {
    // Check if all fines are paid
    $finesStmt = $pdo->prepare("
        SELECT COUNT(*) as unpaid_count
        FROM resident_fines
        WHERE occupancy_id = :occupancy_id
        AND status != 'Paid'
        AND status != 'Waived'
    ");
    $finesStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
    $finesStmt->execute();
    $finesResult = $finesStmt->fetch(PDO::FETCH_ASSOC);

    $fine_status = ($finesResult['unpaid_count'] > 0) ? 'Pending' : 'Cleared';

    // Update fine status in clearance record
    $updateStmt = $pdo->prepare("
        UPDATE clearance_records
        SET fine_status = :fine_status,
            updated_at = NOW()
        WHERE occupancy_id = :occupancy_id
    ");
    $updateStmt->bindParam(':fine_status', $fine_status);
    $updateStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
    $updateStmt->execute();

    // Check if all clearance requirements are met
    $clearanceStmt = $pdo->prepare("
        SELECT * FROM clearance_records
        WHERE occupancy_id = :occupancy_id
        AND rental_fee_status = 'Cleared'
        AND fine_status = 'Cleared'
        AND room_status = 'Cleared'
        AND document_status = 'Cleared'
    ");
    $clearanceStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
    $clearanceStmt->execute();

    if ($clearanceStmt->rowCount() > 0) {
        // All requirements met, update overall status
        $finalStmt = $pdo->prepare("
            UPDATE clearance_records
            SET status = 'Cleared',
                date_cleared = NOW(),
                cleared_by = :user_id
            WHERE occupancy_id = :occupancy_id
        ");
        $finalStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $finalStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
        $finalStmt->execute();
    }
}

// Helper function to check if a table exists
function tableExists($pdo, $table) {
    try {
        $result = $pdo->query("SHOW TABLES LIKE '{$table}'");
        return $result->rowCount() > 0;
    }
    catch (Exception $e) {
        return false;
    }
}