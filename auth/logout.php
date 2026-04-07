<?php
// Start session
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to homepage
header('Location: /student2student/auth/login.php');
exit;
?>