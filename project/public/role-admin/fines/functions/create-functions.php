<?php
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

// --- Log the incoming data to help debug ---
error_log("Fine creation - POST data: " . print_r($input, true));

// --- Enhanced Validation for Resident ID/Student ID ---
if (empty($input['resident']) && empty($input['resident_id'])) {
    $errors['resident_id'] = 'Resident is required.';
} else {
    // Get the resident ID from either the direct field or by searching with student ID
    $resident_id = !empty($input['resident_id']) ? $input['resident_id'] : null;
    $student_id = !empty($input['resident']) ? trim($input['resident']) : null;
    
    // If we have a student ID but no resident ID, look up the resident by student ID
    if ($student_id && !$resident_id) {
        error_log("Looking up resident by student ID: $student_id");
        
        $pdo = connect_db();
        if ($pdo) {
            try {
                // First try exact match
                $stmt = $pdo->prepare("
                    SELECT r.resident_id
                    FROM residents r
                    JOIN users u ON r.user_id = u.user_id
                    WHERE r.student_id = :student_id 
                    AND r.active = 1 
                    AND u.active = 1
                    LIMIT 1
                ");
                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
                $stmt->execute();
                
                $resident = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // If no exact match, try LIKE search
                if (!$resident) {
                    $stmt = $pdo->prepare("
                        SELECT r.resident_id
                        FROM residents r
                        JOIN users u ON r.user_id = u.user_id
                        WHERE r.student_id LIKE :student_id 
                        AND r.active = 1 
                        AND u.active = 1
                        LIMIT 1
                    ");
                    $search_pattern = "%$student_id%";
                    $stmt->bindParam(':student_id', $search_pattern, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    $resident = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                
                if ($resident) {
                    $resident_id = $resident['resident_id'];
                    $input['resident_id'] = $resident_id; // Update the input array
                    error_log("Found resident ID: $resident_id for student ID: $student_id");
                } else {
                    $errors['resident_id'] = 'No resident found with student ID: ' . $student_id;
                    error_log("No resident found with student ID: $student_id");
                }
            } catch (PDOException $e) {
                error_log("Error looking up resident: " . $e->getMessage());
                $errors['resident_id'] = 'Error looking up resident by Student ID.';
            }
        }
    }
    
    // Verify resident exists and is active
    if ($resident_id) {
        $pdo = connect_db();
        if ($pdo) {
            $stmt = $pdo->prepare("
                SELECT r.resident_id, r.student_id 
                FROM residents r
                JOIN users u ON r.user_id = u.user_id
                WHERE r.resident_id = :resident_id 
                AND r.active = 1 
                AND u.active = 1
            ");
            $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                $errors['resident_id'] = 'Selected resident is not active or does not exist.';
                error_log("Resident ID $resident_id is not active or does not exist");
            }
        }
    } elseif (empty($errors['resident_id'])) {
        $errors['resident_id'] = 'Valid resident is required.';
    }
}

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

if (empty($input['violation_date'])) {
    $errors['violation_date'] = 'Violation date is required.';
} else {
    // Validate date format and range
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
            // Begin a transaction
            $pdo->beginTransaction();
            
            // First, get the occupancy_id for the resident
            $stmt = $pdo->prepare("
            SELECT ro.occupancy_id 
            FROM resident_occupancy ro
            JOIN semesters s ON ro.semester_id = s.semester_id
            WHERE ro.resident_id = :resident_id 
            AND ro.active = 1 
            AND s.active = 1
            LIMIT 1
            ");
            $stmt->bindParam(':resident_id', $input['resident_id'], PDO::PARAM_INT);
            $stmt->execute();
            $occupancy = $stmt->fetch(PDO::FETCH_ASSOC);

            // If no active occupancy found, look for any occupancy in the current semester
            if (!$occupancy) {
                $stmt = $pdo->prepare("
                    SELECT ro.occupancy_id 
                    FROM resident_occupancy ro
                    JOIN semesters s ON ro.semester_id = s.semester_id
                    WHERE ro.resident_id = :resident_id 
                    AND s.active = 1
                    ORDER BY ro.updated_at DESC
                    LIMIT 1
                ");
                $stmt->bindParam(':resident_id', $input['resident_id'], PDO::PARAM_INT);
                $stmt->execute();
                $occupancy = $stmt->fetch(PDO::FETCH_ASSOC);

                // If found, activate it
                if ($occupancy) {
                    $updateStmt = $pdo->prepare("
                        UPDATE resident_occupancy
                        SET active = 1, room_status = 'Not Vacated'
                        WHERE occupancy_id = :occupancy_id
                    ");
                    $updateStmt->bindParam(':occupancy_id', $occupancy['occupancy_id'], PDO::PARAM_INT);
                    $updateStmt->execute();
                    
                    // Log the activation
                    error_log("Reactivated occupancy ID: " . $occupancy['occupancy_id'] . " for resident ID: " . $input['resident_id']);
                }
            }

            // If still no occupancy found, create a new one
            if (!$occupancy) {
                // Get the current active semester
                $stmt = $pdo->prepare("SELECT semester_id FROM semesters WHERE active = 1 LIMIT 1");
                $stmt->execute();
                $semester = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$semester) {
                    throw new Exception("No active semester found. Please set an active semester first.");
                }
                
                // Find a room to use
                $roomStmt = $pdo->prepare("SELECT room_id FROM rooms WHERE number = 'A1' LIMIT 1");
                $roomStmt->execute();
                $room = $roomStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$room) {
                    throw new Exception("Room A1 not found. Please create it first.");
                }
                
                // Get table structure to ensure we're matching columns correctly
                $columnStmt = $pdo->prepare("DESCRIBE resident_occupancy");
                $columnStmt->execute();
                $columns = $columnStmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Remove auto-increment column (typically the primary key)
                $columns = array_diff($columns, ['occupancy_id']);
                
                // Build the SQL dynamically based on the actual columns
                $columnList = implode(', ', $columns);
                $placeholders = ':' . implode(', :', $columns);
                
                $sql = "INSERT INTO resident_occupancy ($columnList) VALUES ($placeholders)";
                $stmt = $pdo->prepare($sql);
                // Create data array with values for all columns
                $data = [
                    'semester_id' => $semester['semester_id'],
                    'resident_id' => $input['resident_id'],
                    'room_id' => $room['room_id'],
                    'room_status' => 'Not Vacated',
                    'rental_balance' => 0.00,
                    'active' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Bind all parameters
                foreach ($data as $column => $value) {
                    if (in_array($column, $columns)) {
                        $stmt->bindValue(":$column", $value);
                    }
                }
                
                $stmt->execute();
                $occupancy_id = $pdo->lastInsertId();
                
                // Log the creation of temporary occupancy
                error_log("Created temporary occupancy ID: " . $occupancy_id . " for resident ID: " . $input['resident_id']);
                
                if (!$occupancy_id) {
                    throw new Exception("Failed to create occupancy record.");
                }
            } else {
                $occupancy_id = $occupancy['occupancy_id'];
            }
            
            // Get the fine amount and details from the fines table
            $stmt = $pdo->prepare("SELECT amount FROM fines WHERE fine_id = :fine_id");
            $stmt->bindParam(':fine_id', $input['fine_id'], PDO::PARAM_INT);
            $stmt->execute();
            $fineInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$fineInfo) {
                throw new Exception("Fine type information not found.");
            }
            
            // Insert into resident_fines table
            $sql = "INSERT INTO resident_fines 
            (occupancy_id, fine_id, status, violation_date, date_issued, issued_by) 
            VALUES 
            (:occupancy_id, :fine_id, :status, :violation_date, :date_issued, :issued_by)";
   
            $stmt = $pdo->prepare($sql);
            
            // Set all values explicitly
            $issued_by = $_SESSION['user_id'] ?? 1; // Default to 1 if not logged in (for testing)
            $status = 'Unpaid';
            $date_issued = date('Y-m-d H:i:s'); // Current datetime in MySQL format
            
            // Bind all parameters explicitly
            $stmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
            $stmt->bindParam(':fine_id', $input['fine_id'], PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':violation_date', $input['violation_date'], PDO::PARAM_STR);
            $stmt->bindParam(':date_issued', $date_issued, PDO::PARAM_STR);
            $stmt->bindParam(':issued_by', $issued_by, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $fine_id = $pdo->lastInsertId();
                
                // Check if clearance record exists
                $checkSql = "SELECT clearance_id FROM clearance_records WHERE occupancy_id = :occupancy_id LIMIT 1";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
                $checkStmt->execute();
                
                if ($checkStmt->rowCount() > 0) {
                    // Update existing clearance record
                    $updateClearanceSql = "UPDATE clearance_records 
                                          SET fine_status = 'Pending', status = 'Pending', updated_at = NOW() 
                                          WHERE occupancy_id = :occupancy_id";
                    $updateClearanceStmt = $pdo->prepare($updateClearanceSql);
                    $updateClearanceStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
                    $updateClearanceStmt->execute();
                } else {
                    // Insert a new clearance record
                    $createClearanceSql = "INSERT INTO clearance_records (
                                            resident_id, 
                                            occupancy_id, 
                                            status, 
                                            rental_fee_status, 
                                            fine_status, 
                                            room_status, 
                                            document_status
                                          ) VALUES (
                                            :resident_id,
                                            :occupancy_id,
                                            'Pending',
                                            'Pending',
                                            'Pending',
                                            'Pending',
                                            'Pending'
                                          )";
                    $createClearanceStmt = $pdo->prepare($createClearanceSql);
                    $createClearanceStmt->bindParam(':resident_id', $input['resident_id'], PDO::PARAM_INT);
                    $createClearanceStmt->bindParam(':occupancy_id', $occupancy_id, PDO::PARAM_INT);
                    $createClearanceStmt->execute();
                }
                
                // Commit the transaction
                $pdo->commit();
                
                $_SESSION['success'] = 'Fine issued successfully!';
                header('Location: ../fine-list.php');
                exit;
            } else {
                // Rollback transaction on error
                $pdo->rollBack();
                $errors['general'] = 'Failed to issue fine. Please try again.';
            }
        } catch (Exception $e) {
            // Rollback transaction on exception
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            
            error_log("Create Fine Error: " . $e->getMessage());
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
    header('Location: ../fine-list.php#addFine');
    exit;
}