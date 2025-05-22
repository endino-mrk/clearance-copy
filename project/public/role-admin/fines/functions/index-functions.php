<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/../../../config/database.php';

/**
 * Fetch all fine records with related information
 */
function getFineRecords() {
    $fines = [];
    
    try {
        $pdo = connect_db();
        if (!$pdo) {
            return $fines;
        }
        
        $sql = "SELECT rf.resident_fine_id, rf.occupancy_id, rf.fine_id, f.name as reason, 
                       f.description, f.amount, rf.status, rf.violation_date, 
                       rf.date_issued, rf.date_paid, rf.updated_at,
                       r.resident_id, CONCAT(u.first_name, ' ', u.last_name) as resident_name,
                       rm.number as room, rc.name as building
                FROM resident_fines rf
                JOIN fines f ON rf.fine_id = f.fine_id
                JOIN resident_occupancy ro ON rf.occupancy_id = ro.occupancy_id
                JOIN residents r ON ro.resident_id = r.resident_id
                JOIN users u ON r.user_id = u.user_id
                JOIN rooms rm ON ro.room_id = rm.room_id
                ORDER BY rf.date_issued DESC";
        
        $stmt = $pdo->query($sql);
        $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the data for display
        foreach ($fines as &$fine) {
            // Calculate due date (15 days after issue date by default)
            if (!empty($fine['date_issued'])) {
                $dueDate = date('Y-m-d', strtotime($fine['date_issued'] . ' + 15 days'));
                $fine['due_date'] = $dueDate;
            }
            
            // Prepare display values
            $fine['formatted_amount'] = formatCurrency($fine['amount']);
            $fine['formatted_issue_date'] = formatDate($fine['date_issued']);
            $fine['formatted_due_date'] = formatDate($fine['due_date'] ?? null);
            $fine['formatted_payment_date'] = formatDate($fine['date_paid'] ?? null);
            
            // Check if fine is overdue but not marked as such
            if ($fine['status'] === 'Unpaid' && !empty($fine['due_date'])) {
                $today = date('Y-m-d');
                if ($today > $fine['due_date']) {
                    // For display purposes only - doesn't update the database
                    $fine['status'] = 'Overdue';
                }
            }
            
            // For table display - truncate description
            if (!empty($fine['description'])) {
                $fine['short_description'] = strlen($fine['description']) > 50 
                    ? substr($fine['description'], 0, 50) . '...' 
                    : $fine['description'];
            } else {
                $fine['short_description'] = '';
            }
        }
        
        return $fines;
    } catch (PDOException $e) {
        error_log("Error fetching fine records: " . $e->getMessage());
        return [];
    }
}

/**
 * Get fine statistics for dashboard
 */
function getFineStatistics() {
    $stats = [
        'total_count' => 0,
        'total_amount' => 0,
        'paid_count' => 0,
        'paid_amount' => 0,
        'unpaid_count' => 0,
        'unpaid_amount' => 0,
        'overdue_count' => 0,
        'overdue_amount' => 0,
        'waived_count' => 0,
        'waived_amount' => 0,
        'pending_count' => 0,
        'pending_amount' => 0
    ];
    
    try {
        $pdo = connect_db();
        if (!$pdo) {
            return $stats;
        }
        
        // Get all fines
        $sql = "SELECT rf.status, f.amount, rf.date_issued, rf.date_paid 
                FROM resident_fines rf
                JOIN fines f ON rf.fine_id = f.fine_id";
        
        $stmt = $pdo->query($sql);
        $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate statistics
        foreach ($fines as $fine) {
            $stats['total_count']++;
            $stats['total_amount'] += $fine['amount'];
            
            $status = $fine['status'];
            
            // Check if fine is actually overdue
            if ($status === 'Unpaid' && !empty($fine['date_issued'])) {
                $dueDate = date('Y-m-d', strtotime($fine['date_issued'] . ' + 15 days'));
                $today = date('Y-m-d');
                
                if ($today > $dueDate) {
                    $status = 'Overdue';
                }
            }
            
            switch ($status) {
                case 'Paid':
                    $stats['paid_count']++;
                    $stats['paid_amount'] += $fine['amount'];
                    break;
                case 'Unpaid':
                    $stats['unpaid_count']++;
                    $stats['unpaid_amount'] += $fine['amount'];
                    break;
                case 'Overdue':
                    $stats['overdue_count']++;
                    $stats['overdue_amount'] += $fine['amount'];
                    break;
                case 'Waived':
                    $stats['waived_count']++;
                    $stats['waived_amount'] += $fine['amount'];
                    break;
            }
        }
        
        // Calculate collection rate
        $stats['collection_rate'] = ($stats['total_amount'] - $stats['waived_amount']) > 0 
            ? round(($stats['paid_amount'] / ($stats['total_amount'] - $stats['waived_amount'])) * 100) 
            : 0;
            
        // Combine unpaid and overdue for "outstanding" calculation
        $stats['outstanding_count'] = $stats['unpaid_count'] + $stats['overdue_count'];
        $stats['outstanding_amount'] = $stats['unpaid_amount'] + $stats['overdue_amount'];
        
        return $stats;
    } catch (PDOException $e) {
        error_log("Error calculating fine statistics: " . $e->getMessage());
        return $stats;
    }
}