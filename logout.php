<?php
require_once 'config.php';

// Clear user session
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);

// Optional: destroy the session
session_destroy();

// Redirect to home page
$_SESSION['success'] = 'You have been logged out successfully.';
header('Location: index.php');
exit;
?>