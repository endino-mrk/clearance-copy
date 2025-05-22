<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/resident-process.php'; 

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../resident-account-list.php');
    exit;
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

// Add check for unique email in the database
if (empty($input['password'])) {
    $errors['password'] = 'Password is required.';
} elseif (strlen($input['password']) < 8) { // Example: Minimum length
    $errors['password'] = 'Password must be at least 8 characters long.';
}
if ($input['password'] !== $input['password_confirmation']) {
    $errors['password_confirmation'] = 'Passwords do not match.';
}

// Validate Student ID
if (empty(trim($input['student_id']))) {
    $errors['student_id'] = 'Student ID is required.';
} else {
    // Check if student ID is already in use
    $pdo = connect_db();
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT student_id FROM residents WHERE student_id = :student_id");
        $stmt->bindParam(':student_id', $input['student_id']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $errors['student_id'] = 'This Student ID is already registered.';
        }
    }
}

// Validate Room selection
if (empty($input['room_id'])) {
    $errors['room_id'] = 'Room assignment is required.';
} else {
    // Verify room exists 
    $pdo = connect_db();
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT room_id FROM rooms WHERE room_id = :room_id");
        $stmt->bindParam(':room_id', $input['room_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            $errors['room_id'] = 'Selected room does not exist.';
        }
    }
}

// If there are validation errors, store them in session and redirect back
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    // Remove sensitive data before storing in session
    unset($input['password'], $input['password_confirmation']);
    $_SESSION['old'] = $input;
    
    header('Location: ../resident-account-list.php');
    exit;
}

// --- Process Data ---
$pdo = connect_db();
if (!$pdo) {
    $_SESSION['errors']['general'] = 'Database connection failed.';
    header('Location: ../resident-account-list.php');
    exit;
}

try {
    // Store password without hashing
    $plainPassword = $input['password'];

    $sql = "INSERT INTO users (first_name, last_name, middle_name, email, phone_number, password, active, type, created_at, updated_at) 
                VALUES (:first_name, :last_name, :middle_name, :email, :phone_number, :password, :active, :type, NOW(), NOW())";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $firstName = trim($input['first_name']);
    $lastName = trim($input['last_name']);
    $email = trim($input['email']);
    $middleName = !empty(trim($input['middle_name'])) ? trim($input['middle_name']) : null;
    $phoneNumber = !empty(trim($input['phone_number'])) ? trim($input['phone_number']) : null;
    $type = $input['user_role'];
    
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':last_name', $lastName);
    $stmt->bindParam(':middle_name', $middleName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone_number', $phoneNumber);
    $stmt->bindParam(':password', $plainPassword);
    $stmt->bindParam(':type', $type);

    $active = 1;
    $stmt->bindParam(':active', $active);
    
    if ($stmt->execute()) {
        $userId = $pdo->lastInsertId();
        $studentId = trim($input['student_id']);
        $roomId = $input['room_id'];
        
        // Process new resident account
        processNewResident($userId, $studentId, $roomId);
        
        $_SESSION['success'] = 'Resident created successfully!';
        header('Location: ../resident-account-list.php');
        exit;
    } else {
        $_SESSION['errors']['general'] = 'Failed to create resident. Please try again.';
        header('Location: ../resident-account-list.php');
        exit;
    }

} catch (PDOException $e) {
    error_log("Create Resident Error: " . $e->getMessage());
    
    if ($e->getCode() == '23000') { 
        $_SESSION['errors']['email'] = 'This email address is already registered.';
        $_SESSION['errors']['general'] = 'This email address is already registered.';
    } else {
        $_SESSION['errors']['general'] = 'An unexpected database error occurred: ' . $e->getMessage();
    }
    
    // Remove sensitive data before storing in session
    unset($input['password'], $input['password_confirmation']);
    $_SESSION['old'] = $input;
    
    header('Location: ../resident-account-list.php');
    exit;
}
?>