<?php
require_once __DIR__ . '/role_auth_check.php';

// Basic authentication check - just require login
requireLogin();

// For role-specific pages, use requireRole() function in individual pages
?>
