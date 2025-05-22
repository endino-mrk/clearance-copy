<?php 

/**
 * Role-based authentication and authorization middleware
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        // Clear any corrupted session data
        session_destroy();
        session_start();
        header('Location: /clearance/project/public/login.php');
        exit;
    }
}

/**
 * Check if user has required role(s)
 * @param array|string $allowedRoles Single role or array of allowed roles
 */
function requireRole($allowedRoles) {
    requireLogin();
    
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    
    $userType = $_SESSION['user_type'] ?? null;
    
    if (!in_array($userType, $allowedRoles)) {
        // Redirect to appropriate page based on user's actual role
        redirectToUserDashboard();
    }
}

/**
 * Redirect user to their appropriate dashboard
 */
function redirectToUserDashboard() {
    $userType = $_SESSION['user_type'] ?? null;
    
    switch ($userType) {
        case 'Manager':
        header('Location: /clearance/project/public/role-admin/index.php');
            break;
        case 'Resident':
        case 'Treasurer':
            header('Location: /clearance/project/public/role-admin/clearance-status/resident-clearance.php');
            break;
        default:
            header('Location: /clearance/project/public/login.php');
            break;
    }
    exit;
}

/**
 * Check if current user is Manager
 */
function isManager() {
    return ($_SESSION['user_type'] ?? null) === 'Manager';
}

/**
 * Check if current user is Treasurer
 */
function isTreasurer() {
    return ($_SESSION['user_type'] ?? null) === 'Treasurer';
}

/**
 * Check if current user is Resident
 */
function isResident() {
    return ($_SESSION['user_type'] ?? null) === 'Resident';
}

/**
 * Check if user has access to financial features
 */
function hasFinancialAccess() {
    $userType = $_SESSION['user_type'] ?? null;
    return in_array($userType, ['Manager', 'Treasurer']);
}

/**
 * Check if user has admin access
 */
function hasAdminAccess() {
    return isManager();
}

/**
 * Display unauthorized message and redirect
 */
function showUnauthorized() {
    $_SESSION['error'] = 'You do not have permission to access this page.';
    redirectToUserDashboard();
}

/**
 * Get user's resident ID if they are a resident
 */
function getResidentId() {
    if (isResident() || isTreasurer()) {
        return $_SESSION['resident_id'] ?? null;
    }
    return null;
}

/**
 * Check if user can access specific resident data
 * @param int $residentId The resident ID to check access for
 */
function canAccessResidentData($residentId) {
    if (isManager()) {
        return true; // Managers can access all resident data
    }
    
    if (isResident() || isTreasurer()) {
        return getResidentId() == $residentId;
    }
    
    return false;
}







?>