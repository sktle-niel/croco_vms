<?php
session_start();

// Determine where to redirect based on logout type
$redirect = isset($_GET['type']) && $_GET['type'] === 'admin' 
    ? '../auth/admin.php' 
    : '../auth/form.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: ' . $redirect);
exit;
?>
