<?php
// public/index.php - Landing page with proper routing
require_once __DIR__ . '/../config/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login
    header('Location: /clearance/project/public/login.php');
    exit;
}

// User is logged in, redirect based on their role
$userType = $_SESSION['user_type'] ?? null;

switch ($userType) {
    case 'Manager':
        // Redirect to manager dashboard
        header('Location: /clearance/project/public/role-admin/index.php');
        break;
    case 'Resident':
    case 'Treasurer':
        // Redirect to resident/treasurer dashboard
        header('Location: /clearance/project/public/role-admin/clearance-status/resident-clearance.php');
        break;
    default:
        // Unknown role, redirect to login
        session_destroy();
        header('Location: /clearance/project/public/login.php');
        break;
}
exit;
?>