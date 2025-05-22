<?php 

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$clearance_id = 1;
$pdo = connect_db();
if ($pdo) {
    try {
        // Query to fetch clearance record with resident details
        $sql = "SELECT cr.clearance_id, cr.status, cr.rental_fee_status, cr.fine_status, 
                       cr.room_status, cr.document_status, cr.date_cleared, cr.updated_at,
                       r.resident_id, r.student_id, 
                       u.first_name, u.last_name,
                       ro.occupancy_id, ro.room_status as occupancy_room_status,
                       rm.number as room_number, rm.monthly_rental,
                       s.academic_year, s.term, s.start_date, s.end_date
                FROM clearance_records cr
                JOIN resident_occupancy ro ON cr.occupancy_id = ro.occupancy_id
                JOIN residents r ON cr.resident_id = r.resident_id
                JOIN users u ON r.user_id = u.user_id
                JOIN rooms rm ON ro.room_id = rm.room_id
                JOIN semesters s ON ro.semester_id = s.semester_id
                WHERE cr.clearance_id = :clearance_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':clearance_id', $clearance_id, PDO::PARAM_INT);
        $stmt->execute();

        $clearanceData = $stmt->fetch(PDO::FETCH_ASSOC);

        foreach ($clearanceData as $key => $value) {
            echo "<strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars($value) . "<br>";
        }

    } catch(PDOException $e) {
        //
    }
}
                


?>