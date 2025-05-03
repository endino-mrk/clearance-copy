<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/config/database.php';

// Determine if the request is AJAX
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$response = [
    'success' => false,
    'message' => '',
    'errors' => [],
    'id' => null
];

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($is_ajax) {
        $response['message'] = 'Invalid request method.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        header('Location: ../resident-account-list.php');
        exit;
    }
}

$errors = [];
$input = $_POST; // Use a copy for potential modification

// --- Basic Validation ---
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

//  Add check for unique email in the database
if (empty($input['password'])) {
    $errors['password'] = 'Password is required.';
} elseif (strlen($input['password']) < 8) { // Example: Minimum length
    $errors['password'] = 'Password must be at least 8 characters long.';
}
if ($input['password'] !== $input['password_confirmation']) {
    $errors['password_confirmation'] = 'Passwords do not match.';
}

// --- Process Data ---
if (empty($errors)) {
    $pdo = connect_db();
    if ($pdo) {
        try {
            // Hash the password
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (first_name, last_name, middle_name, email, phone_number, password, created_at, updated_at) 
                    VALUES (:first_name, :last_name, :middle_name, :email, :phone_number, :password, NOW(), NOW())";
            
            $stmt = $pdo->prepare($sql);
            
            // Bind parameters
            $firstName = trim($input['first_name']);
            $lastName = trim($input['last_name']);
            $email = trim($input['email']);
            $middleName = !empty(trim($input['middle_name'])) ? trim($input['middle_name']) : null;
            $phoneNumber = !empty(trim($input['phone_number'])) ? trim($input['phone_number']) : null;
            
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':middle_name', $middleName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone_number', $phoneNumber);
            $stmt->bindParam(':password', $hashedPassword);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Resident created successfully!';
                $response['id'] = $pdo->lastInsertId();
                $_SESSION['success'] = $response['message'];
            } else {
                 $response['message'] = 'Failed to create resident. Please try again.';
                 $errors['general'] = $response['message'];
            }

        } catch (PDOException $e) {
            error_log("Create Resident Error: " . $e->getMessage());
            if ($e->getCode() == '23000') { 
                 $errors['email'] = 'This email address is already registered.';
                 $response['message'] = $errors['email'];
            } else {
                $errors['general'] = 'An unexpected database error occurred.';
                $response['message'] = $errors['general'];
            }
        }
    } else {
         $errors['general'] = 'Database connection failed.';
         $response['message'] = $errors['general'];
    }
} else {
    $response['message'] = 'Validation failed. Please check the form.';
}

if ($is_ajax) {
    if (!empty($errors)) {
        $response['errors'] = $errors;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        unset($input['password'], $input['password_confirmation']);
        $_SESSION['old'] = $input;
    }
    
    header('Location: ../resident-account-list.php');
    exit;
}

?> 