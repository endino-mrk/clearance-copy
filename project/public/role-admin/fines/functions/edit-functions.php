<?php
// Updated edit-functions.php with simplified functionality

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

// --- Basic Validation ---
$fine_id = filter_var($input['fine_id'] ?? null, FILTER_VALIDATE_INT);
if (!$fine_id) {
    $errors['general'] = 'Invalid fine ID.';
}

// Validate fine type ID
if (empty($input['fine_id'])) {
    $errors['fine_id'] = 'Fine type is required.';
} else {
    // Verify fine type exists
    $pdo = connect_db();
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT fine_id FROM fines WHERE fine_id = :fine_id");
        $stmt->bindParam(':fine_id', $input['fine_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            $errors['fine_id'] = 'Selected fine type does not exist.';
        }
    }
}

// Validate violation date
if (empty($input['violation_date'])) {
    $errors['violation_date'] = 'Violation date is required.';
} else {
    // Validate date format
    $date = date_create($input['violation_date']);
    if (!$date) {
        $errors['violation_date'] = 'Invalid date format.';
    } else {
        // Check if date is in the future
        $today = new DateTime();
        if ($date > $today) {
            $errors['violation_date'] = 'Violation date cannot be in the future.';
        }
        
        // Check if date is too far in the past (optional, e.g., more than 1 year)
        $oneYearAgo = (new DateTime())->modify('-1 year');
        if ($date < $oneYearAgo) {
            $errors['violation_date'] = 'Violation date is too far in the past.';
        }
    }
}

// --- Process Data ---
if (empty($errors)) {
    $pdo = connect_db();
    if ($pdo) {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
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
            
            // Check if description column exists
            $hasDescriptionColumn = columnExists($pdo, 'resident_fines', 'description');
            error_log("Description column exists: " . ($hasDescriptionColumn ? 'Yes' : 'No'));
            
            // Get current fine data to check for changes
            $sql = "SELECT rf.fine_id, rf.violation_date, rf.occupancy_id, r.student_id";
            if ($hasDescriptionColumn) {
                $sql .= ", rf.description";
            }
            $sql .= " FROM resident_fines rf
                      JOIN resident_occupancy ro ON rf.occupancy_id = ro.occupancy_id
                      JOIN residents r ON ro.resident_id = r.resident_id
                      WHERE rf.resident_fine_id = :fine_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':fine_id', $fine_id, PDO::PARAM_INT);
            $stmt->execute();
            $currentFine = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$currentFine) {
                throw new Exception("Fine record not found. ID: " . $fine_id);
            }
            
            // Debug log
            error_log("Updating fine ID: " . $fine_id . " with fine_type: " . $input['fine_id'] . " and date: " . $input['violation_date']);
            
            // Build the update SQL based on available columns
            $sql = "UPDATE resident_fines SET fine_id = :fine_type_id, violation_date = :violation_date";
            
            // Only include description in update if the column exists
            if ($hasDescriptionColumn) {
                $sql .= ", description = :description";
            }
            
            $sql .= ", updated_by = :updated_by, updated_at = NOW() WHERE resident_fine_id = :resident_fine_id";
            
            $stmt = $pdo->prepare($sql);
            
            // Bind parameters - use different variable names to avoid confusion
            $stmt->bindParam(':fine_type_id', $input['fine_id'], PDO::PARAM_INT);
            $stmt->bindParam(':violation_date', $input['violation_date'], PDO::PARAM_STR);
            $stmt->bindParam(':resident_fine_id', $fine_id, PDO::PARAM_INT);
            
            // Only bind description if the column exists
            if ($hasDescriptionColumn) {
                $stmt->bindParam(':description', $input['description']);
            }
            
            // Current user ID
            $updated_by = $_SESSION['user_id'] ?? 1; // Default to 1 if not logged in (for testing)
            $stmt->bindParam(':updated_by', $updated_by, PDO::PARAM_INT);
            
            // Execute the update
            if ($stmt->execute()) {
                // If student ID was provided and is different from current, update it
                if (!empty($input['student_id']) && isset($currentFine['student_id']) && $input['student_id'] !== $currentFine['student_id']) {
                    // Find the resident ID from the occupancy record
                    $residentSql = "SELECT resident_id FROM resident_occupancy WHERE occupancy_id = :occupancy_id";
                    $residentStmt = $pdo->prepare($residentSql);
                    $residentStmt->bindParam(':occupancy_id', $currentFine['occupancy_id'], PDO::PARAM_INT);
                    $residentStmt->execute();
                    $residentId = $residentStmt->fetchColumn();
                    
                    if ($residentId) {
                        // Update the student ID
                        $updateStudentIdSql = "UPDATE residents SET student_id = :student_id WHERE resident_id = :resident_id";
                        $updateStudentIdStmt = $pdo->prepare($updateStudentIdSql);
                        $updateStudentIdStmt->bindParam(':student_id', $input['student_id'], PDO::PARAM_STR);
                        $updateStudentIdStmt->bindParam(':resident_id', $residentId, PDO::PARAM_INT);
                        $updateStudentIdStmt->execute();
                    }
                }
                
                // Commit transaction
                $pdo->commit();
                
                $_SESSION['success'] = 'Fine updated successfully!';
                header('Location: ../fine-list.php');
                exit;
            } else {
                // Rollback transaction on failure
                $pdo->rollBack();
                $errors['general'] = 'Failed to update fine.';
                error_log("SQL execution failed for fine update");
            }
        } catch (Exception $e) {
            // Rollback transaction on exception
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            
            error_log("Update Fine Error: " . $e->getMessage());
            $errors['general'] = 'An error occurred: ' . $e->getMessage();
        }
    } else {
        $errors['general'] = 'Database connection failed.';
    }
}

// If we got here, there were errors - store in session and redirect back
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $input; // Store input for repopulating the form
    header("Location: ../edit-fine.php?id=$fine_id");
    exit;
}