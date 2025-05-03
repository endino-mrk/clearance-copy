<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect logged-in users away
    exit;
}

$errors = [];
$input = [];

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    
    // --- Basic Validation ---
    if (empty(trim($input['first_name']))) { $errors['first_name'] = 'First name is required.'; }
    if (empty(trim($input['last_name']))) { $errors['last_name'] = 'Last name is required.'; }
    if (empty($input['email'])) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }
    if (empty($input['password'])) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($input['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }
    if ($input['password'] !== $input['password_confirmation']) {
        $errors['password_confirmation'] = 'Passwords do not match.';
    }
    if (!isset($input['terms'])) {
        $errors['terms'] = 'You must agree to the terms.';
    }

    // --- Check if email exists (only if email validation passed so far) ---
    if (empty($errors['email'])) {
        $pdo_check = connect_db();
        if ($pdo_check) {
            try {
                $sql_check = "SELECT COUNT(*) as count FROM users WHERE email = :email";
                $stmt_check = $pdo_check->prepare($sql_check);
                $stmt_check->bindParam(':email', $input['email']);
                $stmt_check->execute();
                $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
                
                if ($result && $result['count'] > 0) {
                    $errors['email'] = 'This email address is already registered.';
                }
            } catch (PDOException $e) {
                 error_log("Register Email Check Error: " . $e->getMessage());
                 $errors['general'] = 'Error checking email uniqueness: ' . $e->getMessage();
            }
        } else {
             $errors['general'] = 'Database connection failed during email check.';
        }
    }

    // --- Process Data if No Errors ---
    if (empty($errors)) {
        $pdo = connect_db(); // Reconnect or use existing $pdo_check if persistent
        if ($pdo) {
            try {
                // Hash the password
                $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

                // Default empty values to empty strings instead of NULL to avoid NULL constraint issues
                $middleName = !empty(trim($input['middle_name'] ?? '')) ? trim($input['middle_name']) : '';
                $phoneNumber = !empty(trim($input['phone_number'] ?? '')) ? trim($input['phone_number']) : '';
                
                // Use a simpler INSERT query that matches the table structure we saw in test.php
                $sql = "INSERT INTO users (first_name, last_name, middle_name, email, phone_number, password, active, created_at, updated_at) 
                        VALUES (:first_name, :last_name, :middle_name, :email, :phone_number, :password, :active, NOW(), NOW())";
                
                $stmt = $pdo->prepare($sql);
                
                // Bind parameters
                $stmt->bindParam(':first_name', $input['first_name']);
                $stmt->bindParam(':last_name', $input['last_name']);
                $stmt->bindParam(':middle_name', $middleName);
                $stmt->bindParam(':email', $input['email']);
                $stmt->bindParam(':phone_number', $phoneNumber);
                $stmt->bindParam(':password', $hashedPassword);
                
                // Set active to 1 (true) for new users
                $active = 1;
                $stmt->bindParam(':active', $active);
                
                if ($stmt->execute()) {
                    // Set success message and redirect to login page
                    $_SESSION['success_message'] = 'Registration successful! You can now log in.';
                    header('Location: login.php'); // Redirect to login on success
                    exit;
                } else {
                     $errors['general'] = 'Registration failed. Please try again. Error: ' . implode(', ', $stmt->errorInfo());
                }

            } catch (PDOException $e) {
                error_log("Register Insert Error: " . $e->getMessage());
                 // Catch potential duplicate email again just in case check failed somehow
                 if ($e->getCode() == '23000') { 
                     $errors['email'] = 'This email address is already registered.';
                 } else {
                    $errors['general'] = 'Database error: ' . $e->getMessage();
                 }
            }
        } else {
             $errors['general'] = 'Database connection failed.';
        }
    }

    // --- Store Errors and Old Input on Failure ---
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        unset($input['password'], $input['password_confirmation'], $input['terms']); // Don't store sensitive/checkbox data
        $_SESSION['old_input'] = $input;
        header('Location: register.php'); // Redirect back to registration page
        exit;
    }
}

// --- Retrieve data from session for display ---
$session_errors = $_SESSION['errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_input']); // Clear after retrieving

