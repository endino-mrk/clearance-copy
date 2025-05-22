<?php
if (isset($_POST['record_payment'])) {
    include 'config.php';

    $student_id = $_POST['student_id'];
    $amount = $_POST['amount'];
    $receipt_no = $_POST['receipt_no'];
    $date_paid = $_POST['date_paid'];
    $recorded_by = $_SESSION['user_id'];
    $date_recorded = date('Y-m-d');

    // Fetch occupancy_id
    $query = "SELECT o.occupancy_id
              FROM occupancy o
              JOIN resident r ON o.resident_id = r.resident_id
              WHERE r.student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->bind_result($occupancy_id);
    $stmt->fetch();
    $stmt->close();

    if ($occupancy_id) {
        $insert = "INSERT INTO rental_fee_payment (occupancy_id, amount, receipt_no, date_paid, recorded_by, date_recorded)
                   VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("idssis", $occupancy_id, $amount, $receipt_no, $date_paid, $recorded_by, $date_recorded);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Payment recorded successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-warning'>Student not found or no occupancy record.</div>";
    }

    $conn->close();
}
?>