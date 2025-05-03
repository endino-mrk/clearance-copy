<?php
/**
 * Helper Functions for Dormitory Clearance System
 */

// Function to get initials from name
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    return strlen($initials) > 2 ? substr($initials, 0, 2) : $initials;
}

// Function to format date
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) {
        return '-';
    }
    return date($format, strtotime($date));
}

// Function to format currency
function formatCurrency($amount, $symbol = '₱') {
    return $symbol . number_format((float)$amount, 2, '.', ',');
}

// Function to get status class for residents
function getResidentStatusClass($status) {
    switch ($status) {
        case 'Active':
            return ['text-green-800', 'bg-green-100'];
        case 'Inactive':
            return ['text-red-800', 'bg-red-100'];
        case 'Moving Out':
            return ['text-yellow-800', 'bg-yellow-100'];
        default:
            return ['text-gray-800', 'bg-gray-100'];
    }
}

// Function to get status class for clearances
function getClearanceStatusClass($status) {
    switch ($status) {
        case 'Approved':
            return ['text-green-800', 'bg-green-100'];
        case 'Rejected':
            return ['text-red-800', 'bg-red-100'];
        case 'Pending':
            return ['text-yellow-800', 'bg-yellow-100'];
        default:
            return ['text-gray-800', 'bg-gray-100'];
    }
}

// Function to get status class for payments
function getPaymentStatusClass($status) {
    switch ($status) {
        case 'Paid':
            return ['text-green-800', 'bg-green-100'];
        case 'Partially Paid':
            return ['text-blue-800', 'bg-blue-100'];
        case 'Unpaid':
            return ['text-yellow-800', 'bg-yellow-100'];
        case 'Overdue':
            return ['text-red-800', 'bg-red-100'];
        default:
            return ['text-gray-800', 'bg-gray-100'];
    }
}

// Function to get completion status class
function getCompletionClass($status) {
    switch ($status) {
        case 'Complete':
            return ['text-green-800', 'bg-green-100'];
        case 'Incomplete':
            return ['text-red-800', 'bg-red-100'];
        case 'Pending':
            return ['text-yellow-800', 'bg-yellow-100'];
        case 'Approved':
            return ['text-green-800', 'bg-green-100'];
        default:
            return ['text-gray-800', 'bg-gray-100'];
    }
}

// Function to check if a page is active
function isPageActive($page) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage === $page;
}

// Set title for pages
function pageTitle($title = '') {
    global $pageTitle;
    if (!empty($title)) {
        $pageTitle = $title;
    }
    return isset($pageTitle) ? $pageTitle : 'Dormitory Clearance System';
}

// Get active class for navigation
function isActive($path) {
    $currentPath = $_SERVER['REQUEST_URI'];
    if (strpos($currentPath, $path) === 0) {
        return 'active';
    }
    return '';
}

// Get user data from session
function getUser() {
    if (isset($_SESSION['user_id'])) {
        // Get the user data from the database
        // This is a simplified example - you would typically query the database
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'User',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'user',
        ];
        return $user;
    }
    return null;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user has a specific role
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    // For simplicity, we're just checking the role from the session
    // In a real application, you'd verify against your database
    return $_SESSION['user_role'] === $role;
}

// Generate random string for temporary passwords, tokens, etc.
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Truncate text for display
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    return $text . '...';
}

// Get old input values 
function old($field) {
    return $_SESSION['old_input'][$field] ?? '';
}

// Check if a field has error
function hasError($field, $errors = null) {
    if ($errors === null) {
        $errors = $_SESSION['errors'] ?? [];
    }
    return isset($errors[$field]);
}

// Get error message for a field
function getError($field, $errors = null) {
    if ($errors === null) {
        $errors = $_SESSION['errors'] ?? [];
    }
    return $errors[$field] ?? '';
}

/**
 * Displays the error message paragraph for a specific field if it exists.
 *
 * @param string $field The name of the form field.
 * @param array $errors An array of errors, typically from $_SESSION['errors'].
 * @return void
 */
function displayError(string $field, array $errors): void
{
    if (isset($errors[$field])) {
        echo '<p class="text-red-500 text-xs mt-1 error-message">' . htmlspecialchars($errors[$field]) . '</p>';
    }
}

// Convert status to badge color
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'active':
        case 'approved':
        case 'paid':
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'pending':
        case 'processing':
            return 'bg-yellow-100 text-yellow-800';
        case 'inactive':
        case 'rejected':
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Redirect with flash message
function redirectWithMessage($url, $message, $type = 'success') {
    if ($type === 'success') {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['errors']['general'] = $message;
    }
    
    header("Location: $url");
    exit;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Check if request is AJAX
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Return JSON response
function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

?> 