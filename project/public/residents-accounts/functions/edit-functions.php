<?php
// functions/edit-functions.php
require_once __DIR__ . '/../../../config/bootstrap.php';
require_once BASE_PATH . '/config/database.php';

// We expect AJAX POST request for updates now
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$response = ['success' => false, 'message' => 'Invalid request.']; // Default JSON response

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$is_ajax) {
     if ($is_ajax) { echo json_encode($response); } 
    exit;
}

$errors = [];
$input = $_POST; 

// --- Basic Validation ---
$id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    $errors['general'] = 'Invalid resident ID.';
}
if (empty(trim($input['first_name']))) {
    $errors['first_name'] = 'First name is required.';
}
if (empty(trim($input['last_name']))) {
    $errors['last_name'] = 'Last name is required.';
}
if (empty($input['email'])) {
    $errors['email'] = 'Email is required.';
} elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format.';
}
// TODO: Add check for unique email (excluding the current resident's email)

// Password validation (only if provided)
$updatePassword = false;
if (!empty($input['password'])) {
    if (strlen($input['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    } elseif ($input['password'] !== $input['password_confirmation']) {
        $errors['password_confirmation'] = 'Passwords do not match.';
    } else {
        $updatePassword = true;
    }
}

// --- Process Data ---
if (empty($errors)) {
    $pdo = connect_db();
    if ($pdo) {
        try {
             // Build the SQL query dynamically based on whether password is being updated
            $sql = "UPDATE users SET 
                        first_name = :first_name, 
                        last_name = :last_name, 
                        middle_name = :middle_name, 
                        email = :email, 
                        phone_number = :phone_number, ";
            
            if ($updatePassword) {
                $sql .= "password = :password, ";
            }

            $sql .= "updated_at = NOW() WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            
            // Bind parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':first_name', $input['first_name']);
            $stmt->bindParam(':last_name', $input['last_name']);
            $middleName = !empty(trim($input['middle_name'])) ? trim($input['middle_name']) : null;
            $phoneNumber = !empty(trim($input['phone_number'])) ? trim($input['phone_number']) : null;
            $stmt->bindParam(':middle_name', $middleName);
            $stmt->bindParam(':email', $input['email']);
            $stmt->bindParam(':phone_number', $phoneNumber);
            
            if ($updatePassword) {
                $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $hashedPassword);
            }
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                     $response = ['success' => true, 'message' => 'Resident updated successfully!'];
                } else {
                     $response = ['success' => true, 'message' => 'No changes detected.'];
                }
            } else {
                 $response['message'] = 'Failed to update resident.';
            }

        } catch (PDOException $e) {
            error_log("Update Resident Error: " . $e->getMessage());
             if ($e->getCode() == '23000') { 
                 $errors['email'] = 'This email address is already registered by another resident.';
                 $response['errors'] = $errors;
                 $response['message'] = 'Update failed due to duplicate email.';
             } else {
                 $response['message'] = 'An unexpected database error occurred.';
             }
             $response['success'] = false; 
        }
    } else {
         $response['message'] = 'Database connection failed.';
    }
} else {
     // Validation failed
     $response['success'] = false;
     $response['message'] = 'Validation failed. Please check the form.';
     $response['errors'] = $errors;
}


// --- Output JSON Response ---
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>