// Helper functions are now loaded globally via bootstrap.php
/* // REMOVED LOCAL DEFINITIONS
function displayError($field, $errors) {
    if (isset($errors[$field])) {
        echo '<p class="text-red-500 text-xs mt-1 error-message">' . htmlspecialchars($errors[$field]) . '</p>';
    }
}
function old($field, $old_input) {
    return htmlspecialchars($old_input[$field] ?? '');
}
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dormitory Clearance System</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config={
            theme:{
                extend:{
                    colors:{
                        primary:'#4f46e5',
                        secondary:'#6366f1'
                    },
                    borderRadius:{
                        'none':'0px',
                        'sm':'4px',
                        DEFAULT:'8px',
                        'md':'12px',
                        'lg':'16px',
                        'xl':'20px',
                        '2xl':'24px',
                        '3xl':'32px',
                        'full':'9999px',
                        'button':'8px'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md overflow-hidden">
        <div class="py-10 px-8">
            <div class="flex justify-center mb-8">
                <h1 class="text-4xl font-['Pacifico'] text-primary">DormClear</h1>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">Create an account</h2>
            <p class="text-gray-600 text-center mb-8">Register to access the dormitory clearance system</p>
            
            <?php // Display general errors if any
                if (isset($session_errors['general'])) {
                    echo '<div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded mb-6 relative" role="alert">';
                    echo '<span class="block sm:inline">' . htmlspecialchars($session_errors['general']) . '</span>';
                    echo '</div>';
                }
            ?>
            
            <form action="register.php" method="POST">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="first_name" class="block text-gray-700 text-sm font-medium mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" required
                            class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary <?php echo isset($session_errors['first_name']) ? 'border-red-500' : '' ?>"
                            placeholder="First Name"
                            value="<?php echo old('first_name', $old_input); ?>">
                        <?php displayError('first_name', $session_errors); ?>
                    </div>
                    <div>
                        <label for="last_name" class="block text-gray-700 text-sm font-medium mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required
                            class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary <?php echo isset($session_errors['last_name']) ? 'border-red-500' : '' ?>"
                            placeholder="Last Name"
                            value="<?php echo old('last_name', $old_input); ?>">
                        <?php displayError('last_name', $session_errors); ?>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="middle_name" class="block text-gray-700 text-sm font-medium mb-2">Middle Name (Optional)</label>
                    <input type="text" id="middle_name" name="middle_name"
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Middle Name"
                        value="<?php echo old('middle_name', $old_input); ?>">
                    <?php displayError('middle_name', $session_errors); ?>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary <?php echo isset($session_errors['email']) ? 'border-red-500' : '' ?>"
                        placeholder="example@gmail.com"
                        value="<?php echo old('email', $old_input); ?>">
                    <?php displayError('email', $session_errors); ?>
                </div>
                
                <div class="mb-4">
                    <label for="phone_number" class="block text-gray-700 text-sm font-medium mb-2">Phone Number (Optional)</label>
                    <input type="tel" id="phone_number" name="phone_number"
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Enter your phone number"
                        value="<?php echo old('phone_number', $old_input); ?>">
                    <?php displayError('phone_number', $session_errors); ?>
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary <?php echo isset($session_errors['password']) ? 'border-red-500' : '' ?>"
                        placeholder="Choose a password">
                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters</p>
                    <?php displayError('password', $session_errors); ?>
                </div>
                
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary <?php echo isset($session_errors['password_confirmation']) ? 'border-red-500' : '' ?>"
                        placeholder="Confirm your password">
                    <?php displayError('password_confirmation', $session_errors); ?>
                </div>
                
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="terms" name="terms" 
                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded <?php echo isset($session_errors['terms']) ? 'border-red-500' : '' ?>">
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        I agree to the <a href="#" class="text-primary hover:underline">Terms and Conditions</a>
                    </label>
                    <?php displayError('terms', $session_errors); ?>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition">
                    Create Account
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600">Already have an account? 
                    <a href="login.php" class="text-primary hover:underline">Login here</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html> 