<?php
// project/public/login.php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/role_auth_check.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    redirectBasedOnRole();
    exit;
}

$error = null;
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Both email and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        $pdo = connect_db();
        if ($pdo) {
            try {
                $sql = "SELECT * FROM users WHERE email = :email AND active = 1 LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && $password === $user['password']) {
                    // Password is correct, set session variables
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_type'] = $user['type'];
                    
                    // Check if user is also a resident to get resident_id
                    if ($user['type'] === 'Resident' || $user['type'] === 'Treasurer') {
                        $residentSql = "SELECT resident_id FROM residents WHERE user_id = :user_id AND active = 1";
                        $residentStmt = $pdo->prepare($residentSql);
                        $residentStmt->bindParam(':user_id', $user['user_id']);
                        $residentStmt->execute();
                        $resident = $residentStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($resident) {
                            $_SESSION['resident_id'] = $resident['resident_id'];
                        }
                    }
                    
                    // Redirect based on role
                    redirectBasedOnRole();
                } else {
                    $error = 'Invalid email or password.';
                }
            } catch (PDOException $e) {
                error_log("Login Error: " . $e->getMessage());
                $error = 'An error occurred. Please try again.';
            }
        } else {
            $error = 'Database connection failed.';
        }
    
        
    // Store error and email for repopulating form
    $_SESSION['error'] = $error;
    $_SESSION['old_email'] = $email; 
    // Redirect back to the login page itself to show errors/repopulate
    header('Location: login.php');
    exit;
}
}

// Function to redirect based on user role
function redirectBasedOnRole() {
    $userType = $_SESSION['user_type'] ?? null;
    
    switch ($userType) {
        case 'Manager':
            header('Location: index.php');
            break;
        case 'Resident':
            header('Location: role-admin/clearance-status/resident-clearance.php');
            break;
        case 'Treasurer':
            header('Location: role-admin/clearance-status/resident-clearance.php');
            break;
        default:
            header('Location: login.php');
            break;
    }
    // exit;
}

// Retrieve error/old email from session if redirected
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['old_email'])) {
    $email = $_SESSION['old_email'];
    unset($_SESSION['old_email']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dormitory Clearance System</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md overflow-hidden">
        <div class="py-10 px-8">
            <div class="flex justify-center mb-8">
                <h1 class="text-4xl font-['Pacifico'] text-primary">DormClear</h1>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">Welcome back</h2>
            <p class="text-gray-600 text-center mb-8">Please enter your credentials to log in</p>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded mb-6 relative" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <div class="mb-5">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Enter your email address"
                        value="<?php echo htmlspecialchars($email); ?>">
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-gray-700 text-sm font-medium">Password</label>
                        <a href="#" class="text-sm text-primary hover:text-primary-dark">Forgot password?</a>
                    </div>
                    <input type="password" id="password" name="password" required
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Enter your password">
                </div>
                
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="remember_me" name="remember_me" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition">
                    Log in
                </button>
            </form>
            
        </div>
    </div>
</body>
</html>