<?php
// File: /public/residents-accounts/functions/resident-process.php
require_once __DIR__ . '/../../../../config/bootstrap.php';
require_once __DIR__ . '/../../../../config/database.php';

/**
 * Process a newly created resident account
 * 
 * @param int $user_id The user ID of the newly created account
 * @param string $student_id The student ID (required)
 * @param string $room_number The room number of a student (required)
 * @return array|bool Success status and messages
 */
// function processNewResident($user_id, $student_id, $room_id) {
//     // echo "ENTERED processNewResident";

//     $pdo = connect_db();
//     if (!$pdo) {
//         return [
//             'success' => false,
//             'message' => 'Database connection failed'
//         ];
//     }
    
//     try {
//         // echo "BEFORE database transaction";
//         // Start transaction
//         $pdo->beginTransaction();
//         // echo "AFTER database transaction";
        
//         // 1. Check for active semester
//         $stmt = $pdo->query("SELECT semester_id FROM semesters WHERE active = 1 LIMIT 1");
//         $semester = $stmt->fetch(PDO::FETCH_ASSOC);
        
//         if (!$semester) {
//             // No active semester
//             $pdo->rollBack();
//             return [
//                 'success' => false,
//                 'message' => 'No active semester found. Please activate a semester first.'
//             ];
//         }
        
//         // 2. Create resident record
//         $stmt = $pdo->prepare("INSERT INTO residents (user_id, student_id, active) VALUES (?, ?, 1)");
//         $stmt->execute([$user_id, $student_id]);
//         $resident_id = $pdo->lastInsertId();
        

//         // 3. Find room_id of resident's room
//         $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_id = :room_id");
//         $stmt->bindParam(":room_id", $room_id);
//         $stmt->execute();

//         // Fetch room
//         $room= $stmt->fetch(PDO::FETCH_ASSOC);

//         if (!$room) {
//             $pdo->rollBack();
//             return [
//                 'success' => false,
//                 'message' => 'Room not found. Please check the room number.'
//             ];
//         }
        

//         // 4. Calculate rental balance
//         $rental_balance = $room['monthly_rental'] * 5;


        
//         // 4. Create occupancy
//         $stmt = $pdo->prepare("
//             INSERT INTO resident_occupancy 
//             (semester_id, resident_id, room_id, room_status, rental_balance, active)
//             VALUES (?, ?, ?, 'Not Vacated', ?, 1)
//         ");
//         $stmt->execute([$semester['semester_id'], $resident_id, $room['room_id'], $rental_balance]);
//         $occupancy_id = $pdo->lastInsertId();
        
//         // 5. Create document submissions (simplified)
//         $stmt = $pdo->query("SELECT document_id FROM documents");
//         $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
//         foreach ($documents as $document) {
//             $stmt = $pdo->prepare("
//                 INSERT INTO document_submissions 
//                 (occupancy_id, document_id, submitted)
//                 VALUES (?, ?, 'False')
//             ");
//             $stmt->execute([$occupancy_id, $document['document_id']]);
//         }
        
//         // 6. Create clearance record
//         $stmt = $pdo->prepare("
//             INSERT INTO clearance_records 
//             (resident_id, occupancy_id, status, rental_fee_status, fine_status, room_status, document_status)
//             VALUES (?, ?, 'Pending', 'Pending', 'Pending', 'Pending', 'Pending')
//         ");
//         $stmt->execute([$resident_id, $occupancy_id]);
        
//         // Commit transaction
//         $pdo->commit();
        
//         return [
//             'success' => true,
//             'message' => 'Resident processed successfully',
//             'data' => [
//                 'resident_id' => $resident_id,
//                 'occupancy_id' => $occupancy_id
//             ]
//         ];
        
//     } catch (PDOException $e) {
//         // Roll back transaction on error
//         if ($pdo->inTransaction()) {
//             $pdo->rollBack();
//         }
        
//         error_log("Resident processing error: " . $e->getMessage());
        
//         return [
//             'success' => false,
//             'message' => 'An error occurred while processing the resident'
//         ];
//     }
// }


function processNewResident($user_id, $student_id, $room_id) {
    // echo "ENTERED processNewResident";

    $pdo = connect_db();
    if (!$pdo) {
        return [
            'success' => false,
            'message' => 'Database connection failed'
        ];
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // 1. Check for active semester
        $stmt = $pdo->query("SELECT semester_id FROM semesters WHERE active = 1 LIMIT 1");
        $semester = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$semester) {
            // No active semester
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => 'No active semester found. Please activate a semester first.'
            ];
        }
        
        // 2. Create resident record
        $stmt = $pdo->prepare("INSERT INTO residents (user_id, student_id, active) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $student_id]);
        $resident_id = $pdo->lastInsertId();
        

        // 3. Find room_id of resident's room
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_id = :room_id");
        $stmt->bindParam(":room_id", $room_id);
        $stmt->execute();

        // Fetch room
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Room not found. Please check the room number.'
            ];
        }
        
        // 4. Calculate rental balance
        $rental_balance = $room['monthly_rental'] * 5;
        
        // 4. Create occupancy
        $stmt = $pdo->prepare("
            INSERT INTO resident_occupancy 
            (semester_id, resident_id, room_id, room_status, rental_balance, active)
            VALUES (?, ?, ?, 'Not Vacated', ?, 1)
        ");
        $stmt->execute([$semester['semester_id'], $resident_id, $room['room_id'], $rental_balance]);
        $occupancy_id = $pdo->lastInsertId();
        
        // 5. Create document submissions (simplified)
        $stmt = $pdo->query("SELECT document_id FROM documents");
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($documents as $document) {
            $stmt = $pdo->prepare("
                INSERT INTO document_submissions 
                (occupancy_id, document_id, submitted)
                VALUES (?, ?, 'False')
            ");
            $stmt->execute([$occupancy_id, $document['document_id']]);
        }
        
        // 6. Create clearance record - UPDATED FOR NEW SCHEMA
        // Get semester end date to use as the clearance due date
        $stmt = $pdo->prepare("SELECT end_date FROM semesters WHERE semester_id = ?");
        $stmt->execute([$semester['semester_id']]);
        $semesterInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // // Set due date to be 1 week before semester end
        // $dueDate = date('Y-m-d H:i:s', strtotime($semesterInfo['end_date'] . ' -1 week'));
        
        $stmt = $pdo->prepare("
            INSERT INTO clearance_records 
            (resident_id, occupancy_id, status, rental_fee_status, fine_status, room_status, document_status, due_date, created_at)
            VALUES (?, ?, 'Pending', 'Pending', 'Pending', 'Pending', 'Pending', ?, NOW())
        ");
        $stmt->execute([$resident_id, $occupancy_id, $semesterInfo['end_date']]);
        
        // Commit transaction
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Resident processed successfully',
            'data' => [
                'resident_id' => $resident_id,
                'occupancy_id' => $occupancy_id
            ]
        ];
        
    } catch (PDOException $e) {
        // Roll back transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Resident processing error: " . $e->getMessage());
        
        return [
            'success' => false,
            'message' => 'An error occurred while processing the resident: ' . $e->getMessage()
        ];
    }
}

function calculateRentalBalance($monthly_rental) {
    return $monthly_rental * 5;
}