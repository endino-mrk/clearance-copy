<?php

namespace App\Controllers;

use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB; // Optional: If using DB facade directly
use Illuminate\Support\Facades\Redirect;

class AuthController extends BaseController
{
    /**
     * Helper function to render a PHP view file.
     * (Consider moving this to a BaseController later)
     */
    protected function view(string $view, array $data = []): void
    {
        $viewFile = BASE_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Error: View file not found at {$viewFile}";
            error_log("View file not found: {$viewFile}");
            return;
        }
        extract($data);
        ob_start();
        try {
            include $viewFile;
        } catch (\Throwable $e) {
            ob_end_clean();
            http_response_code(500);
            echo "Error rendering view: " . $e->getMessage();
            error_log("View rendering error in {$viewFile}: " . $e->getMessage());
            return;
        }
        echo ob_get_clean();
    }

    /**
     * Show the login form.
     */
    public function showLoginForm(): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /admin');
            exit;
        }
        
        $this->view('auth/login');
    }

    /**
     * Process the login attempt.
     */
    public function login(): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Validate required fields
        if (empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION['error'] = 'Email and password are required.';
            header('Location: /login');
            exit;
        }
        
        // Get email and password from the form
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Try to find the user by email
        $user = User::where('email', $email)->first();
        
        // If user not found or password doesn't match
        if (!$user || !password_verify($password, $user->password)) {
            $_SESSION['error'] = 'Invalid email or password.';
            $_SESSION['old_email'] = $email; // Keep the email for form repopulation
            header('Location: /login');
            exit;
        }
        
        // User authenticated successfully, set session data
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->first_name . ' ' . $user->last_name;
        
        // Redirect to dashboard
        header('Location: /admin');
        exit;
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm(): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /admin');
            exit;
        }
        
        $this->view('auth/register');
    }

    /**
     * Process the registration attempt.
     */
    public function register(): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Validate required fields
        if (empty($_POST['first_name']) || empty($_POST['last_name']) || 
            empty($_POST['email']) || empty($_POST['password']) || 
            empty($_POST['password_confirmation'])) {
            $_SESSION['error'] = 'Please fill in all required fields.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /register');
            exit;
        }
        
        // Check if passwords match
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            $_SESSION['error'] = 'Passwords do not match.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /register');
            exit;
        }
        
        // Check if email is already in use
        $existingUser = User::where('email', $_POST['email'])->first();
        if ($existingUser) {
            $_SESSION['error'] = 'Email is already in use.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /register');
            exit;
        }
        
        // Create new user
        try {
            $user = new User();
            $user->first_name = $_POST['first_name'];
            $user->last_name = $_POST['last_name'];
            $user->email = $_POST['email'];
            $user->password = $_POST['password']; // Will be hashed in the model
            
            // Optional fields
            if (!empty($_POST['middle_name'])) {
                $user->middle_name = $_POST['middle_name'];
            }
            
            if (!empty($_POST['phone_number'])) {
                $user->phone_number = $_POST['phone_number'];
            }
            
            $user->save();
            
            // Log the user in
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_name'] = $user->first_name . ' ' . $user->last_name;
            
            // Redirect to dashboard
            header('Location: /admin');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Registration failed: ' . $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            header('Location: /register');
            exit;
        }
    }

    /**
     * Log the user out.
     */
    public function logout(): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset all session values
        $_SESSION = [];

        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header('Location: /login');
        exit;
    }
} 