<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // your database username
define('DB_PASS', ''); // your database password
define('DB_NAME', 'Shopease');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Function to handle errors
function error_redirect($message, $page = 'index.php') {
    $_SESSION['error'] = $message;
    header("Location: $page");
    exit;
}

// Function to handle success messages
function success_redirect($message, $page = 'index.php') {
    $_SESSION['success'] = $message;
    header("Location: $page");
    exit;
}
?>