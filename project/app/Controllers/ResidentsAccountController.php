<?php

namespace App\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Capsule\Manager as DB;

class ResidentsAccountController extends BaseController
{
    /**
     * Display a listing of the resident accounts.
     */
    public function index(): void 
    { 
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Get all users (residents)
        $resident_accounts = User::all();
        
        // Render the residents-account index view with the data
        $this->view('residents-account/index', [
            'resident_accounts' => $resident_accounts
        ]);
    }
    
    /**
     * Show the form for creating a new resident account.
     */
    public function create(): void 
    { 
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Render the create view
        $this->view('residents-account/create');
    }
    
    /**
     * Store a newly created resident account in the database.
     */
    public function store(): void 
    { 
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Validate input
        $errors = [];
        
        // Required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        // Email validation
        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }
        
        // Check if email is already in use
        if (!empty($_POST['email']) && User::where('email', $_POST['email'])->exists()) {
            $errors['email'] = 'Email is already in use';
        }
        
        // Password confirmation
        if (!empty($_POST['password']) && $_POST['password'] !== $_POST['password_confirmation']) {
            $errors['password_confirmation'] = 'Password confirmation does not match';
        }
        
        // If there are errors, redirect back with errors and input data
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /residents-account/create');
            exit;
        }
        
        // Create the new user
        try {
            $user = new User();
            $user->first_name = $_POST['first_name'];
            $user->last_name = $_POST['last_name'];
            $user->middle_name = $_POST['middle_name'] ?? null;
            $user->phone_number = $_POST['phone_number'] ?? null;
            $user->email = $_POST['email'];
            $user->password = $_POST['password']; // Model will hash this automatically
            $user->save();
            
            // Set success message
            $_SESSION['success'] = 'Resident account created successfully';
            
            // Redirect to index
            header('Location: /residents-account');
            exit;
        } catch (\Exception $e) {
            // Set error message
            $_SESSION['errors'] = ['general' => 'An error occurred while creating the resident account: ' . $e->getMessage()];
            $_SESSION['old_input'] = $_POST;
            header('Location: /residents-account/create');
            exit;
        }
    }
    
    /**
     * Display the specified resident account.
     */
    public function show(array $params): void 
    { 
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Get the user ID from the URL
        $id = $params['id'] ?? null;
        
        if (!$id) {
            $_SESSION['errors'] = ['general' => 'Resident ID is required'];
            header('Location: /residents-account');
            exit;
        }
        
        // Find the user
        $resident = User::find($id);
        
        if (!$resident) {
            $_SESSION['errors'] = ['general' => 'Resident not found'];
            header('Location: /residents-account');
            exit;
        }
        
        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
                  
        // If AJAX, return JSON data
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($resident);
            exit;
        }
        
        // Otherwise, render the show view with the resident data
        $this->view('residents-account/show', [
            'resident' => $resident
        ]);
    }
    
    /**
     * Show the form for editing the specified resident account.
     */
    public function edit(array $params): void 
    { 
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Get the user ID from the URL
        $id = $params['id'] ?? null;
        
        if (!$id) {
            $_SESSION['errors'] = ['general' => 'Resident ID is required'];
            header('Location: /residents-account');
            exit;
        }
        
        // Find the user
        $resident = User::find($id);
        
        if (!$resident) {
            $_SESSION['errors'] = ['general' => 'Resident not found'];
            header('Location: /residents-account');
            exit;
        }
        
        // Render the edit view with the resident data
        $this->view('residents-account/edit', [
            'resident' => $resident
        ]);
    }
    
    /**
     * Update the specified resident account in the database.
     */
    public function update(array $params): void 
    { 
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Get the user ID from the URL
        $id = $params['id'] ?? null;
        
        if (!$id) {
            $_SESSION['errors'] = ['general' => 'Resident ID is required'];
            header('Location: /residents-account');
            exit;
        }
        
        // Find the user
        $resident = User::find($id);
        
        if (!$resident) {
            $_SESSION['errors'] = ['general' => 'Resident not found'];
            header('Location: /residents-account');
            exit;
        }
        
        // Parse the input data - for PUT requests, we need to manually parse the input
        parse_str(file_get_contents('php://input'), $_PUT);
        
        // Validate input
        $errors = [];
        
        // Required fields
        $requiredFields = ['first_name', 'last_name', 'email'];
        foreach ($requiredFields as $field) {
            if (empty($_PUT[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        // Email validation
        if (!empty($_PUT['email']) && !filter_var($_PUT['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }
        
        // Check if email is already in use by another user
        if (!empty($_PUT['email']) && $_PUT['email'] !== $resident->email && 
            User::where('email', $_PUT['email'])->exists()) {
            $errors['email'] = 'Email is already in use';
        }
        
        // Password confirmation (only if password is provided)
        if (!empty($_PUT['password']) && $_PUT['password'] !== $_PUT['password_confirmation']) {
            $errors['password_confirmation'] = 'Password confirmation does not match';
        }
        
        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        // If there are errors
        if (!empty($errors)) {
            if ($isAjax) {
                // Return JSON response with errors
                header('Content-Type: application/json');
                echo json_encode(['errors' => $errors]);
                exit;
            } else {
                // Redirect back with errors and input data
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = $_PUT;
                header("Location: /residents-account/{$id}/edit");
                exit;
            }
        }
        
        // Update the user
        try {
            $resident->first_name = $_PUT['first_name'];
            $resident->last_name = $_PUT['last_name'];
            $resident->middle_name = $_PUT['middle_name'] ?? $resident->middle_name;
            $resident->phone_number = $_PUT['phone_number'] ?? $resident->phone_number;
            $resident->email = $_PUT['email'];
            
            // Only update password if provided
            if (!empty($_PUT['password'])) {
                $resident->password = $_PUT['password']; // Model will hash this automatically
            }
            
            $resident->save();
            
            // Set success message
            $_SESSION['success'] = 'Resident account updated successfully';
            
            if ($isAjax) {
                // Return JSON success response
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Resident account updated successfully']);
                exit;
            } else {
                // Redirect to index
                header('Location: /residents-account');
                exit;
            }
        } catch (\Exception $e) {
            // Set error message
            $errorMessage = 'An error occurred while updating the resident account: ' . $e->getMessage();
            
            if ($isAjax) {
                // Return JSON error response
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $errorMessage]);
                exit;
            } else {
                $_SESSION['errors'] = ['general' => $errorMessage];
                $_SESSION['old_input'] = $_PUT;
                header("Location: /residents-account/{$id}/edit");
                exit;
            }
        }
    }
    
    /**
     * Remove the specified resident account from the database.
     */
    public function destroy(array $params): void 
    { 
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Get the user ID from the URL
        $id = $params['id'] ?? null;
        
        if (!$id) {
            $_SESSION['errors'] = ['general' => 'Resident ID is required'];
            header('Location: /residents-account');
            exit;
        }
        
        // Find the user
        $resident = User::find($id);
        
        if (!$resident) {
            $_SESSION['errors'] = ['general' => 'Resident not found'];
            header('Location: /residents-account');
            exit;
        }
        
        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        // Delete the user
        try {
            $resident->delete();
            
            // Set success message
            $_SESSION['success'] = 'Resident account deleted successfully';
            
            if ($isAjax) {
                // Return JSON success response
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Resident account deleted successfully']);
                exit;
            }
        } catch (\Exception $e) {
            // Set error message
            $errorMessage = 'An error occurred while deleting the resident account: ' . $e->getMessage();
            
            if ($isAjax) {
                // Return JSON error response
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $errorMessage]);
                exit;
            } else {
                $_SESSION['errors'] = ['general' => $errorMessage];
            }
        }
        
        // Redirect to index
        header('Location: /residents-account');
        exit;
    }
}