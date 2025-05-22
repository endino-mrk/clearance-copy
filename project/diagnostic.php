<?php
// Diagnostic script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<h1>Diagnostic Page</h1>';
echo '<p>PHP Version: ' . phpversion() . '</p>';

// Check if session works
session_start();
echo '<h2>Session Test</h2>';
$_SESSION['test'] = 'Session is working';
echo '<p>Session test value: ' . ($_SESSION['test'] ?? 'Not set') . '</p>';

// Try to set a test user
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';
echo '<p>Set temporary test user for auth check: ID=' . $_SESSION['user_id'] . ', Name=' . $_SESSION['user_name'] . '</p>';

// File system paths
echo '<h2>File System Paths</h2>';
echo '<p>__DIR__: ' . __DIR__ . '</p>';
echo '<p>Document Root: ' . $_SERVER['DOCUMENT_ROOT'] . '</p>';

// URL info
echo '<h2>URL Information</h2>';
echo '<p>PHP_SELF: ' . $_SERVER['PHP_SELF'] . '</p>';
echo '<p>REQUEST_URI: ' . $_SERVER['REQUEST_URI'] . '</p>';
echo '<p>HTTP_HOST: ' . ($_SERVER['HTTP_HOST'] ?? 'Not set') . '</p>';

// Create a test link to the fines page
echo '<h2>Test Links</h2>';
echo '<p><a href="project/public/fines/fine-list.php">Go to Fines Page</a></p>';
echo '<p><a href="project/public/fines/test.php">Go to Fines Test Page</a></p>';

?> 